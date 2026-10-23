<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipos de evento CloudEvents del Flujo Simplificado.
 *
 * Fuente: https://docs.firmaseguraec.com/eventos-webhook-2069473m0
 *
 * @method static self sessionCompleted()
 * @method static self sessionRejected()
 * @method static self sessionError()
 * @method static self sessionCancelled()
 * @method static self sessionExpired()
 */
final class CloudEventType extends Enum
{
    protected static function values(): array
    {
        return [
            'sessionCompleted' => 'ec.firmasegura.signature.session.completed',
            'sessionRejected' => 'ec.firmasegura.signature.session.rejected',
            'sessionError' => 'ec.firmasegura.signature.session.error',
            'sessionCancelled' => 'ec.firmasegura.signature.session.cancelled',
            'sessionExpired' => 'ec.firmasegura.signature.session.expired',
        ];
    }

    protected static function labels(): array
    {
        return [
            'sessionCompleted' => 'Sesión completada',
            'sessionRejected' => 'Sesión rechazada',
            'sessionError' => 'Sesión con error',
            'sessionCancelled' => 'Sesión cancelada',
            'sessionExpired' => 'Sesión expirada',
        ];
    }
}
