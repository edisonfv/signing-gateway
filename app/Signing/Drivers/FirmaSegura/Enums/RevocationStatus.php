<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de revocación.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-revocaci%C3%B3n-1820403m0
 *
 * @method static self registered()
 * @method static self approved()
 * @method static self rejected()
 */
final class RevocationStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'registered' => 'Revocación ingresada',
            'approved' => 'Revocación aprobada',
            'rejected' => 'Revocación rechazada',
        ];
    }
}
