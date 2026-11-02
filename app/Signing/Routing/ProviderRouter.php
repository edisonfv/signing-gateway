<?php

namespace App\Signing\Routing;

use App\Enums\Environment;
use App\Enums\ProductType;
use App\Exceptions\NoProviderAvailableException;
use App\Models\ProviderAccount;
use App\Models\Tenant;
use App\Signing\SignatureProviderManager;
use Illuminate\Support\Collection;

/**
 * Elige la cuenta de proveedor que atiende un producto: activa, del ambiente configurado y
 * cuyo driver ofrezca el producto. Primero la cuenta preferida del tenant, si está disponible;
 * si no, por prioridad (menor número primero).
 *
 * Cambiar de proveedor es activar otra cuenta o ajustar prioridades; el SaaS no cambia.
 */
class ProviderRouter
{
    public function __construct(private SignatureProviderManager $providers) {}

    public function accountFor(ProductType $product, ?Tenant $tenant = null): ProviderAccount
    {
        $candidates = $this->candidates($product);
        $preferred = $tenant?->productSetting($product)?->preferred_provider_account_id;

        $account = ($preferred ? $candidates->firstWhere('id', $preferred) : null) ?? $candidates->first();

        return $account ?? throw new NoProviderAvailableException($product, Environment::from(config('gateway.environment')));
    }

    public function hasAccountFor(ProductType $product): bool
    {
        return $this->candidates($product)->isNotEmpty();
    }

    /**
     * Cuentas activas del ambiente que ofrecen el producto, por prioridad.
     *
     * @return Collection<int, ProviderAccount>
     */
    public function candidates(ProductType $product): Collection
    {
        return ProviderAccount::query()
            ->where('environment', Environment::from(config('gateway.environment'))->value)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get()
            ->filter(fn (ProviderAccount $account) => $this->providers->forAccount($account)->offering($product) !== null)
            ->values();
    }
}
