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
     * @param  array<int, string>  $abilities  subconjunto de PermissionName::forApiClients(), o clients.manage
     *                                         para la credencial de alta de la plataforma; vacío = las de un cliente
     * @return array{client: ApiClient, token: string, signing_secret: string, notification_secret: string, expires_at: string|null}
     */
    public function __invoke(Tenant $tenant, string $name, array $allowedIps = [], ?string $deliveryPublicKey = null, ?string $notificationUrl = null, array $abilities = [], ?ApiClient $issuedBy = null): array
    {
        if ($deliveryPublicKey !== null && ! SealedBox::isValidPublicKey($deliveryPublicKey)) {
            throw new InvalidArgumentException('La llave pública de entrega debe ser una llave X25519 en base64 (32 bytes).');
        }

        $forClients = array_map(fn (PermissionName $permission) => $permission->value, PermissionName::forApiClients());
        $allowed = [...$forClients, PermissionName::clientsManage()->value];
        $unknown = array_diff($abilities, $allowed);

        if ($unknown !== []) {
            throw new InvalidArgumentException('Abilities no permitidas para un cliente: '.implode(', ', $unknown).'. Válidas: '.implode(', ', $allowed).'.');
        }

        // Una credencial solo de lectura (p. ej. el catálogo de la plataforma) no puede emitir ni retirar;
        // clients.manage (dar de alta afiliados) nunca se otorga por defecto.
        $abilities = $abilities === [] ? $forClients : array_values(array_unique($abilities));

        $signingSecret = Str::random(64);
        $notificationSecret = Str::random(64);

        return DB::transaction(function () use ($tenant, $name, $allowedIps, $deliveryPublicKey, $notificationUrl, $signingSecret, $notificationSecret, $abilities, $issuedBy) {
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
                $abilities,
                now()->addDays(config('gateway.api_clients.token_ttl_days')),
            );

            AuditLog::record('api_client.issued', $issuedBy, $client, context: ['tenant' => $tenant->getKey(), 'abilities' => $abilities], ip: $issuedBy ? null : 'cli');

            return [
                'client' => $client,
                'token' => $token->plainTextToken,
                'signing_secret' => $signingSecret,
                'notification_secret' => $notificationSecret,
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
            ];
        });
    }
}
