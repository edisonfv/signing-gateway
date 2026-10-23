<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tras auth:api: la credencial debe estar vigente, su tenant activo y la IP permitida.
 */
class EnsureActiveApiClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user();

        if (! $client instanceof ApiClient || ! $client->isActive()) {
            abort(403, 'Credencial revocada o tenant suspendido.');
        }

        if (! $client->allowsIp($request->ip())) {
            AuditLog::record('api_client.ip_denied', $client, context: ['ip' => $request->ip()]);

            abort(403, 'IP no permitida para esta credencial.');
        }

        return $next($request);
    }
}
