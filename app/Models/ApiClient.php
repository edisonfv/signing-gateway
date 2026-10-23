<?php

namespace App\Models;

use App\Casts\VaultEncrypted;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Credencial de integración de un cliente del SaaS, limitada a su tenant (guard api).
 *
 * El tenant se deduce siempre del token autenticado, nunca de la petición.
 */
#[Fillable(['tenant_id', 'name', 'signing_secret', 'notification_secret', 'delivery_public_key', 'allowed_ips', 'revoked_at'])]
#[Hidden(['signing_secret', 'notification_secret'])]
class ApiClient extends Authenticatable
{
    use HasApiTokens, HasRoles, HasUuids;

    protected string $guard_name = 'api';

    protected function casts(): array
    {
        return [
            'signing_secret' => VaultEncrypted::class,
            'notification_secret' => VaultEncrypted::class,
            'allowed_ips' => 'array',
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null && ! $this->tenant->isSuspended();
    }

    public function allowsIp(?string $ip): bool
    {
        return empty($this->allowed_ips) || ($ip !== null && IpUtils::checkIp($ip, $this->allowed_ips));
    }

    public function markUsed(Request $request): void
    {
        $this->forceFill(['last_used_at' => now(), 'last_used_ip' => $request->ip()])->saveQuietly();
    }
}
