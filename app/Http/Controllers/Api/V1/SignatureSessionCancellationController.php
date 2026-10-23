<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\SignatureSessionResource;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Signing\SignatureProviderManager;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class SignatureSessionCancellationController extends Controller
{
    /**
     * Cancelar solicitud
     *
     * Solo antes de emitir el certificado. Si la sesión ya existe en el proveedor, también se cancela allá.
     */
    public function store(SignatureRequest $signatureSession, SignatureProviderManager $providers): SignatureSessionResource
    {
        Gate::authorize('update', $signatureSession);

        abort_unless($signatureSession->status->canTransitionTo(RequestStatus::cancelled()), 409, 'La solicitud ya no se puede cancelar.');

        if ($signatureSession->provider_session_id !== null) {
            try {
                $providers->apiFlowFor($signatureSession)->cancel($signatureSession);
            } catch (ProviderRequestException $exception) {
                abort($exception->isRetryable() ? 503 : 409, 'El proveedor no aceptó la cancelación: '.$exception->getMessage());
            }
        }

        $signatureSession->transitionTo(RequestStatus::cancelled(), 'api');
        AuditLog::record('signature_session.cancelled', request()->user(), $signatureSession);

        return SignatureSessionResource::make($signatureSession);
    }
}
