<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Tipo de solicitante.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-tipos-de-solicitud-fr-1820347m0
 *
 * @method static self naturalPerson()
 * @method static self naturalPersonRuc()
 * @method static self legalRepresentative()
 */
final class ApplicationType extends Enum
{
    protected static function values(): array
    {
        return [
            'naturalPerson' => 'NATURAL_PERSON',
            'naturalPersonRuc' => 'NATURAL_PERSON_RUC',
            'legalRepresentative' => 'LEGAL_REPRESENTATIVE',
        ];
    }

    protected static function labels(): array
    {
        return [
            'naturalPerson' => 'Firma para persona natural',
            'naturalPersonRuc' => 'Firma para persona natural con RUC',
            'legalRepresentative' => 'Firma para representante legal',
        ];
    }
}
