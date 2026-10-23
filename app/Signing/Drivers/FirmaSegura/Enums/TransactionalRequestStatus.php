<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de solicitud de Firmas Transaccionales.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-solicitud-ft-1820740m0
 *
 * @method static self pending()
 * @method static self completed()
 * @method static self sent()
 * @method static self error()
 * @method static self refused()
 */
final class TransactionalRequestStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'pending' => 'En validación',
            'completed' => 'Validación completada',
            'sent' => 'Firma enviada',
            'error' => 'Con errores',
            'refused' => 'Rechazada',
        ];
    }
}
