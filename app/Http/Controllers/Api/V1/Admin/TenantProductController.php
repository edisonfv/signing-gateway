<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTenantProductsRequest;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Signing\ProductCatalog;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class TenantProductController extends Controller
{
    /**
     * Productos de un afiliado
     *
     * Condiciones configuradas y la oferta efectiva que verá el afiliado en GET /v1/products.
     */
    public function show(string $externalId, ProductCatalog $catalog): JsonResponse
    {
        return $this->respond(Tenant::query()->where('external_id', $externalId)->firstOrFail(), $catalog);
    }

    /**
     * Configurar productos de un afiliado
     *
     * Reemplaza la configuración completa: productos, vigencias que puede vender y cuenta de
     * proveedor preferida. Los productos que no se envían quedan desactivados.
     */
    public function update(UpdateTenantProductsRequest $request, string $externalId, ProductCatalog $catalog): JsonResponse
    {
        $tenant = Tenant::query()->where('external_id', $externalId)->firstOrFail();

        $tenant->syncProducts($request->validated('products'));
        AuditLog::record('tenant.products_updated', $request->user(), $tenant, context: ['products' => $request->validated('products')]);

        return $this->respond($tenant, $catalog);
    }

    private function respond(Tenant $tenant, ProductCatalog $catalog): JsonResponse
    {
        return response()->json([
            'external_id' => $tenant->external_id,
            'uses_defaults' => ! $tenant->products()->exists(),
            'settings' => $tenant->productSettings(),
            'available' => array_map(fn ($offering) => $catalog->describe($offering), $catalog->availableFor($tenant)),
        ]);
    }
}
