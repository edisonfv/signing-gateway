<?php

use App\Http\Middleware\AssignRequestContext;
use App\Http\Middleware\EnsureActiveApiClient;
use App\Http\Middleware\RestrictSourceNetwork;
use App\Http\Middleware\VerifyRequestSignature;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

// El .env se carga en $_ENV/$_SERVER de esta petición, nunca con putenv(): en un PHP compartido por
// varios sitios (Apache mod_php en Laragon) putenv es del proceso entero, y las variables de otra app
// (p. ej. CACHE_STORE de la plataforma) terminaban en el gateway o al revés. Las variables reales del
// sistema (systemd, PHP-FPM) se siguen leyendo desde $_SERVER.
Env::disablePutenv();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(AssignRequestContext::class);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'api.client' => EnsureActiveApiClient::class,
            'api.signature' => VerifyRequestSignature::class,
            'api.source' => RestrictSourceNetwork::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
