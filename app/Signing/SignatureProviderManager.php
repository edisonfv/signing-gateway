<?php

namespace App\Signing;

use App\Models\ProviderAccount;
use App\Models\SignatureRequest;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\Contracts\SignatureProvider;
use App\Signing\Drivers\FirmaSegura\FirmaSeguraDriver;
use Illuminate\Support\Manager;
use LogicException;

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

    /**
     * Driver de la cuenta que atiende la solicitud, con las capacidades del Flujo por API.
     */
    public function apiFlowFor(SignatureRequest $request): DrivesApiFlow
    {
        $driver = $this->forAccount($request->providerAccount);

        if (! $driver instanceof DrivesApiFlow) {
            throw new LogicException("El proveedor {$driver->name()} no implementa el Flujo por API.");
        }

        return $driver;
    }

    protected function createFirmaseguraDriver(): FirmaSeguraDriver
    {
        return $this->container->make(FirmaSeguraDriver::class);
    }
}
