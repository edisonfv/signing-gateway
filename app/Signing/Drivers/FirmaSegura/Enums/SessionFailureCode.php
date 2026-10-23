<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use App\Enums\ScreamingSnakeEnum;

/**
 * Errores técnicos de sesión (evento session.error).
 *
 * Fuente: https://docs.firmaseguraec.com/eventos-webhook-2069473m0
 *
 * @method static self passwordEncryptionFailed()
 * @method static self certificateGenerationFailed()
 * @method static self contractGenerationFailed()
 * @method static self sendFailed()
 */
final class SessionFailureCode extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'passwordEncryptionFailed' => 'Falló el cifrado de la contraseña',
            'certificateGenerationFailed' => 'Falló la generación del certificado',
            'contractGenerationFailed' => 'Falló la generación del contrato',
            'sendFailed' => 'Falló el envío',
        ];
    }
}
