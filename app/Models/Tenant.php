<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cliente del SaaS. Cada uno tiene sus propias credenciales y topes.
 */
#[Fillable(['external_id', 'name', 'daily_cap', 'hourly_cap', 'enabled_products', 'suspended_at'])]
class Tenant extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'daily_cap' => 'integer',
            'hourly_cap' => 'integer',
            'enabled_products' => 'array',
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

    /**
     * Productos que el tenant contrató (condiciones comerciales). Sin configuración, se usan
     * los de gateway.default_products.
     *
     * @return array<int, ProductType>
     */
    public function enabledProducts(): array
    {
        return array_map(
            fn (string $product): ProductType => ProductType::from($product),
            $this->enabled_products ?? config('gateway.default_products'),
        );
    }

    public function allowsProduct(ProductType $product): bool
    {
        return in_array($product->value, array_map(fn (ProductType $enabled) => $enabled->value, $this->enabledProducts()), true);
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
