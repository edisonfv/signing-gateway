<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Etapas de sesión, en orden.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-estadosetapas-de-sesi%C3%B3n-de-fs-2069469m0
 *
 * @method static self validation()
 * @method static self initialization()
 * @method static self biometric()
 * @method static self confirmation()
 * @method static self certificate()
 */
final class SessionStage extends Enum
{
    protected static function values(): array
    {
        return [
            'validation' => 'VALIDATION',
            'initialization' => 'INITIALIZATION',
            'biometric' => 'BIOMETRIC',
            'confirmation' => 'CONFIRMATION',
            'certificate' => 'CERTIFICATE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'validation' => 'Validación',
            'initialization' => 'Inicialización',
            'biometric' => 'Biometría',
            'confirmation' => 'Confirmación',
            'certificate' => 'Certificado',
        ];
    }
}
