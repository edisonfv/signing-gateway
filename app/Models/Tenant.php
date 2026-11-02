<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Cliente del SaaS. Cada uno tiene sus propias credenciales y topes.
 */
#[Fillable(['external_id', 'name', 'daily_cap', 'hourly_cap', 'suspended_at', 'blocked_at'])]
class Tenant extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'daily_cap' => 'integer',
            'hourly_cap' => 'integer',
            'suspended_at' => 'datetime',
            'blocked_at' => 'datetime',
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

    /**
     * Suspendido (comercial) o bloqueado: no puede crear ni avanzar solicitudes.
     */
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null || $this->blocked_at !== null;
    }

    /**
     * Bloqueado por un incidente: tampoco puede consultar ni retirar lo ya emitido.
     */
    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    /**
     * @return HasMany<TenantProduct, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(TenantProduct::class);
    }

    /**
     * Productos que el tenant contrató (condiciones comerciales). Un tenant sin productos
     * configurados usa los de gateway.default_products.
     *
     * @return array<int, ProductType>
     */
    public function enabledProducts(): array
    {
        if (! $this->products()->exists()) {
            return array_map(fn (string $product): ProductType => ProductType::from($product), config('gateway.default_products'));
        }

        return $this->products()->where('is_active', true)->orderBy('created_at')->get()
            ->map(fn (TenantProduct $setting): ProductType => $setting->product)
            ->all();
    }

    public function allowsProduct(ProductType $product): bool
    {
        return $product->equals(...$this->enabledProducts());
    }

    /**
     * Condiciones del producto contratado, o null si no tiene (productos por defecto).
     */
    public function productSetting(ProductType $product): ?TenantProduct
    {
        return $this->products()->where('product', $product->value)->where('is_active', true)->first();
    }

    /**
     * Reemplaza los productos contratados. Los que no se envían quedan inactivos (no se borran, para
     * conservar sus condiciones si se reactivan).
     *
     * @param  array<int, array{product: string, periods?: array<int, string>|null, preferred_provider_account_id?: string|null}>  $products
     */
    public function syncProducts(array $products): void
    {
        DB::transaction(function () use ($products) {
            $kept = [];

            foreach ($products as $product) {
                $this->products()->updateOrCreate(['product' => $product['product']], [
                    'periods' => isset($product['periods']) ? array_values(array_unique($product['periods'])) : null,
                    'preferred_provider_account_id' => $product['preferred_provider_account_id'] ?? null,
                    'is_active' => true,
                ]);
                $kept[] = $product['product'];
            }

            $this->products()->whereNotIn('product', $kept)->update(['is_active' => false]);
        });
    }

    /**
     * Activa solo estos productos y conserva las condiciones que ya tenían (vigencias, cuenta).
     *
     * @param  array<int, string>  $products
     */
    public function enableOnly(array $products): void
    {
        DB::transaction(function () use ($products) {
            foreach (array_unique($products) as $product) {
                $this->products()->updateOrCreate(['product' => $product], ['is_active' => true]);
            }

            $this->products()->whereNotIn('product', $products)->update(['is_active' => false]);
        });
    }

    /**
     * @return array<int, array{product: string, periods: array<int, string>|null, preferred_provider_account_id: string|null}>
     */
    public function productSettings(): array
    {
        return $this->products()->where('is_active', true)->orderBy('created_at')->get()
            ->map(fn (TenantProduct $setting) => [
                'product' => $setting->product->value,
                'periods' => $setting->periods,
                'preferred_provider_account_id' => $setting->preferred_provider_account_id,
            ])->all();
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
