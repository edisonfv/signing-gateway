<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SignatureSessionResource;
use App\Models\SignatureRequest;
use App\Signing\ApiFlow\SessionReconciler;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Gate;

#[Group('Flujo por API', weight: 2)]
class SignatureSessionRefreshController extends Controller
{
    /**
     * Actualizar estado desde el proveedor
     *
     * Consulta bajo demanda el estado de la sesión en el proveedor y corrige el estado local si
     * hace falta. Consume el presupuesto horario de consultas compartido con la reconciliación
     * automática (cada 10 minutos); si se agotó responde 429.
     */
    public function store(SignatureRequest $signatureSession, SessionReconciler $reconciler): SignatureSessionResource
    {
        Gate::authorize('view', $signatureSession);

        abort_if($signatureSession->provider_session_id === null, 409, 'La solicitud aún no tiene sesión en el proveedor.');

        return SignatureSessionResource::make($reconciler->reconcile($signatureSession, 'api'));
    }
}
