<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductType;
use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegularCorrectionRequest;
use App\Http\Resources\SignatureSessionResource;
use App\Models\AuditLog;
use App\Models\SignatureRequest;
use App\Signing\SignatureProviderManager;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Gate;

#[Group('Firmas Regulares', 'Validación por documentos a cargo de operadores del proveedor.', weight: 3)]
class RegularCorrectionController extends Controller
{
    /**
     * Corregir solicitud
     *
     * Cuando la solicitud está en `FAILED_RETRYABLE` (el proveedor la marcó con ERROR), reenvía el
     * código dactilar y/o las imágenes con error activo. `error.codes` de la solicitud indica qué
     * corregir: D02 (código dactilar), A01 (anverso), A02 (reverso), A03 (selfie). Otros errores los
     * corrigen los operadores del proveedor.
     */
    public function store(StoreRegularCorrectionRequest $request, SignatureRequest $signatureSession, SignatureProviderManager $providers): SignatureSessionResource
    {
        Gate::authorize('update', $signatureSession);

        abort_unless($signatureSession->product->equals(ProductType::regular()), 409, 'Las correcciones solo aplican a Firmas Regulares.');
        abort_unless($signatureSession->status->equals(RequestStatus::failedRetryable()), 409, 'La solicitud no tiene errores pendientes de corrección.');

        try {
            $providers->regularFlowFor($signatureSession)->correctRegularRequest(
                $signatureSession,
                $request->validated('finger_code'),
                $request->validated('documents') ?? [],
            );
        } catch (ProviderRequestException $exception) {
            abort($exception->isRetryable() ? 503 : 422, 'El proveedor rechazó la corrección: '.$exception->getMessage());
        }

        $signatureSession->forceFill(['last_error' => null])->save();
        $signatureSession->transitionTo(RequestStatus::validating(), 'api', ['correction' => true]);
        AuditLog::record('signature_session.corrected', $request->user(), $signatureSession, [
            'fields' => array_keys(array_filter(['finger_code' => $request->validated('finger_code'), ...($request->validated('documents') ?? [])])),
        ]);

        return SignatureSessionResource::make($signatureSession);
    }
}
