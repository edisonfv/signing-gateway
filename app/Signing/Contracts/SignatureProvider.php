<?php

namespace App\Signing\Contracts;

use App\Enums\ProductType;
use App\Models\ProviderAccount;

/**
 * Contrato mínimo de un proveedor de firma.
 *
 * Las capacidades por flujo (crear sesión, emitir, cancelar…) se agregan como interfaces
 * adicionales cuando se implementa cada flujo (F1 en adelante), no antes.
 */
interface SignatureProvider
{
    public function name(): string;

    public function supports(ProductType $product): bool;

    /**
     * Devuelve una instancia del driver ligada a una cuenta (credenciales y URL).
     */
    public function withAccount(ProviderAccount $account): static;
}
