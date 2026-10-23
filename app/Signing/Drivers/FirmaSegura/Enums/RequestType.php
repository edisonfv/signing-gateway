<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Origen de una solicitud consultada por estado.
 *
 * Fuente: https://docs.firmaseguraec.com/tipos-de-solicitudes-1868491m0
 *
 * @method static self linkedPartnerRequest()
 * @method static self regularRequest()
 */
final class RequestType extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'linkedPartnerRequest' => 'Solicitud de Tercero Vinculado',
            'regularRequest' => 'Solicitud de Firmas Regulares',
        ];
    }
}
