<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Vigencia del certificado. ONE_DAY solo aplica a la firma transaccional de Tercero Vinculado.
 *
 * Fuente: https://docs.firmaseguraec.com/lista-de-periodos-de-firmas-1820346m0
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
final class CertificatePeriod extends Enum
{
    protected static function values(): array
    {
        return [
            'oneDay' => 'ONE_DAY',
            'oneWeek' => 'ONE_WEEK',
            'oneMonth' => 'ONE_MONTH',
            'oneYear' => 'ONE_YEAR',
            'twoYears' => 'TWO_YEARS',
            'threeYears' => 'THREE_YEARS',
            'fourYears' => 'FOUR_YEARS',
            'fiveYears' => 'FIVE_YEARS',
        ];
    }

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
