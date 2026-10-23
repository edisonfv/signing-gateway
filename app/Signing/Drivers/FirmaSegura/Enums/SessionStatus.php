<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Estados de sesión (Flujo por API y Simplificado). RETRYABLE y ERROR son pausas, no estados finales.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estadosetapas-de-sesi%C3%B3n-de-fs-2069469m0
 *
 * @method static self inProgress()
 * @method static self completed()
 * @method static self retryable()
 * @method static self error()
 * @method static self rejected()
 * @method static self expired()
 * @method static self cancelled()
 * @method static self failed()
 */
final class SessionStatus extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'inProgress' => 'En progreso',
            'completed' => 'Completada',
            'retryable' => 'Reintentable',
            'error' => 'Error',
            'rejected' => 'Rechazada',
            'expired' => 'Expirada',
            'cancelled' => 'Anulada',
            'failed' => 'Fallida',
        ];
    }
}
