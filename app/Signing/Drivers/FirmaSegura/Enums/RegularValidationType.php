<?php

namespace App\Signing\Drivers\FirmaSegura\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipos de validación de Firmas Regulares.
 *
 * Fuente: https://docs.firmaseguraec.com/tipos-de-validaci%C3%B3n-de-solicitudes-fr-1820390m0
 *
 * @method static self documentEntity()
 * @method static self biometrics()
 * @method static self olderPersonVideo()
 * @method static self legalRepresentativeDocuments()
 * @method static self rucNaturalPerson()
 */
final class RegularValidationType extends Enum
{
    protected static function values(): array
    {
        return [
            'documentEntity' => 'DOCUMENT_ENTITY',
            'biometrics' => 'BIOMETRICS',
            'olderPersonVideo' => 'OLDER_PERSON_VIDEO',
            'legalRepresentativeDocuments' => 'LEGAL_REPRESENTATIVE_DOCUMENTS',
            'rucNaturalPerson' => 'RUC_NATURAL_PERSON',
        ];
    }

    protected static function labels(): array
    {
        return [
            'documentEntity' => 'Validación de datos del documento de identificación',
            'biometrics' => 'Validación biométrica',
            'olderPersonVideo' => 'Validación de persona de la tercera edad',
            'legalRepresentativeDocuments' => 'Validación de documentos del representante legal',
            'rucNaturalPerson' => 'Validación de documentos para RUC de persona natural',
        ];
    }
}
