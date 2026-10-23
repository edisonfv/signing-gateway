<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Motivos de rechazo de sesión (evento session.rejected).
 *
 * Fuente: https://docs.firmaseguraec.com/eventos-webhook-2069473m0
 *
 * @method static self faceLivenessExhausted()
 * @method static self dataRejectedByUser()
 * @method static self otpTotalLimitExceeded()
 */
final class SessionRejectionCode extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'faceLivenessExhausted' => 'Intentos de prueba de vida agotados',
            'dataRejectedByUser' => 'El titular rechazó sus datos',
            'otpTotalLimitExceeded' => 'Se superó el límite de OTP',
        ];
    }
}
