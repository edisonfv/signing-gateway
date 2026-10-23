<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Tipos de documento de términos y condiciones.
 *
 * Fuente: https://docs.firmaseguraec.com/consulta-de-tyc-34905010e0
 *
 * @method static self traditionalSignature()
 */
final class TermsType extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'traditionalSignature' => 'Firma tradicional',
        ];
    }
}
