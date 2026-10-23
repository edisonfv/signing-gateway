<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

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
final class RegularValidationStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'pending' => 'PENDING',
            'manuallyValidating' => 'MANUALLY_VALIDATING',
            'automaticallyValidating' => 'AUTOMATICALLY_VALIDATING',
            'error' => 'ERROR',
            'refused' => 'REFUSED',
            'completed' => 'COMPLETED',
        ];
    }

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
