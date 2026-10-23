<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Productos internos, independientes del proveedor (ver docs/ARQUITECTURA.md §3.1).
 *
 * @method static self regular()
 * @method static self transactional()
 * @method static self linkedPartner()
 * @method static self simplified()
 * @method static self headless()
 * @method static self posVoucher()
 */
final class ProductType extends Enum
{
    /**
     * Quién recibe el p12: el titular (lo entrega el proveedor) o el SaaS (sellado).
     */
    public function deliveryMode(): DeliveryMode
    {
        return $this->equals(self::transactional(), self::linkedPartner(), self::headless())
            ? DeliveryMode::clientSealed()
            : DeliveryMode::holder();
    }

    protected static function values(): array
    {
        return [
            'regular' => 'REGULAR',
            'transactional' => 'TRANSACTIONAL',
            'linkedPartner' => 'LINKED_PARTNER',
            'simplified' => 'SIMPLIFIED',
            'headless' => 'HEADLESS',
            'posVoucher' => 'POS_VOUCHER',
        ];
    }

    protected static function labels(): array
    {
        return [
            'regular' => 'Firmas Regulares',
            'transactional' => 'Firmas Transaccionales',
            'linkedPartner' => 'Tercero Vinculado',
            'simplified' => 'Flujo Simplificado',
            'headless' => 'Flujo por API',
            'posVoucher' => 'Punto de venta',
        ];
    }
}
