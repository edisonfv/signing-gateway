<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Diagnóstico', 'Verificación de la credencial configurada en el SaaS.', weight: 0)]
class PingController extends Controller
{
    /**
     * Probar credencial
     *
     * Permite al admin del SaaS comprobar el token y la firma HMAC. Devuelve el cliente,
     * su tenant, las abilities del token y su vencimiento.
     */
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
