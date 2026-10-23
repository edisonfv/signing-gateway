<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\LivenessCredentialController;
use App\Http\Controllers\Api\V1\LivenessResultController;
use App\Http\Controllers\Api\V1\LivenessSessionController;
use App\Http\Controllers\Api\V1\PingController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProvinceCityController;
use App\Http\Controllers\Api\V1\ProvinceController;
use App\Http\Controllers\Api\V1\RegularCorrectionController;
use App\Http\Controllers\Api\V1\SignatureSessionCancellationController;
use App\Http\Controllers\Api\V1\SignatureSessionController;
use App\Http\Controllers\Api\V1\SignatureSessionRefreshController;
use App\Http\Controllers\Api\V1\TermsController;
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
            Route::get('products', [ProductController::class, 'index'])->name('products.index');
            Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index');
            Route::get('provinces/{province}/cities', [ProvinceCityController::class, 'index'])->whereUuid('province')->name('provinces.cities.index');
        });

        $create = 'abilities:'.PermissionName::signatureSessionsCreate()->value;
        $view = 'abilities:'.PermissionName::signatureSessionsView()->value;

        Route::get('terms/current', [TermsController::class, 'show'])->middleware($create)->name('terms.current');

        Route::prefix('signature-sessions')->name('signature-sessions.')->group(function () use ($create, $view) {
            Route::get('/', [SignatureSessionController::class, 'index'])->middleware($view)->name('index');
            Route::post('/', [SignatureSessionController::class, 'store'])->middleware($create)->name('store');

            Route::whereUuid('signatureSession')->group(function () use ($create, $view) {
                Route::get('{signatureSession}', [SignatureSessionController::class, 'show'])->middleware($view)->name('show');
                Route::post('{signatureSession}/refresh', [SignatureSessionRefreshController::class, 'store'])->middleware($view)->name('refresh');
                Route::post('{signatureSession}/liveness-session', [LivenessSessionController::class, 'store'])->middleware($create)->name('liveness-session.store');
                Route::get('{signatureSession}/liveness-credentials', [LivenessCredentialController::class, 'show'])->middleware($create)->name('liveness-credentials.show');
                Route::post('{signatureSession}/liveness-result', [LivenessResultController::class, 'store'])->middleware($create)->name('liveness-result.store');
                Route::post('{signatureSession}/corrections', [RegularCorrectionController::class, 'store'])->middleware($create)->name('corrections.store');
                Route::post('{signatureSession}/cancel', [SignatureSessionCancellationController::class, 'store'])
                    ->middleware('abilities:'.PermissionName::signatureSessionsCancel()->value)
                    ->name('cancel');
                Route::get('{signatureSession}/certificate', [CertificateController::class, 'show'])
                    ->middleware('abilities:'.PermissionName::certificatesRetrieve()->value)
                    ->name('certificate.show');
            });
        });
    });
