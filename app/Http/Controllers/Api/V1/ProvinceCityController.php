<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\Province;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Catálogos', weight: 1)]
class ProvinceCityController extends Controller
{
    /**
     * Listar ciudades de una provincia
     *
     * Ciudades de la provincia indicada (UUID de `GET /v1/provinces`).
     */
    #[QueryParameter('filter[name]', 'Filtra por coincidencia parcial del nombre.', type: 'string', example: 'san')]
    #[QueryParameter('sort', 'Orden: `name` o `-name`.', type: 'string', default: 'name', example: 'name')]
    public function index(Province $province): AnonymousResourceCollection
    {
        $cities = QueryBuilder::for($province->cities())
            ->allowedFilters('name')
            ->allowedSorts('name')
            ->defaultSort('name')
            ->get();

        return CityResource::collection($cities);
    }
}
