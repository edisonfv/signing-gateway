<?php

namespace App\Signing\Ledger;

use App\Models\ClientNotification;
use App\Models\IssuanceLedgerEntry;
use App\Models\SignatureRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Registro de emisiones: una entrada por certificado que emitió el proveedor, encadenada por hash
 * y con correlativo sin huecos. Es lo que el proveedor cobra y lo que la plataforma factura.
 */
class IssuanceLedger
{
    /**
     * Registra la emisión de la solicitud (idempotente: una sola entrada por solicitud) y avisa al
     * cliente con `certificate.issued`. Debe llamarse dentro de la transacción que cambia el estado.
     * La carga histórica no avisa: esas emisiones ya se notificaron con su propio evento.
     */
    public function record(SignatureRequest $request, string $source, bool $notify = true): IssuanceLedgerEntry
    {
        return DB::transaction(function () use ($request, $source, $notify) {
            // Serializa las inserciones: el correlativo y el hash anterior no admiten carreras.
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("SELECT pg_advisory_xact_lock(hashtext('issuance_ledger'))");
            }

            $existing = IssuanceLedgerEntry::query()->where('signature_request_id', $request->getKey())->first();

            if ($existing !== null) {
                return $existing;
            }

            $previous = IssuanceLedgerEntry::query()->orderByDesc('sequence')->first();
            $account = $request->providerAccount;

            $entry = new IssuanceLedgerEntry([
                'sequence' => ($previous->sequence ?? 0) + 1,
                'signature_request_id' => $request->getKey(),
                'tenant_id' => $request->tenant_id,
                'api_client_id' => $request->api_client_id,
                'provider_account_id' => $account->getKey(),
                'provider' => $account->provider->value,
                'environment' => $account->environment->value,
                'product' => $request->product,
                'flow' => $request->flow,
                'application_type' => $request->application_type,
                'period' => $request->period,
                'provider_reference' => $request->provider_session_id ?? $request->provider_request_id,
                'serial_number' => $request->vaultEntry?->serial_number,
                'identification_hash' => $request->identification_hash,
                'issued_at' => CarbonImmutable::parse($request->issued_at ?? now())->startOfSecond(),
                'source' => $source,
                'previous_hash' => $previous?->hash,
                'created_at' => now(),
            ]);
            $entry->hash = $entry->computeHash();
            $entry->save();

            if ($notify) {
                ClientNotification::queue($request->apiClient, 'certificate.issued', [
                    'signature_session_id' => $request->getKey(),
                    'sequence' => $entry->sequence,
                    'product' => $entry->product->value,
                    'period' => $entry->period->value,
                    'application_type' => $entry->application_type->value,
                    'issued_at' => $entry->issued_at->toIso8601String(),
                ], $request);
            }

            return $entry;
        });
    }

    /**
     * Recorre el registro y devuelve el primer correlativo con un problema (hueco, hash alterado o
     * cadena rota), o null si está íntegro.
     *
     * @return array{sequence: int, reason: string}|null
     */
    public function verify(): ?array
    {
        $expectedSequence = 1;
        $previousHash = null;

        foreach (IssuanceLedgerEntry::query()->lazyById(500, 'sequence') as $entry) {
            $problem = match (true) {
                $entry->sequence !== $expectedSequence => 'falta el correlativo '.$expectedSequence,
                $entry->previous_hash !== $previousHash => 'la cadena no coincide con la entrada anterior',
                ! hash_equals($entry->computeHash(), $entry->hash) => 'el contenido no coincide con su hash',
                default => null,
            };

            if ($problem !== null) {
                return ['sequence' => $entry->sequence, 'reason' => $problem];
            }

            $expectedSequence++;
            $previousHash = $entry->hash;
        }

        return null;
    }
}
