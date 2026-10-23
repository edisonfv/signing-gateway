<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de solicitud de Tercero Vinculado.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estados-de-solicitud-tv-1859950m0
 *
 * @method static self registered()
 * @method static self error()
 * @method static self generated()
 * @method static self sent()
 * @method static self refused()
 * @method static self canceled()
 * @method static self validating()
 * @method static self revoked()
 */
final class LinkedPartnerStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'registered' => 'Registrada',
            'error' => 'Con errores, se puede reintentar',
            'generated' => 'Firma creada',
            'sent' => 'Entregada',
            'refused' => 'Rechazada',
            'canceled' => 'Cancelada',
            'validating' => 'Correo enviado al titular',
            'revoked' => 'Revocada',
        ];
    }
}
