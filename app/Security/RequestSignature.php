<?php

namespace App\Security;

use Illuminate\Http\Request;

/**
 * Firma HMAC-SHA256 de las peticiones SaaS → gateway.
 *
 * Cadena canónica (una línea por elemento):
 *   MÉTODO
 *   /ruta?query
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
        $query = $request->getQueryString();

        return self::canonical(
            $request->getMethod(),
            '/'.ltrim($request->path(), '/').($query ? "?{$query}" : ''),
            (string) $request->header(self::HEADER_TIMESTAMP),
            (string) $request->header(self::HEADER_NONCE),
            $request->getContent(),
        );
    }

    public static function verify(#[\SensitiveParameter] string $secret, Request $request): bool
    {
        $signature = (string) $request->header(self::HEADER_SIGNATURE);

        return $signature !== '' && hash_equals(self::sign($secret, self::canonicalFromRequest($request)), $signature);
    }
}
