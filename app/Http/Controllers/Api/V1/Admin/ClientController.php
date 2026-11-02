<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\IssueApiClientCredentials;
use App\Actions\RevokeApiClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiClientRequest;
use App\Models\ApiClient;
use App\Models\Tenant;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\HeaderParameter;
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
     *
     * Con `Idempotency-Key`, un reintento con la misma clave revoca la credencial que emitió el
     * intento anterior (sus secretos no se pueden volver a mostrar) y emite una nueva: un job que
     * falló después de emitir nunca deja credenciales huérfanas activas.
     */
    #[HeaderParameter('Idempotency-Key', 'Identificador de la operación en la plataforma (máx. 100 caracteres). Recomendado.', required: false, type: 'string')]
    public function store(StoreApiClientRequest $request, string $externalId, IssueApiClientCredentials $issue, RevokeApiClient $revoke): JsonResponse
    {
        $tenant = Tenant::query()->where('external_id', $externalId)->firstOrFail();
        $data = $request->validated();
        $idempotencyKey = $request->header('Idempotency-Key') ?: null;
        abort_if($idempotencyKey !== null && mb_strlen($idempotencyKey) > 100, 400, 'Idempotency-Key admite hasta 100 caracteres.');

        /** @var ApiClient $caller */
        $caller = $request->user();

        $replaced = $idempotencyKey === null ? collect() : $tenant->apiClients()
            ->where('idempotency_key', $idempotencyKey)
            ->whereNull('revoked_at')
            ->get();

        try {
            $credentials = $issue($tenant, $data['name'], $data['allowed_ips'] ?? [], $data['delivery_public_key'], $data['notification_url'], issuedBy: $caller);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage(), 'errors' => ['delivery_public_key' => [$exception->getMessage()]]], 422);
        }

        $credentials['client']->forceFill(['idempotency_key' => $idempotencyKey])->save();
        $replaced->each(fn (ApiClient $client) => $revoke($client, $caller));

        return response()->json([
            'client' => $credentials['client']->getKey(),
            'replaced_clients' => $replaced->map(fn (ApiClient $client) => $client->getKey())->values()->all(),
            'token' => $credentials['token'],
            'signing_secret' => $credentials['signing_secret'],
            'notification_secret' => $credentials['notification_secret'],
            'token_expires_at' => $credentials['expires_at'],
        ], 201);
    }

    /**
     * Credenciales de un afiliado
     *
     * Para auditar y limpiar: una credencial activa que la plataforma no tiene guardada debe revocarse.
     * Nunca devuelve tokens ni secretos.
     */
    public function index(string $externalId): JsonResponse
    {
        $tenant = Tenant::query()->where('external_id', $externalId)->firstOrFail();

        $clients = $tenant->apiClients()->with('tokens')->latest()->get()->map(fn (ApiClient $client) => [
            'client' => $client->getKey(),
            'name' => $client->name,
            'abilities' => $client->tokens->first()?->abilities ?? [],
            'created_at' => $client->created_at?->toIso8601String(),
            'last_used_at' => $client->last_used_at?->toIso8601String(),
            'token_expires_at' => $client->tokens->first()?->expires_at?->toIso8601String(),
            'revoked_at' => $client->revoked_at?->toIso8601String(),
        ]);

        return response()->json(['data' => $clients]);
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
