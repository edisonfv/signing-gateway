<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApplicationType;
use App\Enums\CertificatePeriod;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;
use Spatie\Enum\Enum;

/**
 * Expone los catálogos que son enums para que el SaaS arme sus formularios.
 * Provincias y ciudades viven en base de datos (ProvinceController).
 */
#[Group('Catálogos', weight: 1)]
class CatalogController extends Controller
{
    /** @var array<string, class-string<Enum>> */
    private const CATALOGS = [
        'application-types' => ApplicationType::class,
        'periods' => CertificatePeriod::class,
    ];

    /**
     * Obtener catálogo
     *
     * Valores (`value`) y etiquetas (`label`) de un catálogo basado en enums.
     */
    #[PathParameter('catalog', 'Catálogo: `application-types` o `periods`.', type: 'string', example: 'periods')]
    public function __invoke(string $catalog): JsonResponse
    {
        abort_unless(isset(self::CATALOGS[$catalog]), 404);

        return response()->json(['data' => array_map(
            fn (Enum $case): array => ['value' => $case->value, 'label' => $case->label],
            self::CATALOGS[$catalog]::cases(),
        )]);
    }
}
