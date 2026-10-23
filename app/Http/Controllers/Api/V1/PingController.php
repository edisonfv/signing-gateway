<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Permite al admin del SaaS probar la credencial configurada (token + firma).
 */
class PingController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var ApiClient $client */
        $client = $request->user();

        return response()->json([
            'client' => $client->getKey(),
            'tenant' => $client->tenant->external_id,
            'abilities' => $client->currentAccessToken()->abilities,
            'token_expires_at' => $client->currentAccessToken()->expires_at?->toIso8601String(),
        ]);
    }
}
