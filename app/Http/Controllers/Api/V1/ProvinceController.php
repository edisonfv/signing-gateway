<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Catálogos', 'Datos de referencia para armar los formularios del SaaS.', weight: 1)]
class ProvinceController extends Controller
{
    /**
     * Listar provincias
     *
     * Provincias con el nombre exacto que espera el proveedor y la cantidad de ciudades.
     */
    #[QueryParameter('filter[name]', 'Filtra por coincidencia parcial del nombre.', type: 'string', example: 'santo')]
    #[QueryParameter('sort', 'Orden: `name` o `-name`.', type: 'string', default: 'name', example: 'name')]
    public function index(): AnonymousResourceCollection
    {
        $provinces = QueryBuilder::for(Province::class)
            ->withCount('cities')
            ->allowedFilters('name')
            ->allowedSorts('name')
            ->defaultSort('name')
            ->get();

        return ProvinceResource::collection($provinces);
    }
}
