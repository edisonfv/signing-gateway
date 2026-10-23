<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Razones de cancelación automática de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-razones-de-cancelaci%C3%B3n-autom%C3%A1tica-fr-2442844m0
 *
 * @method static self expiredCorrection()
 * @method static self outdatedIdentification()
 * @method static self civilRegistryDataNotFound()
 * @method static self unclaimed()
 */
final class AutoCancellationReason extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'expiredCorrection' => 'No se envió la corrección a tiempo',
            'outdatedIdentification' => 'Cédula no vigente en Registro Civil',
            'civilRegistryDataNotFound' => 'Cédula o código dactilar no encontrados',
            'unclaimed' => 'Certificado no reclamado a tiempo',
        ];
    }
}
