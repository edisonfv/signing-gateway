<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Códigos de error de validación de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/c%C3%B3digos-de-error-de-validaci%C3%B3n-fr-1820392m0
 *
 * @method static self frontIdentification()
 * @method static self backIdentification()
 * @method static self selfie()
 * @method static self elderlyVideo()
 * @method static self identificationNumber()
 * @method static self fingerCode()
 * @method static self firstName()
 * @method static self firstLastName()
 * @method static self secondLastName()
 * @method static self birthDate()
 */
final class RegularValidationErrorCode extends Enum
{
    protected static function values(): array
    {
        return [
            'frontIdentification' => 'A01',
            'backIdentification' => 'A02',
            'selfie' => 'A03',
            'elderlyVideo' => 'A04',
            'identificationNumber' => 'D01',
            'fingerCode' => 'D02',
            'firstName' => 'D03',
            'firstLastName' => 'D04',
            'secondLastName' => 'D05',
            'birthDate' => 'D06',
        ];
    }

    protected static function labels(): array
    {
        return [
            'frontIdentification' => 'Error en la validación de la cédula frontal',
            'backIdentification' => 'Error en la validación de la cédula posterior',
            'selfie' => 'Error en la validación de la selfie del solicitante',
            'elderlyVideo' => 'Error en la validación del video de autorización para tercera edad',
            'identificationNumber' => 'Error en el número de identificación',
            'fingerCode' => 'Error en el código de huella dactilar',
            'firstName' => 'Error en el nombre del solicitante',
            'firstLastName' => 'Error en el primer apellido del solicitante',
            'secondLastName' => 'Error en el segundo apellido del solicitante',
            'birthDate' => 'Error en la fecha de nacimiento del solicitante',
        ];
    }
}
