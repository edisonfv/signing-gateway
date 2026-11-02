<?php

namespace App\Signing;

use App\Models\SignatureRequest;
use Illuminate\Support\Facades\File;

/**
 * Decide si una solicitud puede seguir llamando al proveedor. Se consulta justo antes de cada
 * llamada (en los jobs), no solo al crear la solicitud: suspender, bloquear, revocar o detener
 * la emisión frena también lo que ya estaba en cola.
 *
 * El freno global es un archivo en storage (como el modo mantenimiento): sobrevive a cache:clear
 * y lo ven todos los workers del servidor.
 */
class EmissionGuard
{
    public function isHalted(): bool
    {
        return File::exists($this->haltFile());
    }

    /**
     * @return array{reason: string, at: string}|null
     */
    public function haltDetails(): ?array
    {
        return $this->isHalted() ? json_decode(File::get($this->haltFile()), true) : null;
    }

    public function halt(string $reason): void
    {
        File::put($this->haltFile(), json_encode(['reason' => $reason, 'at' => now()->toIso8601String()], JSON_THROW_ON_ERROR));
    }

    public function resume(): void
    {
        File::delete($this->haltFile());
    }

    /**
     * Motivo por el que la solicitud no puede continuar, o null si puede.
     */
    public function blockReason(SignatureRequest $request): ?string
    {
        $tenant = $request->tenant;
        $client = $request->apiClient;

        return match (true) {
            $this->isHalted() => 'La emisión está detenida por el operador del gateway.',
            $tenant->isBlocked() => 'El afiliado está bloqueado.',
            $tenant->isSuspended() => 'El afiliado está suspendido.',
            $client->revoked_at !== null => 'La credencial que creó la solicitud fue revocada.',
            default => null,
        };
    }

    private function haltFile(): string
    {
        // Configurable solo para que las pruebas en paralelo no compartan el freno.
        return config('gateway.emission_halt_file') ?? storage_path('framework/emission-halted');
    }
}
