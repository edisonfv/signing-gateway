<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cliente del SaaS. Cada uno tiene sus propias credenciales y topes.
 */
#[Fillable(['external_id', 'name', 'daily_cap', 'hourly_cap', 'suspended_at'])]
class Tenant extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'daily_cap' => 'integer',
            'hourly_cap' => 'integer',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<ApiClient, $this>
     */
    public function apiClients(): HasMany
    {
        return $this->hasMany(ApiClient::class);
    }

    /**
     * @return HasMany<SignatureRequest, $this>
     */
    public function signatureRequests(): HasMany
    {
        return $this->hasMany(SignatureRequest::class);
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function dailyCap(): int
    {
        return $this->daily_cap ?? config('gateway.limits.tenant_daily');
    }

    public function hourlyCap(): int
    {
        return $this->hourly_cap ?? config('gateway.limits.tenant_hourly');
    }
}
