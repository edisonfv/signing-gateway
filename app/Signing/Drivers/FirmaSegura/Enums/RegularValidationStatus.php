<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de validación de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/estados-de-validaci%C3%B3n-de-solicitudes-fr-1820389m0
 *
 * @method static self pending()
 * @method static self manuallyValidating()
 * @method static self automaticallyValidating()
 * @method static self error()
 * @method static self refused()
 * @method static self completed()
 */
final class RegularValidationStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'pending' => 'Pendiente',
            'manuallyValidating' => 'Validación manual',
            'automaticallyValidating' => 'Validación automática',
            'error' => 'Con error',
            'refused' => 'Rechazada',
            'completed' => 'Completada',
        ];
    }
}
