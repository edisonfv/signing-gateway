<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipos de documento de términos y condiciones.
 *
 * Fuente: https://docs.firmaseguraec.com/consulta-de-tyc-34905010e0
 *
 * @method static self traditionalSignature()
 */
final class TermsType extends Enum
{
    protected static function values(): array
    {
        return [
            'traditionalSignature' => 'TRADITIONAL_SIGNATURE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'traditionalSignature' => 'Firma tradicional',
        ];
    }
}
