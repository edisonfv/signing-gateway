<?php

namespace App\Signing\ApiFlow;

use App\Models\ProviderAccount;
use App\Signing\Contracts\DrivesApiFlow;
use App\Signing\Data\TermsData;
use App\Signing\SignatureProviderManager;
use Illuminate\Support\Facades\Cache;
use LogicException;

/**
 * Términos y condiciones vigentes por cuenta de proveedor, con caché para no consultar
 * al proveedor en cada solicitud.
 */
class TermsCatalog
{
    private const TTL_SECONDS = 1800;

    public function __construct(private SignatureProviderManager $providers) {}

    public function current(ProviderAccount $account): TermsData
    {
        return TermsData::fromArray(Cache::remember(
            "provider-terms:{$account->getKey()}",
            self::TTL_SECONDS,
            fn (): array => $this->driver($account)->currentTerms()->toArray(),
        ));
    }

    public function forget(ProviderAccount $account): void
    {
        Cache::forget("provider-terms:{$account->getKey()}");
    }

    private function driver(ProviderAccount $account): DrivesApiFlow
    {
        $driver = $this->providers->forAccount($account);

        return $driver instanceof DrivesApiFlow
            ? $driver
            : throw new LogicException("El proveedor {$driver->name()} no implementa el Flujo por API.");
    }
}
