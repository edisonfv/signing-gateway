<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\Province;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ProvinceCityController extends Controller
{
    /**
     * GET /v1/provinces/{province}/cities?filter[name]=san&sort=name
     */
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
