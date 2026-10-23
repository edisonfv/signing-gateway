<?php

namespace App\Enums;

/**
 * Vigencias del certificado. ONE_DAY solo aplica a la firma transaccional de Tercero Vinculado.
 *
 * @method static self oneDay()
 * @method static self oneWeek()
 * @method static self oneMonth()
 * @method static self oneYear()
 * @method static self twoYears()
 * @method static self threeYears()
 * @method static self fourYears()
 * @method static self fiveYears()
 */
final class CertificatePeriod extends ScreamingSnakeEnum
{
    protected static function labels(): array
    {
        return [
            'oneDay' => '1 DÍA',
            'oneWeek' => '1 SEMANA',
            'oneMonth' => '1 MES',
            'oneYear' => '1 AÑO',
            'twoYears' => '2 AÑOS',
            'threeYears' => '3 AÑOS',
            'fourYears' => '4 AÑOS',
            'fiveYears' => '5 AÑOS',
        ];
    }
}
