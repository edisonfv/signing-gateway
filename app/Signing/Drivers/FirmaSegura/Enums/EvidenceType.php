<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Campos de evidencia de Tercero Vinculado.
 *
 * Fuente: https://docs.firmaseguraec.com/tipo-de-evidencias-tv-1859955m0
 *
 * @method static self evidenceFile()
 * @method static self validatorUrl()
 * @method static self token()
 */
final class EvidenceType extends Enum
{
    protected static function values(): array
    {
        return [
            'evidenceFile' => 'evidenceFile',
            'validatorUrl' => 'validatorUrl',
            'token' => 'token',
        ];
    }

    protected static function labels(): array
    {
        return [
            'evidenceFile' => 'Archivo JSON con los resultados de la validación',
            'validatorUrl' => 'URL para validar el archivo',
            'token' => 'Token para ver el archivo',
        ];
    }
}
