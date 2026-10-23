<?php

namespace App\Security;

use Illuminate\Http\Request;

/**
 * Firma HMAC-SHA256 de las peticiones SaaS → gateway.
 *
 * Cadena canónica (una línea por elemento):
 *   MÉTODO
 *   /ruta?query (query decodificada, en el orden enviado)
 *   X-Timestamp (epoch en segundos)
 *   X-Nonce
 *   sha256 hex del cuerpo crudo
 */
class RequestSignature
{
    public const HEADER_TIMESTAMP = 'X-Timestamp';

    public const HEADER_NONCE = 'X-Nonce';

    public const HEADER_SIGNATURE = 'X-Signature';

    public static function canonical(string $method, string $pathWithQuery, string $timestamp, string $nonce, string $body): string
    {
        return implode("\n", [strtoupper($method), $pathWithQuery, $timestamp, $nonce, hash('sha256', $body)]);
    }

    public static function sign(#[\SensitiveParameter] string $secret, string $canonical): string
    {
        return hash_hmac('sha256', $canonical, $secret);
    }

    public static function canonicalFromRequest(Request $request): string
    {
        // Query en el orden en que llegó pero decodificada, para que la firma no dependa de si el
        // cliente codifica o no caracteres como [ ] (filter[name] vs filter%5Bname%5D).
        $query = rawurldecode((string) $request->server->get('QUERY_STRING', ''));

        return self::canonical(
            $request->getMethod(),
            '/'.ltrim($request->path(), '/').($query ? "?{$query}" : ''),
            (string) $request->header(self::HEADER_TIMESTAMP),
            (string) $request->header(self::HEADER_NONCE),
            $request->getContent(),
        );
    }

    /**
     * Firma de las notificaciones gateway → SaaS: HMAC-SHA256 de "timestamp\ncuerpo"
     * con el secreto de notificaciones del cliente.
     */
    public static function signNotification(#[\SensitiveParameter] string $secret, string $timestamp, string $body): string
    {
        return hash_hmac('sha256', "{$timestamp}\n{$body}", $secret);
    }

    public static function verify(#[\SensitiveParameter] string $secret, Request $request): bool
    {
        $signature = (string) $request->header(self::HEADER_SIGNATURE);

        return $signature !== '' && hash_equals(self::sign($secret, self::canonicalFromRequest($request)), $signature);
    }
}
