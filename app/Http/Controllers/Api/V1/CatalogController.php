<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Enums\City;
use App\Enums\Province;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Spatie\Enum\Enum;

/**
 * Expone los catálogos (enums) para que el SaaS arme sus formularios.
 */
class CatalogController extends Controller
{
    /** @var array<string, class-string<Enum>> */
    private const CATALOGS = [
        'application-types' => ApplicationType::class,
        'periods' => CertificatePeriod::class,
        'provinces' => Province::class,
    ];

    public function __invoke(string $catalog): JsonResponse
    {
        if ($catalog === 'cities') {
            return response()->json(['data' => array_map(fn (City $city) => [
                'value' => $city->value,
                'label' => $city->providerName(),
                'province' => $city->province()->value,
            ], City::cases())]);
        }

        abort_unless(isset(self::CATALOGS[$catalog]), 404);

        return response()->json(['data' => array_map(
            fn (Enum $case) => ['value' => $case->value, 'label' => $case->label],
            self::CATALOGS[$catalog]::cases(),
        )]);
    }
}
