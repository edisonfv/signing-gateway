<?php

use App\Http\Controllers\Webhooks\FirmaSeguraWebhookController;
use Illuminate\Support\Facades\Route;

/*
| Webhooks públicos de los proveedores: sin sesión ni CSRF. En producción nginx expone
| solo esta ruta hacia afuera (la API interna sigue en el túnel).
*/
Route::post('webhooks/firmasegura/{providerAccount}/{token}', FirmaSeguraWebhookController::class)
    ->whereUuid('providerAccount')
    ->name('webhooks.firmasegura');
