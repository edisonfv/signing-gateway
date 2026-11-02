<?php

namespace App\Signing;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\DocumentType;
use App\Enums\ProductType;
use App\Enums\Provider;
use App\Exceptions\NoProviderAvailableException;
use App\Models\ProviderAccount;
use App\Models\Tenant;
use App\Signing\Data\ProductOffering;
use App\Signing\Routing\ProviderRouter;

/**
 * Catálogo de productos armado con lo que declara cada proveedor (ProductOffering) y lo que
 * contrató cada tenant. El gateway no define requisitos propios: los toma de la oferta.
 */
class ProductCatalog
{
    public function __construct(
        private SignatureProviderManager $providers,
        private ProviderRouter $router,
    ) {}

    /**
     * Productos que algún proveedor integrado ofrece (tenga o no cuenta activa).
     *
     * @return array<int, ProductType>
     */
    public function implemented(): array
    {
        $products = [];

        foreach (Provider::cases() as $provider) {
            foreach ($this->providers->driver($provider->value)->offerings() as $offering) {
                $products[$offering->product->value] = $offering->product;
            }
        }

        return array_values($products);
    }

    public function isImplemented(ProductType $product): bool
    {
        return $product->equals(...$this->implemented());
    }

    /**
     * Oferta que verá el tenant: la de la cuenta que lo atiende, limitada a sus vigencias.
     * Null si no contrató el producto o ninguna cuenta activa lo ofrece.
     */
    public function offeringFor(ProductType $product, Tenant $tenant, ?ProviderAccount $account = null): ?ProductOffering
    {
        if (! $tenant->allowsProduct($product)) {
            return null;
        }

        try {
            $account ??= $this->router->accountFor($product, $tenant);
        } catch (NoProviderAvailableException) {
            return null;
        }

        return $this->providers->forAccount($account)->offering($product)
            ?->restrictedTo($tenant->productSetting($product)?->allowedPeriods());
    }

    /**
     * @return array<int, ProductOffering>
     */
    public function availableFor(Tenant $tenant): array
    {
        return array_values(array_filter(array_map(
            fn (ProductType $product) => $this->offeringFor($product, $tenant),
            $tenant->enabledProducts(),
        )));
    }

    /**
     * Todo lo que el gateway puede vender hoy, con las cuentas que atienden cada producto,
     * para que la plataforma configure a sus afiliados.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $catalog = [];

        foreach ($this->implemented() as $product) {
            $accounts = $this->router->candidates($product);

            if ($accounts->isEmpty()) {
                continue;
            }

            $catalog[] = [
                ...$this->describe($this->providers->forAccount($accounts->first())->offering($product)),
                'provider_accounts' => $accounts->map(fn (ProviderAccount $account) => [
                    'id' => $account->getKey(),
                    'provider' => $account->provider->value,
                    'name' => $account->name,
                    'priority' => $account->priority,
                ])->all(),
            ];
        }

        return $catalog;
    }

    /**
     * @return array<string, mixed>
     */
    public function describe(ProductOffering $offering): array
    {
        return [
            'product' => $offering->product->value,
            'label' => $offering->product->label,
            'flow' => $offering->flow->value,
            'delivery_mode' => $offering->flow->deliveryMode()->value,
            'identity_validation' => $offering->flow->identityValidation(),
            'requires_liveness' => ! $offering->acceptsDocuments(),
            'requires_birth_date' => $offering->requiresBirthDate,
            'allows_holder_password' => $offering->allowsHolderPassword,
            'application_types' => array_map(fn (ApplicationType $type) => [
                'value' => $type->value,
                'label' => $type->label,
                'required_documents' => array_map(fn (DocumentType $document) => $document->value, $offering->documentsFor($type)),
            ], $offering->applicationTypes),
            'periods' => array_map(fn (CertificatePeriod $period) => ['value' => $period->value, 'label' => $period->label], $offering->periods),
            'documents' => $offering->acceptsDocuments() ? array_map(fn (DocumentType $document) => [
                'type' => $document->value,
                'label' => $document->label,
                'mimes' => $document->allowedMimes(),
                'max_bytes' => $document->maxBytes(),
            ], DocumentType::cases()) : [],
            'guides' => $offering->acceptsDocuments() ? [
                ...$offering->guides,
                ...($offering->elderlyVideo === null ? [] : ['elderly_video' => $offering->elderlyVideo]),
            ] : [],
        ];
    }
}
