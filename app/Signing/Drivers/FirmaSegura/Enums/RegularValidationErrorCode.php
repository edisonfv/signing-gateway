<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Códigos de error de validación de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/c%C3%B3digos-de-error-de-validaci%C3%B3n-fr-1820392m0
 *
 * @method static self a01()
 * @method static self a02()
 * @method static self a03()
 * @method static self a04()
 * @method static self d01()
 * @method static self d02()
 * @method static self d03()
 * @method static self d04()
 * @method static self d05()
 * @method static self d06()
 */
final class RegularValidationErrorCode extends Enum
{
    protected static function values(): array
    {
        return [
            'a01' => 'A01',
            'a02' => 'A02',
            'a03' => 'A03',
            'a04' => 'A04',
            'd01' => 'D01',
            'd02' => 'D02',
            'd03' => 'D03',
            'd04' => 'D04',
            'd05' => 'D05',
            'd06' => 'D06',
        ];
    }

    protected static function labels(): array
    {
        return [
            'a01' => 'Error en la validación de la cédula frontal',
            'a02' => 'Error en la validación de la cédula posterior',
            'a03' => 'Error en la validación de la selfie del solicitante',
            'a04' => 'Error en la validación del video de autorización para tercera edad',
            'd01' => 'Error en el número de identificación',
            'd02' => 'Error en el código de huella dactilar',
            'd03' => 'Error en el nombre del solicitante',
            'd04' => 'Error en el primer apellido del solicitante',
            'd05' => 'Error en el segundo apellido del solicitante',
            'd06' => 'Error en la fecha de nacimiento del solicitante',
        ];
    }
}
