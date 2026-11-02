<?php

namespace App\Http\Controllers\Api\V1\Platform;

use App\Enums\Provider;
use App\Http\Controllers\Controller;
use App\Jobs\ReconcileSignatureSession;
use App\Models\AuditLog;
use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\Drivers\FirmaSegura\Enums\CloudEventType;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Group('Plataforma', 'Solo para la credencial de servicio de la plataforma (ability platform.service).', weight: 11)]
class ProviderEventController extends Controller
{
    /**
     * Reenviar un aviso del proveedor
     *
     * El proveedor avisa en la ruta pública de la plataforma (callbackUrl) y la plataforma reenvía
     * aquí, por el túnel, el cuerpo tal cual y los mismos `{provider}/{account}/{token}` de la ruta,
     * sin interpretarlo. Así el gateway no se expone a internet.
     *
     * El aviso nunca cambia un estado: solo adelanta la reconciliación de esa sesión, que consulta
     * al proveedor (FirmaSegura no firma sus webhooks). Responde 202 aunque la sesión no exista,
     * para no revelar cuáles existen; 404 si la cuenta o el token no coinciden.
     */
    public function store(Request $request, string $provider, ProviderAccount $providerAccount, string $token): JsonResponse
    {
        abort_unless(
            $providerAccount->provider->value === $provider && hash_equals($providerAccount->webhookToken(), $token),
            404,
        );

        return match ($providerAccount->provider->value) {
            Provider::firmaSegura()->value => $this->firmaSegura($request, $providerAccount),
        };
    }

    /**
     * Eventos CloudEvents de FirmaSegura (https://docs.firmaseguraec.com/eventos-webhook-2069473m0).
     */
    private function firmaSegura(Request $request, ProviderAccount $account): JsonResponse
    {
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
            ->whereBelongsTo($account)
            ->where('provider_session_id', $sessionId)
            ->first();

        if ($signatureRequest !== null && ! $signatureRequest->status->isTerminal()) {
            AuditLog::record('webhook.received', subject: $signatureRequest, context: ['type' => $type, 'id' => $id, 'relayed' => true], ip: $request->ip());
            ReconcileSignatureSession::dispatch($signatureRequest, 'webhook');
        }

        return response()->json(['message' => 'Evento recibido.'], 202);
    }
}
