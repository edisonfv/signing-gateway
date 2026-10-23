<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Estados de revocación.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-revocaci%C3%B3n-1820403m0
 *
 * @method static self registered()
 * @method static self approved()
 * @method static self rejected()
 */
final class RevocationStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'registered' => 'REGISTERED',
            'approved' => 'APPROVED',
            'rejected' => 'REJECTED',
        ];
    }

    protected static function labels(): array
    {
        return [
            'registered' => 'Revocación ingresada',
            'approved' => 'Revocación aprobada',
            'rejected' => 'Revocación rechazada',
        ];
    }
}
