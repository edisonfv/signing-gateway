<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de solicitud de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-solicitud-fr-1820725m0
 *
 * @method static self registered()
 * @method static self validating()
 * @method static self refused()
 * @method static self error()
 * @method static self approved()
 * @method static self generated()
 * @method static self expired()
 */
final class RegularRequestStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'registered' => 'Registrada',
            'validating' => 'En validación por operadores',
            'refused' => 'Rechazada',
            'error' => 'Con errores (los operadores corrigen)',
            'approved' => 'Aprobada, correo de descarga enviado',
            'generated' => 'Certificado descargado',
            'expired' => 'Expirada (50 días sin descarga)',
        ];
    }
}
