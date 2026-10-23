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

    'limits' => [
        // Topes duros por defecto para cada tenant, independientes de los cupos del SaaS.
        'tenant_daily' => (int) env('GATEWAY_TENANT_DAILY_CAP', 50),
        'tenant_hourly' => (int) env('GATEWAY_TENANT_HOURLY_CAP', 10),
        // Tope global de solicitudes nuevas por hora (todas las cuentas).
        'global_hourly' => (int) env('GATEWAY_GLOBAL_HOURLY_CAP', 200),
    ],

    'retention' => [
        // Días que se conserva el p12 en la bóveda si nunca se retira.
        'undelivered_certificate_days' => (int) env('GATEWAY_UNDELIVERED_CERT_DAYS', 7),
        // Días que se conservan los datos personales tras cerrar la solicitud.
        'applicant_data_days' => (int) env('GATEWAY_APPLICANT_DATA_DAYS', 90),
    ],

];
