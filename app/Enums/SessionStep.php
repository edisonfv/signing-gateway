<?php

namespace App\Enums;

/**
 * Pasos del Flujo por API, en orden.
 *
 * @method static self terms()
 * @method static self request()
 * @method static self civilRegistry()
 * @method static self livenessSession()
 * @method static self liveness()
 * @method static self issuance()
 */
final class SessionStep extends ScreamingSnakeEnum
{
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
