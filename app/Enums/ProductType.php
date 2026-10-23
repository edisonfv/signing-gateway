<?php

namespace App\Enums;

/**
 * Productos internos, independientes del proveedor (ver ARQUITECTURA.md §3.1).
 *
 * @method static self regular()
 * @method static self transactional()
 * @method static self linkedPartner()
 * @method static self simplified()
 * @method static self headless()
 * @method static self posVoucher()
 */
final class ProductType extends ScreamingSnakeEnum
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
}
