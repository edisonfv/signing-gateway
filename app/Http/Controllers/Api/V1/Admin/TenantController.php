<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertTenantRequest;
use App\Models\AuditLog;
use App\Models\Tenant;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class TenantController extends Controller
{
    /**
     * Registrar o actualizar un afiliado
     *
     * Idempotente por `external_id` (el id del afiliado en el SaaS): la primera vez lo crea,
     * después actualiza su nombre y, si se envían, sus productos contratados (conserva las condiciones
     * de cada uno; para vigencias o proveedor usar PUT /v1/admin/tenants/{external_id}/products).
     */
    public function update(UpsertTenantRequest $request, string $externalId): JsonResponse
    {
        $data = $request->validated();
        $tenant = Tenant::query()->firstOrNew(['external_id' => $externalId]);
        $created = ! $tenant->exists;

        $tenant->name = $data['name'];
        $tenant->save();

        if (array_key_exists('products', $data)) {
            $tenant->enableOnly($data['products']);
        }

        AuditLog::record($created ? 'tenant.created' : 'tenant.updated', $request->user(), $tenant, context: ['products' => $data['products'] ?? 'unchanged']);

        return response()->json([
            'external_id' => $tenant->external_id,
            'name' => $tenant->name,
            'products' => array_map(fn (ProductType $product) => $product->value, $tenant->enabledProducts()),
            'suspended' => $tenant->isSuspended(),
            'blocked' => $tenant->isBlocked(),
        ], $created ? 201 : 200);
    }
}
