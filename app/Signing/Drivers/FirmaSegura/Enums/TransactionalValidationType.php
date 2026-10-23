<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipos de validación de Firmas Transaccionales.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-tipos-de-validaci%C3%B3n-ft-1820747m0
 *
 * @method static self civilRegistry()
 * @method static self frontDocument()
 * @method static self backDocument()
 * @method static self lifeProof()
 * @method static self certificate()
 */
final class TransactionalValidationType extends Enum
{
    protected static function values(): array
    {
        return [
            'civilRegistry' => 'CIVIL_REGISTRY',
            'frontDocument' => 'FRONT_DOCUMENT',
            'backDocument' => 'BACK_DOCUMENT',
            'lifeProof' => 'LIFE_PROOF',
            'certificate' => 'CERTIFICATE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'civilRegistry' => 'Datos del Registro Civil',
            'frontDocument' => 'Anverso de la cédula',
            'backDocument' => 'Reverso de la cédula',
            'lifeProof' => 'Prueba de vida',
            'certificate' => 'Certificado generado',
        ];
    }
}
