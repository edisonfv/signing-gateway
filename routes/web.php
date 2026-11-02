<?php

use App\Http\Controllers\Dev\LivenessTestController;
use Illuminate\Support\Facades\Route;

/*
| El gateway no tiene interfaz: solo la API (routes/api.php) y /up para el monitoreo.
*/

/*
| Herramientas de desarrollo: SOLO se registran en el entorno local.
*/
if (app()->isLocal()) {
    Route::prefix('dev/liveness')->name('dev.liveness.')->group(function () {
        Route::get('{signatureRequest?}', [LivenessTestController::class, 'show'])->whereUuid('signatureRequest')->name('show');
        Route::post('{signatureRequest}/session', [LivenessTestController::class, 'session'])->whereUuid('signatureRequest')->name('session');
        Route::post('{signatureRequest}/result', [LivenessTestController::class, 'result'])->whereUuid('signatureRequest')->name('result');
    });
}
