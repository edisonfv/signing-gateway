<?php

namespace App\Actions;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Security\SealedBox;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Crea la credencial de integración de un cliente del SaaS.
 *
 * Devuelve el token y los secretos en claro UNA sola vez, para pegarlos en el admin del SaaS.
 * El token queda guardado como hash SHA-256 (Sanctum) y los secretos cifrados con la bóveda.
 */
class IssueApiClientCredentials
{
    /**
     * @param  array<int, string>  $allowedIps
     * @return array{client: ApiClient, token: string, signing_secret: string, notification_secret: string}
     */
    public function __invoke(Tenant $tenant, string $name, array $allowedIps = [], ?string $deliveryPublicKey = null, ?string $notificationUrl = null): array
    {
        if ($deliveryPublicKey !== null && ! SealedBox::isValidPublicKey($deliveryPublicKey)) {
            throw new InvalidArgumentException('La llave pública de entrega debe ser una llave X25519 en base64 (32 bytes).');
        }

        $signingSecret = Str::random(64);
        $notificationSecret = Str::random(64);

        return DB::transaction(function () use ($tenant, $name, $allowedIps, $deliveryPublicKey, $notificationUrl, $signingSecret, $notificationSecret) {
            $client = ApiClient::create([
                'tenant_id' => $tenant->getKey(),
                'name' => $name,
                'signing_secret' => $signingSecret,
                'notification_secret' => $notificationSecret,
                'delivery_public_key' => $deliveryPublicKey,
                'notification_url' => $notificationUrl,
                'allowed_ips' => $allowedIps ?: null,
            ]);

            $client->assignRole(RoleName::saasClient()->value);

            $token = $client->createToken(
                $name,
                array_map(fn (PermissionName $permission) => $permission->value, PermissionName::forApiClients()),
                now()->addDays(config('gateway.api_clients.token_ttl_days')),
            );

            AuditLog::record('api_client.issued', subject: $client, context: ['tenant' => $tenant->getKey()], ip: request()?->ip() ?? 'cli');

            return [
                'client' => $client,
                'token' => $token->plainTextToken,
                'signing_secret' => $signingSecret,
                'notification_secret' => $notificationSecret,
            ];
        });
    }
}
