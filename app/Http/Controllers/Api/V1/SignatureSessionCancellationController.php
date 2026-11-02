<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CancelSignatureSession;
use App\Enums\RequestStatus;
use App\Exceptions\ProviderRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\SignatureSessionResource;
use App\Models\SignatureRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class SignatureSessionCancellationController extends Controller
{
    /**
     * Cancelar solicitud
     *
     * Solo antes de emitir el certificado. Si la sesión ya existe en el proveedor, también se cancela allá.
     */
    public function store(Request $request, SignatureRequest $signatureSession, CancelSignatureSession $cancel): SignatureSessionResource
    {
        Gate::authorize('update', $signatureSession);

        abort_unless($signatureSession->status->canTransitionTo(RequestStatus::cancelled()), 409, 'La solicitud ya no se puede cancelar.');
        abort_unless($cancel->canCancel($signatureSession), 409, 'Firmas Regulares no ofrece cancelación una vez registrada en el proveedor.');

        try {
            $cancel($signatureSession, 'api', $request->user());
        } catch (ProviderRequestException $exception) {
            abort($exception->isRetryable() ? 503 : 409, 'El proveedor no aceptó la cancelación: '.$exception->getMessage());
        }

        return SignatureSessionResource::make($signatureSession);
    }
}
