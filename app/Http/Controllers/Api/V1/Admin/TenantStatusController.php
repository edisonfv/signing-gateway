<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\SuspendTenant;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\Tenant;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class TenantStatusController extends Controller
{
    /**
     * Suspender un afiliado
     *
     * Comercial (canceló, no pagó): desde este instante no crea ni avanza solicitudes; sus solicitudes
     * abiertas no emitidas se cancelan en el proveedor. Aún puede consultar y retirar lo ya emitido.
     */
    public function suspend(Request $request, string $externalId, SuspendTenant $action): JsonResponse
    {
        $tenant = $this->tenant($request, $externalId);
        $action->suspend($tenant, $request->user(), $request->string('reason')->limit(250)->toString() ?: null);

        return $this->respond($tenant);
    }

    /**
     * Bloquear un afiliado
     *
     * Incidente de seguridad: como suspender, y además revoca todas sus credenciales; no puede
     * consultar ni retirar nada. Al reactivarlo hay que emitirle credenciales nuevas.
     */
    public function block(Request $request, string $externalId, SuspendTenant $action): JsonResponse
    {
        $tenant = $this->tenant($request, $externalId);
        $action->block($tenant, $request->user(), $request->string('reason')->limit(250)->toString() ?: null);

        return $this->respond($tenant);
    }

    /**
     * Reactivar un afiliado
     */
    public function resume(Request $request, string $externalId, SuspendTenant $action): JsonResponse
    {
        $tenant = $this->tenant($request, $externalId);
        $action->resume($tenant, $request->user());

        return $this->respond($tenant);
    }

    private function tenant(Request $request, string $externalId): Tenant
    {
        $tenant = Tenant::query()->where('external_id', $externalId)->firstOrFail();

        /** @var ApiClient $caller */
        $caller = $request->user();
        abort_if($tenant->is($caller->tenant), 409, 'La plataforma no puede detener su propio tenant.');

        return $tenant;
    }

    private function respond(Tenant $tenant): JsonResponse
    {
        return response()->json([
            'external_id' => $tenant->external_id,
            'suspended' => $tenant->isSuspended(),
            'blocked' => $tenant->isBlocked(),
            'active_credentials' => $tenant->apiClients()->whereNull('revoked_at')->count(),
        ]);
    }
}
