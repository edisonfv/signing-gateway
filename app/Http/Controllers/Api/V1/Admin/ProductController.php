<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Signing\ProductCatalog;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class ProductController extends Controller
{
    /**
     * Catálogo completo del gateway
     *
     * Todos los productos que el gateway puede emitir en este ambiente, con los requisitos que declara
     * el proveedor y las cuentas que los atienden. La plataforma lo usa para configurar a cada afiliado.
     */
    public function index(ProductCatalog $catalog): JsonResponse
    {
        return response()->json(['data' => $catalog->all()]);
    }
}
