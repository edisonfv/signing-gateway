<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\Provider;
use App\Http\Controllers\Controller;
use App\Jobs\ReconcileSignatureSession;
use App\Models\AuditLog;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\Drivers\FirmaSegura\Enums\CloudEventType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Eventos CloudEvents del Flujo Simplificado (https://docs.firmaseguraec.com/eventos-webhook-2069473m0).
 *
 * FirmaSegura no firma sus webhooks, así que su contenido nunca cambia un estado: el evento
 * solo adelanta la reconciliación de esa sesión, que consulta el tracking del proveedor.
 * Responde 202 aunque la sesión no exista, para no revelar cuáles existen.
 */
class FirmaSeguraWebhookController extends Controller
{
    public function __invoke(Request $request, ProviderAccount $providerAccount, string $token): JsonResponse
    {
        abort_unless(
            $providerAccount->provider->equals(Provider::firmaSegura()) && hash_equals($providerAccount->webhookToken(), $token),
            404,
        );

        $id = $request->input('id');
        $type = $request->input('type');
        $sessionId = $request->input('subject');

        if (! is_string($id) || ! is_string($sessionId) || ! Str::isUuid($sessionId)
            || ! in_array($type, CloudEventType::toValues(), true)) {
            return response()->json(['message' => 'Evento no reconocido.'], 422);
        }

        // Los reintentos del proveedor repiten el id: se atiende una vez.
        if (! Cache::add("webhooks:firmasegura:{$id}", true, now()->addDay())) {
            return response()->json(['message' => 'Evento ya recibido.'], 202);
        }

        $signatureRequest = SignatureRequest::query()
            ->whereBelongsTo($providerAccount)
            ->where('provider_session_id', $sessionId)
            ->first();

        if ($signatureRequest !== null && ! $signatureRequest->status->isTerminal()) {
            AuditLog::record('webhook.received', subject: $signatureRequest, context: ['type' => $type, 'id' => $id], ip: $request->ip());
            ReconcileSignatureSession::dispatch($signatureRequest, 'webhook');
        }

        return response()->json(['message' => 'Evento recibido.'], 202);
    }
}
