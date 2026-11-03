<?php

/*
|--------------------------------------------------------------------------
| Signing Gateway
|--------------------------------------------------------------------------
|
| Llaves y parámetros de seguridad del gateway. Las llaves NUNCA van en el
| repositorio: en producción se inyectan como secretos (systemd
| LoadCredential o .env cifrado con `php artisan env:encrypt`).
| Generarlas con `php artisan gateway:keys:generate`.
|
*/

return [

    // Ambiente de proveedor que usa esta instancia: sandbox | production.
    'environment' => env('GATEWAY_PROVIDER_ENVIRONMENT', 'sandbox'),

    // Verificar el dígito verificador de cédula/RUC. Las personas de prueba del sandbox de
    // FirmaSegura (1801020101…) no lo cumplen, así que por defecto solo se exige en producción.
    'strict_identification' => (bool) env('GATEWAY_STRICT_IDENTIFICATION', env('GATEWAY_PROVIDER_ENVIRONMENT', 'sandbox') === 'production'),

    // Productos habilitados para un tenant sin condiciones comerciales propias (separados por coma).
    'default_products' => array_filter(explode(',', (string) env('GATEWAY_DEFAULT_PRODUCTS', 'HEADLESS'))),

    // Driver por defecto de SignatureProviderManager.
    'default_provider' => env('GATEWAY_DEFAULT_PROVIDER', 'firmasegura'),

    'keys' => [
        // Cifra el p12 y los secretos de proveedores y clientes (bóveda). Distinta de APP_KEY.
        'vault' => env('GATEWAY_VAULT_KEY'),
        'vault_previous' => array_filter(explode(',', (string) env('GATEWAY_VAULT_PREVIOUS_KEYS', ''))),

        // Cifra únicamente las contraseñas de los p12. Distinta de la llave de la bóveda.
        'vault_password' => env('GATEWAY_VAULT_PASSWORD_KEY'),
        'vault_password_previous' => array_filter(explode(',', (string) env('GATEWAY_VAULT_PASSWORD_PREVIOUS_KEYS', ''))),

        // HMAC para índices ciegos (buscar por cédula sin descifrarla).
        'blind_index' => env('GATEWAY_BLIND_INDEX_KEY'),
    ],

    'request_signature' => [
        // Diferencia máxima, en segundos, entre X-Timestamp y la hora del gateway.
        'tolerance' => (int) env('GATEWAY_SIGNATURE_TOLERANCE', 300),
    ],

    'api_clients' => [
        // Vigencia de los tokens de acceso de los clientes del SaaS, en días.
        'token_ttl_days' => (int) env('GATEWAY_CLIENT_TOKEN_TTL_DAYS', 365),
    ],

    'network' => [
        // IPs o CIDR desde los que se acepta la API (la IP de la plataforma en el túnel), separados por coma.
        // En producción es obligatoria: sin ella la API rechaza todo, aunque el firewall o nginx fallen.
        'allowed_sources' => array_values(array_filter(array_map('trim', explode(',', (string) env('GATEWAY_ALLOWED_SOURCES', ''))))),

        // Hosts a los que el gateway puede enviar notificaciones (siempre por HTTPS y sin seguir
        // redirecciones), separados por coma. En producción es obligatoria.
        'notification_hosts' => array_values(array_filter(array_map(fn (string $host) => strtolower(trim($host)), explode(',', (string) env('GATEWAY_NOTIFICATION_HOSTS', ''))))),

        // Certificado (o paquete de CA) en el que se confía al notificar, en vez de los del sistema.
        // Para una CA privada o el certificado autofirmado de un entorno local; nunca se desactiva la verificación.
        'notification_ca' => env('GATEWAY_NOTIFICATION_CA') ?: null,
    ],

    'limits' => [
        // Topes duros por defecto para cada tenant, independientes de los cupos del SaaS.
        'tenant_daily' => (int) env('GATEWAY_TENANT_DAILY_CAP', 50),
        'tenant_hourly' => (int) env('GATEWAY_TENANT_HOURLY_CAP', 10),
        // Tope global de solicitudes nuevas por hora (todas las cuentas).
        'global_hourly' => (int) env('GATEWAY_GLOBAL_HOURLY_CAP', 200),
        // Consultas de estado (tracking) por hora y cuenta de proveedor. FirmaSegura permite 1000.
        'provider_status_hourly' => (int) env('GATEWAY_PROVIDER_STATUS_HOURLY', 900),
    ],

    'reconciliation' => [
        // Cada cuánto se vuelve a consultar una solicitud abierta, según su antigüedad.
        'recent_interval_minutes' => (int) env('GATEWAY_RECONCILE_RECENT_MINUTES', 5),
        'stale_interval_minutes' => (int) env('GATEWAY_RECONCILE_STALE_MINUTES', 60),
        // A partir de cuántas horas una solicitud se considera antigua.
        'stale_after_hours' => (int) env('GATEWAY_RECONCILE_STALE_AFTER_HOURS', 24),
        // No se consulta una solicitud que cambió hace menos de esto (su job puede estar corriendo).
        'quiet_seconds' => (int) env('GATEWAY_RECONCILE_QUIET_SECONDS', 120),
    ],

    'webhooks' => [
        // Ruta pública de REENVÍO en la plataforma (https://app.midominio.com/provider-webhooks): el
        // proveedor avisa ahí y la plataforma pasa el aviso al gateway por el túnel. El gateway no
        // queda expuesto a internet. Sin ella no se envía callbackUrl: los cambios llegan por la reconciliación.
        'url' => env('GATEWAY_WEBHOOK_URL'),
    ],

    'retention' => [
        // Días que se conserva el p12 en la bóveda si nunca se retira.
        'undelivered_certificate_days' => (int) env('GATEWAY_UNDELIVERED_CERT_DAYS', 7),
        // Días que se conservan los datos personales tras cerrar la solicitud.
        'applicant_data_days' => (int) env('GATEWAY_APPLICANT_DATA_DAYS', 90),
    ],

];
