<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLivenessResultRequest;
use App\Http\Resources\SignatureSessionResource;
use App\Jobs\IssueCertificate;
use App\Jobs\SubmitFaceLiveness;
use App\Models\SignatureRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class LivenessResultController extends Controller
{
    /**
     * Enviar resultado de prueba de vida
     *
     * Cuando el SDK del navegador termina, el SaaS envía el `liveness_session_id`. El gateway
     * valida la biometría con el proveedor y, si coincide, emite el certificado. Responde 202;
     * el resultado llega como `certificate.ready`, `session.liveness_failed` o `session.rejected`.
     */
    public function store(StoreLivenessResultRequest $request, SignatureRequest $signatureSession): JsonResponse
    {
        Gate::authorize('update', $signatureSession);

        abort_unless($signatureSession->product->equals(ProductType::headless()), 409, 'La prueba de vida solo aplica al Flujo por API.');

        abort_unless(
            $signatureSession->status->equals(RequestStatus::awaitingApplicant()),
            409,
            'La solicitud no está esperando el resultado de una prueba de vida.',
        );

        DB::transaction(function () use ($request, $signatureSession) {
            $signatureSession->transitionTo(RequestStatus::validating(), 'api');

            Bus::chain([
                new SubmitFaceLiveness($signatureSession, $request->validated('liveness_session_id')),
                new IssueCertificate($signatureSession),
            ])->dispatch();
        });

        return SignatureSessionResource::make($signatureSession->refresh())->response()->setStatusCode(202);
    }
}
