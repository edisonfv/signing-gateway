<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * La API solo responde a la red de la plataforma (gateway.network.allowed_sources), antes de
 * cualquier otra comprobación. Es la segunda barrera: si el firewall o nginx quedan mal
 * configurados, la API sigue cerrada. En producción, sin lista configurada, rechaza todo.
 */
class RestrictSourceNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('gateway.network.allowed_sources');

        if ($allowed === []) {
            if (app()->isProduction()) {
                Log::critical('GATEWAY_ALLOWED_SOURCES no está configurada: la API rechaza todas las peticiones.');
                abort(403, 'Origen no permitido.');
            }

            return $next($request);
        }

        if (! IpUtils::checkIp((string) $request->ip(), $allowed)) {
            Log::warning('Petición a la API desde un origen no permitido.', ['ip' => $request->ip(), 'path' => $request->path()]);
            abort(403, 'Origen no permitido.');
        }

        return $next($request);
    }
}
