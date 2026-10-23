<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Pasos del Flujo por API, en orden.
 *
 * Fuente: https://docs.firmaseguraec.com/flujo-por-api-2110187m0
 *
 * @method static self terms()
 * @method static self request()
 * @method static self civilRegistry()
 * @method static self livenessSession()
 * @method static self liveness()
 * @method static self issuance()
 */
final class SessionStep extends Enum
{
    protected static function values(): array
    {
        return [
            'terms' => 'TERMS',
            'request' => 'REQUEST',
            'civilRegistry' => 'CIVIL_REGISTRY',
            'livenessSession' => 'LIVENESS_SESSION',
            'liveness' => 'LIVENESS',
            'issuance' => 'ISSUANCE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'terms' => 'Aceptación de términos y condiciones',
            'request' => 'Solicitud de firma',
            'civilRegistry' => 'Validación en Registro Civil',
            'livenessSession' => 'Sesión de prueba de vida',
            'liveness' => 'Prueba de vida y comparación biométrica',
            'issuance' => 'Emisión del certificado',
        ];
    }
}
