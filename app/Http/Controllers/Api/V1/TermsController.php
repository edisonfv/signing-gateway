<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ProductType;
use App\Http\Controllers\Controller;
use App\Signing\ApiFlow\TermsCatalog;
use App\Signing\Routing\ProviderRouter;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

#[Group('Flujo por API', weight: 2)]
class TermsController extends Controller
{
    /**
     * Términos y condiciones vigentes
     *
     * El SaaS los muestra al titular y envía `id` y `version` al crear la solicitud.
     */
    public function show(ProviderRouter $router, TermsCatalog $catalog): JsonResponse
    {
        $terms = $catalog->current($router->accountFor(ProductType::headless()));

        return response()->json(['data' => [
            'id' => $terms->id,
            'version' => $terms->version,
            'content' => $terms->content,
            'content_sha256' => $terms->contentSha256(),
        ]]);
    }
}
