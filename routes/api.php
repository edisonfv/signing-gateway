<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\PingController;
use Illuminate\Support\Facades\Route;

/*
| API interna para los clientes del SaaS. Orden de protección:
| auth:api (token Sanctum de un ApiClient) → api.client (vigente, tenant activo, IP)
| → api.signature (HMAC + timestamp + nonce) → abilities → throttle
*/
Route::prefix('v1')
    ->middleware(['auth:api', 'api.client', 'api.signature', 'throttle:api-client'])
    ->group(function () {
        Route::get('ping', PingController::class);

        Route::get('catalogs/{catalog}', CatalogController::class)
            ->middleware('abilities:'.PermissionName::catalogsView()->value);
    });
