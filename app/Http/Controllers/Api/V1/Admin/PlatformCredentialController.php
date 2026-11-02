<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\IssueApiClientCredentials;
use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class PlatformCredentialController extends Controller
{
    /**
     * Emitir la credencial de servicio de la plataforma
     *
     * Credencial de uso frecuente y bajo privilegio (ability `platform.service`): lee el catálogo
     * completo y reenvía los avisos del proveedor. La plataforma la emite y la renueva sola con su
     * credencial de alta; así el operador solo configura una. Hereda las IPs permitidas de la
     * credencial de alta. Devuelve el token y el secreto UNA sola vez.
     */
    public function store(Request $request, IssueApiClientCredentials $issue): JsonResponse
    {
        /** @var ApiClient $caller */
        $caller = $request->user();

        $credentials = $issue(
            $caller->tenant,
            'Plataforma: servicio · '.now()->format('Y-m-d'),
            $caller->allowed_ips ?? [],
            abilities: [PermissionName::platformService()->value],
            issuedBy: $caller,
        );

        return response()->json([
            'client' => $credentials['client']->getKey(),
            'token' => $credentials['token'],
            'signing_secret' => $credentials['signing_secret'],
            'token_expires_at' => $credentials['expires_at'],
        ], 201);
    }
}
