<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\PingController;
use App\Http\Controllers\Api\V1\ProvinceCityController;
use App\Http\Controllers\Api\V1\ProvinceController;
use Illuminate\Support\Facades\Route;

/*
| API interna para los clientes del SaaS. Orden de protección:
| auth:api (token Sanctum de un ApiClient) → api.client (vigente, tenant activo, IP)
| → api.signature (HMAC + timestamp + nonce) → abilities → throttle
*/
Route::prefix('v1')
    ->name('v1.')
    ->middleware(['auth:api', 'api.client', 'api.signature', 'throttle:api-client'])
    ->group(function () {
        Route::get('ping', PingController::class)->name('ping');

        Route::middleware('abilities:'.PermissionName::catalogsView()->value)->group(function () {
            Route::get('catalogs/{catalog}', CatalogController::class)->name('catalogs.show');
            Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index');
            Route::get('provinces/{province}/cities', [ProvinceCityController::class, 'index'])->whereUuid('province')->name('provinces.cities.index');
        });
    });
