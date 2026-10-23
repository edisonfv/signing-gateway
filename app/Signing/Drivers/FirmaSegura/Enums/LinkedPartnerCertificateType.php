<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipos de certificado de Tercero Vinculado.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-tipos-de-certificado-tv-1859949m0
 *
 * @method static self transactionalSignature()
 * @method static self traditionalSignature()
 */
final class LinkedPartnerCertificateType extends Enum
{
    protected static function values(): array
    {
        return [
            'transactionalSignature' => 'TRANSACTIONAL_SIGNATURE',
            'traditionalSignature' => 'TRADITIONAL_SIGNATURE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'transactionalSignature' => 'Firma transaccional (corta duración)',
            'traditionalSignature' => 'Firma tradicional (vigencia elegida)',
        ];
    }
}
