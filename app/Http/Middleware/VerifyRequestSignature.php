<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Security\RequestSignature;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige la firma HMAC de cada petición, además del token.
 *
 * Un token copiado (logs, proxy) no basta: sin el secreto de firma no se puede
 * generar X-Signature, y timestamp + nonce impiden reenviar una petición capturada.
 */
class VerifyRequestSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ApiClient $client */
        $client = $request->user();

        $timestamp = $request->header(RequestSignature::HEADER_TIMESTAMP);
        $nonce = (string) $request->header(RequestSignature::HEADER_NONCE);
        $tolerance = config('gateway.request_signature.tolerance');

        if (! ctype_digit((string) $timestamp) || abs(now()->getTimestamp() - (int) $timestamp) > $tolerance) {
            abort(401, 'X-Timestamp ausente o fuera de la ventana permitida.');
        }

        if (strlen($nonce) < 16 || strlen($nonce) > 64) {
            abort(401, 'X-Nonce inválido.');
        }

        if (! RequestSignature::verify($client->signing_secret, $request)) {
            AuditLog::record('api_client.signature_invalid', $client);

            abort(401, 'Firma inválida.');
        }

        // Cache::add es atómico: solo el primer uso del nonce dentro de la ventana pasa.
        if (! Cache::add("request-nonce:{$client->getKey()}:{$nonce}", true, $tolerance * 2)) {
            AuditLog::record('api_client.replay_detected', $client);

            abort(401, 'Nonce ya utilizado.');
        }

        $client->markUsed($request);

        return $next($request);
    }
}
