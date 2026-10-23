<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Proveedores de firma soportados. El valor es el nombre del driver en SignatureProviderManager.
 *
 * @method static self firmaSegura()
 */
final class Provider extends Enum
{
    protected static function values(): array
    {
        return ['firmaSegura' => 'firmasegura'];
    }

    protected static function labels(): array
    {
        return ['firmaSegura' => 'FirmaSeguraEC'];
    }
}
