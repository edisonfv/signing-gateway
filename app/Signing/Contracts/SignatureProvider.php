<?php

namespace App\Signing\Contracts;

use App\Enums\ProductType;
use App\Models\ProviderAccount;
use App\Signing\Data\ProductOffering;

/**
 * Contrato mínimo de un proveedor de firma.
 *
 * Las capacidades por flujo (crear sesión, emitir, cancelar…) se agregan como interfaces
 * adicionales (DrivesApiFlow, RegistersRegularRequests…), según el SignatureFlow de cada oferta.
 */
interface SignatureProvider
{
    public function name(): string;

    /**
     * Productos que el proveedor atiende por este gateway, con sus requisitos.
     *
     * @return array<int, ProductOffering>
     */
    public function offerings(): array;

    /**
     * Oferta del producto, o null si el proveedor no lo atiende.
     */
    public function offering(ProductType $product): ?ProductOffering;

    /**
     * Devuelve una instancia del driver ligada a una cuenta (credenciales y URL).
     */
    public function withAccount(ProviderAccount $account): static;
}
