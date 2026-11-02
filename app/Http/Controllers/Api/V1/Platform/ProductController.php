<?php

namespace App\Http\Controllers\Api\V1\Platform;

use App\Http\Controllers\Controller;
use App\Signing\ProductCatalog;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Plataforma', 'Solo para la credencial de servicio de la plataforma (ability platform.service).', weight: 11)]
class ProductController extends Controller
{
    /**
     * Catálogo completo del gateway
     *
     * Lo mismo que GET /v1/admin/products, con la credencial de servicio (solo lectura): la plataforma
     * lo consulta seguido para mostrar ofertas sin usar la credencial de alta.
     */
    public function index(ProductCatalog $catalog): JsonResponse
    {
        return response()->json(['data' => $catalog->all()]);
    }
}
