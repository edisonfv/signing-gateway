<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

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
final class TransactionalRequestStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'pending' => 'PENDING',
            'completed' => 'COMPLETED',
            'sent' => 'SENT',
            'error' => 'ERROR',
            'refused' => 'REFUSED',
        ];
    }

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
