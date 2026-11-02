<?php

use App\Enums\PermissionName;
use App\Http\Controllers\Api\V1\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Api\V1\Admin\IssuanceController as AdminIssuanceController;
use App\Http\Controllers\Api\V1\Admin\PlatformCredentialController as AdminPlatformCredentialController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Api\V1\Admin\TenantProductController as AdminTenantProductController;
use App\Http\Controllers\Api\V1\Admin\TenantStatusController as AdminTenantStatusController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\LivenessCredentialController;
use App\Http\Controllers\Api\V1\LivenessResultController;
use App\Http\Controllers\Api\V1\LivenessSessionController;
use App\Http\Controllers\Api\V1\PingController;
use App\Http\Controllers\Api\V1\Platform\ProductController as PlatformProductController;
use App\Http\Controllers\Api\V1\Platform\ProviderEventController;
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
| api.source (red de la plataforma) → auth:api (token Sanctum de un ApiClient) → api.client (vigente, tenant activo, IP)
| → api.signature (HMAC + timestamp + nonce) → abilities → throttle
*/
Route::prefix('v1')
    ->name('v1.')
    ->middleware(['api.source', 'auth:api', 'api.client', 'api.signature', 'throttle:api-client'])
    ->group(function () {
        Route::get('ping', PingController::class)->name('ping');

        // Alta de afiliados desde el backoffice del SaaS: solo la credencial de alta de la plataforma.
        Route::middleware('abilities:'.PermissionName::clientsManage()->value)->prefix('admin')->name('admin.')->group(function () {
            Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
            Route::post('platform-credential', [AdminPlatformCredentialController::class, 'store'])->name('platform-credential.store');
            Route::put('tenants/{externalId}', [AdminTenantController::class, 'update'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.update');
            Route::get('tenants/{externalId}/products', [AdminTenantProductController::class, 'show'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.products.show');
            Route::put('tenants/{externalId}/products', [AdminTenantProductController::class, 'update'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.products.update');
            Route::post('tenants/{externalId}/suspend', [AdminTenantStatusController::class, 'suspend'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.suspend');
            Route::post('tenants/{externalId}/block', [AdminTenantStatusController::class, 'block'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.block');
            Route::post('tenants/{externalId}/resume', [AdminTenantStatusController::class, 'resume'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.resume');
            Route::get('tenants/{externalId}/clients', [AdminClientController::class, 'index'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.clients.index');
            Route::post('tenants/{externalId}/clients', [AdminClientController::class, 'store'])->where('externalId', '[A-Za-z0-9_-]{1,64}')->name('tenants.clients.store');
            Route::delete('clients/{client}', [AdminClientController::class, 'destroy'])->whereUuid('client')->name('clients.destroy');
        });

        // Registro de emisiones: lo lee la credencial de alta o la de servicio (sincronización frecuente).
        Route::middleware('ability:'.PermissionName::clientsManage()->value.','.PermissionName::platformService()->value)->prefix('admin')->name('admin.')->group(function () {
            Route::get('issuances', [AdminIssuanceController::class, 'index'])->name('issuances.index');
            Route::get('issuances/summary', [AdminIssuanceController::class, 'summary'])->name('issuances.summary');
        });

        // Credencial de servicio de la plataforma: catálogo completo y reenvío de avisos del proveedor.
        Route::middleware('abilities:'.PermissionName::platformService()->value)->prefix('platform')->name('platform.')->group(function () {
            Route::get('products', [PlatformProductController::class, 'index'])->name('products.index');
            Route::post('provider-events/{provider}/{providerAccount}/{token}', [ProviderEventController::class, 'store'])
                ->where('provider', '[a-z0-9-]{1,30}')
                ->whereUuid('providerAccount')
                ->where('token', '[A-Za-z0-9]{1,64}')
                ->name('provider-events.store');
        });

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
