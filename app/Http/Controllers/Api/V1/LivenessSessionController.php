<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\SignatureSessionResource;
use App\Jobs\RequestLivenessSession;
use App\Models\SignatureRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class LivenessSessionController extends Controller
{
    /**
     * Solicitar sesión de prueba de vida
     *
     * Disponible cuando la solicitud espera al titular (`AWAITING_APPLICANT`) o tras un intento
     * fallido (`FAILED_RETRYABLE`). Responde 202; cuando esté lista llega la notificación
     * `session.liveness_ready` y las credenciales se retiran con GET …/liveness-credentials.
     */
    public function store(SignatureRequest $signatureSession): JsonResponse
    {
        Gate::authorize('update', $signatureSession);

        abort_unless($signatureSession->product->equals(ProductType::headless()), 409, 'La prueba de vida solo aplica al Flujo por API.');

        abort_unless(
            $signatureSession->status->equals(RequestStatus::awaitingApplicant(), RequestStatus::failedRetryable()),
            409,
            'La solicitud no está esperando una prueba de vida.',
        );

        RequestLivenessSession::dispatch($signatureSession);

        return SignatureSessionResource::make($signatureSession->refresh())->response()->setStatusCode(202);
    }
}
