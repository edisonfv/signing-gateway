<?php

namespace App\Signing\Drivers\FirmaSegura;

use App\Enums\ProductType;
use App\Models\ProviderAccount;
use App\Signing\Contracts\SignatureProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LogicException;

/**
 * Driver de FirmaSeguraEC. En F0 solo provee el cliente HTTP autenticado;
 * los pasos del Flujo por API se implementan en F1.
 */
class FirmaSeguraDriver implements SignatureProvider
{
    private ?ProviderAccount $account = null;

    public function name(): string
    {
        return 'firmasegura';
    }

    public function supports(ProductType $product): bool
    {
        return $product->equals(ProductType::headless());
    }

    public function withAccount(ProviderAccount $account): static
    {
        $driver = clone $this;
        $driver->account = $account;

        return $driver;
    }

    /**
     * Cliente HTTP con la URL y el token de la cuenta. Sin reintentos automáticos:
     * cada paso decide si es seguro reintentar (los POST de emisión no lo son).
     */
    public function http(): PendingRequest
    {
        $account = $this->account ?? throw new LogicException('El driver de FirmaSegura no tiene una cuenta asignada.');

        return Http::baseUrl($account->base_url)
            ->withToken($account->credentials['token'] ?? throw new LogicException("La cuenta {$account->getKey()} no tiene token."))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(30);
    }
}
