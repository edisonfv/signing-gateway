<?php

namespace App\Actions;

use App\Enums\RequestStatus;
use App\Enums\SignatureFlow;
use App\Exceptions\ProviderRequestException;
use App\Models\AuditLog;
use App\Models\ClientNotification;
use App\Models\SignatureRequest;
use App\Signing\SignatureProviderManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Cancela una solicitud antes de que se emita: en el proveedor (si ya tiene sesión) y en el gateway.
 * La usan el SaaS, el freno de los jobs y la suspensión de un afiliado.
 */
class CancelSignatureSession
{
    public function __construct(private SignatureProviderManager $providers) {}

    public function canCancel(SignatureRequest $request): bool
    {
        return $request->status->canTransitionTo(RequestStatus::cancelled())
            // Firmas Regulares no ofrece cancelación una vez registrada en el proveedor.
            && ! ($request->flow->equals(SignatureFlow::documentReview()) && $request->provider_request_id !== null);
    }

    /**
     * @throws ProviderRequestException si el proveedor no acepta la cancelación
     */
    public function __invoke(SignatureRequest $request, string $source, ?Model $actor = null, ?string $reason = null): void
    {
        if ($request->provider_session_id !== null && $request->flow->equals(SignatureFlow::livenessApi())) {
            $this->providers->apiFlowFor($request)->cancel($request);
        }

        $request->transitionTo(RequestStatus::cancelled(), $source, array_filter(['reason' => $reason]));
        AuditLog::record('signature_session.cancelled', $actor, $request, context: array_filter(['reason' => $reason, 'source' => $source]), ip: $actor ? null : $source);

        if ($source !== 'api') {
            ClientNotification::queue($request->apiClient, 'session.cancelled', [
                'signature_session_id' => $request->getKey(),
                'status' => $request->status->value,
                'reason' => $reason,
            ], $request);
        }
    }
}
