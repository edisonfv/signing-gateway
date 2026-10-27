<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\IssueApiClientCredentials;
use App\Actions\RevokeApiClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiClientRequest;
use App\Models\ApiClient;
use App\Models\Tenant;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

#[Group('Alta de afiliados', 'Solo para la credencial de alta de la plataforma (ability clients.manage).', weight: 9)]
class ClientController extends Controller
{
    /**
     * Emitir la credencial de un afiliado
     *
     * Devuelve el token y los secretos UNA sola vez; el SaaS los guarda cifrados. La credencial
     * tiene las abilities de un cliente (nunca clients.manage) y vence según la configuración.
     */
    public function store(StoreApiClientRequest $request, string $externalId, IssueApiClientCredentials $issue): JsonResponse
    {
        $tenant = Tenant::query()->where('external_id', $externalId)->firstOrFail();
        $data = $request->validated();

        /** @var ApiClient $caller */
        $caller = $request->user();

        try {
            $credentials = $issue($tenant, $data['name'], $data['allowed_ips'] ?? [], $data['delivery_public_key'], $data['notification_url'], issuedBy: $caller);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => ['delivery_public_key' => [$exception->getMessage()]]], 422);
        }

        return response()->json([
            'client' => $credentials['client']->getKey(),
            'token' => $credentials['token'],
            'signing_secret' => $credentials['signing_secret'],
            'notification_secret' => $credentials['notification_secret'],
            'token_expires_at' => $credentials['expires_at'],
        ], 201);
    }

    /**
     * Revocar una credencial
     *
     * Sus tokens dejan de funcionar al instante. Se usa al renovar: primero se emite y guarda la
     * nueva, después se revoca la anterior. La credencial de alta no puede revocarse a sí misma.
     */
    public function destroy(Request $request, ApiClient $client, RevokeApiClient $revoke): JsonResponse
    {
        /** @var ApiClient $caller */
        $caller = $request->user();
        abort_if($client->is($caller), 409, 'Una credencial no puede revocarse a sí misma.');

        $revoke($client, $caller);

        return response()->json(['client' => $client->getKey(), 'revoked_at' => $client->fresh()?->revoked_at?->toIso8601String()]);
    }
}
