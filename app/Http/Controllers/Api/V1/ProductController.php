<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Signing\ProductCatalog;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Catálogos', weight: 1)]
class ProductController extends Controller
{
    /**
     * Productos contratados
     *
     * Flujos de firma que el tenant de la credencial tiene habilitados por sus condiciones
     * comerciales, con sus requisitos (tipos de solicitante, vigencias, documentos y textos guía).
     * El frontend del SaaS elige el flujo con esta respuesta y envía `product` al crear la solicitud.
     */
    public function index(Request $request, ProductCatalog $catalog): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->user();

        return response()->json([
            'data' => array_map(fn ($product) => $catalog->describe($product), $catalog->availableFor($client->tenant)),
        ]);
    }
}
