<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tras auth:api: la credencial debe estar vigente, su tenant activo y la IP permitida. Las
 * credenciales de la plataforma (sin tenant) solo llegan a las rutas de la plataforma.
 */
class EnsureActiveApiClient
{
    private const ALLOWED_WHILE_SUSPENDED = [
        'v1.ping',
        'v1.signature-sessions.index',
        'v1.signature-sessions.show',
        'v1.signature-sessions.certificate.show',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->user();

        if (! $client instanceof ApiClient || ! $client->isActive()) {
            abort(403, 'Credencial revocada o afiliado bloqueado.');
        }

        // La plataforma no es un afiliado: nunca opera solicitudes ni catálogos de un tenant.
        if ($client->isPlatform() && ! $request->routeIs('v1.ping', 'v1.admin.*', 'v1.platform.*')) {
            abort(403, 'Una credencial de la plataforma solo usa /v1/admin y /v1/platform.');
        }

        // Suspendido (comercial): solo puede consultar y retirar lo que ya se emitió y pagó.
        if ($client->tenant?->isSuspended() && ! ($request->isMethod('GET') && $request->routeIs(...self::ALLOWED_WHILE_SUSPENDED))) {
            abort(403, 'Afiliado suspendido: solo puede consultar sus solicitudes y retirar certificados ya emitidos.');
        }

        if (! $client->allowsIp($request->ip())) {
            AuditLog::record('api_client.ip_denied', $client, context: ['ip' => $request->ip()]);

            abort(403, 'IP no permitida para esta credencial.');
        }

        return $next($request);
    }
}
