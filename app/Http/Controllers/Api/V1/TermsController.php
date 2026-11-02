<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Signing\ApiFlow\TermsCatalog;
use App\Signing\Routing\ProviderRouter;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Flujo por API', weight: 2)]
class TermsController extends Controller
{
    /**
     * Términos y condiciones vigentes
     *
     * Términos del proveedor que atenderá la solicitud del producto. El SaaS los muestra al titular
     * y envía `id` y `version` al crear la solicitud con el mismo `product`.
     */
    #[QueryParameter('product', 'Producto que se va a solicitar.', type: 'string', default: 'HEADLESS', example: 'REGULAR')]
    public function show(Request $request, ProviderRouter $router, TermsCatalog $catalog): JsonResponse
    {
        $product = ProductType::tryFrom((string) $request->query('product', ProductType::headless()->value));

        abort_if($product === null, 422, 'Producto desconocido.');

        /** @var ApiClient $client */
        $client = $request->user();
        $terms = $catalog->current($router->accountFor($product, $client->tenant));

        return response()->json(['data' => [
            'id' => $terms->id,
            'version' => $terms->version,
            'content' => $terms->content,
            'content_sha256' => $terms->contentSha256(),
        ]]);
    }
}
