<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Security\SealedBox;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class CertificateController extends Controller
{
    /**
     * Retirar certificado
     *
     * Entrega **una sola vez** el p12 y su contraseña, sellados (libsodium sealed box) con la llave
     * pública de entrega de la credencial: solo el SaaS puede abrirlos con su llave privada.
     * Después de la entrega el gateway borra el material de su bóveda. Una segunda llamada responde 410.
     */
    public function show(Request $request, SignatureRequest $signatureSession): JsonResponse
    {
        Gate::authorize('view', $signatureSession);

        /** @var ApiClient $client */
        $client = $request->user();

        abort_if(blank($client->delivery_public_key), 409, 'La credencial no tiene llave pública de entrega configurada.');
        abort_if($signatureSession->status->equals(RequestStatus::delivered()), 410, 'El certificado ya fue entregado.');
        abort_unless($signatureSession->status->equals(RequestStatus::certificateStored()), 409, 'El certificado aún no está disponible.');

        $sealed = DB::transaction(function () use ($signatureSession, $client) {
            $entry = $signatureSession->vaultEntry()->lockForUpdate()->firstOrFail();

            abort_if($entry->isPurged() || $entry->delivered_at !== null, 410, 'El certificado ya fue entregado.');

            $sealed = SealedBox::seal(json_encode([
                'certificate_base64' => $entry->certificate,
                'password' => $entry->password,
                'serial_number' => $entry->serial_number,
                'sha256' => $entry->certificate_sha256,
            ], JSON_THROW_ON_ERROR), $client->delivery_public_key);

            $entry->forceFill(['delivered_at' => now()])->save();
            $entry->purge();

            $signatureSession->forceFill(['delivered_at' => now()])->save();
            $signatureSession->transitionTo(RequestStatus::delivered(), 'api');

            AuditLog::record('certificate.delivered', $client, $signatureSession);

            return $sealed;
        });

        return response()
            ->json(['data' => ['sealed' => $sealed, 'algorithm' => 'libsodium crypto_box_seal (X25519 + XSalsa20-Poly1305)']])
            ->header('Cache-Control', 'no-store');
    }
}
