<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Signing\Data\ProductOffering;
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
     * Productos que el tenant de la credencial tiene contratados, con los requisitos que declara el
     * proveedor que lo atiende (flujo, tipos de solicitante, vigencias contratadas, documentos y textos guía).
     * El frontend del SaaS elige el flujo con esta respuesta y envía `product` al crear la solicitud.
     */
    public function index(Request $request, ProductCatalog $catalog): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->user();

        return response()->json([
            'data' => array_map(fn (ProductOffering $offering) => $catalog->describe($offering), $catalog->availableFor($client->tenant)),
        ]);
    }
}
