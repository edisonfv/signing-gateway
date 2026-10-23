<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ProvinceController extends Controller
{
    /**
     * GET /v1/provinces?filter[name]=pich&sort=name
     */
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
