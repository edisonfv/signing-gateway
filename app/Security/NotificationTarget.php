<?php

namespace App\Security;

/**
 * Adónde puede el gateway enviar notificaciones. Evita que una URL de notificación lo convierta
 * en un proxy hacia su red (SSRF): solo HTTPS y solo hosts de gateway.network.notification_hosts.
 * Sin lista configurada se acepta cualquier host HTTPS, salvo en producción, donde no se acepta ninguno.
 */
final class NotificationTarget
{
    public static function isAllowed(?string $url): bool
    {
        if ($url === null || parse_url($url, PHP_URL_SCHEME) !== 'https') {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowed = config('gateway.network.notification_hosts');

        if ($allowed === []) {
            return $host !== '' && ! app()->isProduction();
        }

        return in_array($host, $allowed, true);
    }
}
