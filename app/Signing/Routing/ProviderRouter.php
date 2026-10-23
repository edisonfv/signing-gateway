<?php

namespace App\Signing\Routing;

use App\Enums\Environment;
use App\Enums\ProductType;
use App\Exceptions\NoProviderAvailableException;
use App\Models\ProviderAccount;
use App\Signing\SignatureProviderManager;

/**
 * Elige la cuenta de proveedor que atiende un producto: activa, del ambiente configurado,
 * cuyo driver soporte el producto, por prioridad (menor número primero).
 *
 * Cambiar de proveedor es activar otra cuenta o ajustar prioridades; el SaaS no cambia.
 */
class ProviderRouter
{
    public function __construct(private SignatureProviderManager $providers) {}

    public function accountFor(ProductType $product): ProviderAccount
    {
        $environment = Environment::from(config('gateway.environment'));

        $account = ProviderAccount::query()
            ->where('environment', $environment->value)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get()
            ->first(fn (ProviderAccount $account) => $this->providers->driver($account->provider->value)->supports($product));

        return $account ?? throw new NoProviderAvailableException($product, $environment);
    }
}
