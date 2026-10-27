<?php

namespace App\Actions;

use App\Models\ApiClient;
use App\Models\AuditLog;

/**
 * Revoca la credencial de un cliente del SaaS: sus tokens dejan de funcionar al instante.
 * La usan `gateway:client:revoke` y el alta por API (renovación de credenciales).
 */
class RevokeApiClient
{
    /** @return bool false si ya estaba revocado */
    public function __invoke(ApiClient $client, ?ApiClient $revokedBy = null): bool
    {
        if ($client->revoked_at !== null) {
            return false;
        }

        $client->forceFill(['revoked_at' => now()])->save();
        $client->tokens()->delete();

        AuditLog::record('api_client.revoked', $revokedBy, $client, context: ['tenant' => $client->tenant_id], ip: $revokedBy ? null : 'cli');

        return true;
    }
}
