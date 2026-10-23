<?php

namespace App\Signing;

use App\Models\ProviderAccount;
use App\Signing\Contracts\SignatureProvider;
use App\Signing\Drivers\FirmaSegura\FirmaSeguraDriver;
use Illuminate\Support\Manager;

/**
 * Resuelve el driver de cada proveedor, con el mismo patrón que Mail o Cache.
 *
 * Un proveedor nuevo se agrega con un método create{Nombre}Driver() o con extend().
 *
 * @method SignatureProvider driver(?string $driver = null)
 */
class SignatureProviderManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('gateway.default_provider');
    }

    public function forAccount(ProviderAccount $account): SignatureProvider
    {
        return $this->driver($account->provider->value)->withAccount($account);
    }

    protected function createFirmaseguraDriver(): FirmaSeguraDriver
    {
        return $this->container->make(FirmaSeguraDriver::class);
    }
}
