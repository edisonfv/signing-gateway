<?php

namespace App\Enums;

/**
 * @method static self holder()
 * @method static self clientSealed()
 */
final class DeliveryMode extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'holder' => 'El proveedor entrega el certificado al titular',
            'clientSealed' => 'El gateway entrega el certificado sellado al SaaS',
        ];
    }
}
