<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

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
final class SessionFailureCode extends Enum
{
    protected static function values(): array
    {
        return [
            'passwordEncryptionFailed' => 'PASSWORD_ENCRYPTION_FAILED',
            'certificateGenerationFailed' => 'CERTIFICATE_GENERATION_FAILED',
            'contractGenerationFailed' => 'CONTRACT_GENERATION_FAILED',
            'sendFailed' => 'SEND_FAILED',
        ];
    }

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
