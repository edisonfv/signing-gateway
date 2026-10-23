<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de validación de Firmas Transaccionales.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-validacion-ft-1820742m0
 *
 * @method static self completed()
 * @method static self error()
 * @method static self refused()
 */
final class TransactionalValidationStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'completed' => 'Validación completada',
            'error' => 'Validación con errores',
            'refused' => 'Validación rechazada',
        ];
    }
}
