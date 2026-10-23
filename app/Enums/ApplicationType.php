<?php

namespace App\Enums;

/**
 * @method static self naturalPerson()
 * @method static self naturalPersonRuc()
 * @method static self legalRepresentative()
 */
final class ApplicationType extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'naturalPerson' => 'Firma para persona natural',
            'naturalPersonRuc' => 'Firma para persona natural con RUC',
            'legalRepresentative' => 'Firma para representante legal',
        ];
    }
}
