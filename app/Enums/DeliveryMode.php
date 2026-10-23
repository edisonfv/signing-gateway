<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Quién recibe el p12.
 *
 * @method static self holder()
 * @method static self clientSealed()
 */
final class DeliveryMode extends Enum
{
    protected static function values(): array
    {
        return [
            'holder' => 'HOLDER',
            'clientSealed' => 'CLIENT_SEALED',
        ];
    }

    protected static function labels(): array
    {
        return [
            'holder' => 'El proveedor entrega el certificado al titular',
            'clientSealed' => 'El gateway entrega el certificado sellado al SaaS',
        ];
    }
}
