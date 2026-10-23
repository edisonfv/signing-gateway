# Signing Gateway

Microservicio aislado que emite certificados de firma electrónica para el SaaS.
Hoy integra FirmaSeguraEC (Flujo por API); está diseñado para sumar otros proveedores.

El diseño completo está en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md).

## Stack

Laravel 13 · PHP 8.3+ · PostgreSQL · Sanctum · spatie/laravel-enum · spatie/laravel-permission · spatie/laravel-query-builder

## Puesta en marcha (desarrollo)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan gateway:keys:generate        # llaves de bóveda e índice ciego (distintas de APP_KEY)
php artisan migrate --seed               # tablas, roles y permisos, provincias y ciudades
php artisan gateway:provider:credentials sandbox   # pide el token de FirmaSegura con entrada oculta
```

## Clientes del SaaS

Cada cliente del SaaS (tenant) tiene su propia credencial:

```bash
php artisan gateway:tenant:create <external_id> "<nombre>"
php artisan gateway:client:create <external_id> "<nombre>" --ip=<ip o cidr> --public-key=<x25519 base64>
```

El segundo comando muestra **una sola vez** el token de acceso, el secreto de firma y el secreto de
notificaciones, que se configuran en el admin del SaaS. Cada petición debe llevar:

- `Authorization: Bearer <token>`
- `X-Timestamp` (epoch en segundos), `X-Nonce` (16–64 caracteres, único)
- `X-Signature`: HMAC-SHA256 con el secreto de firma sobre
  `MÉTODO\n/ruta?query\nX-Timestamp\nX-Nonce\nsha256(cuerpo)`, con la query exactamente como se envía

Prueba de conexión: `GET /api/v1/ping`.

El gateway notifica al SaaS (`--notification-url`) con `X-Gateway-Timestamp` y
`X-Gateway-Signature` = HMAC-SHA256 de `timestamp\ncuerpo` con el secreto de notificaciones.

## Catálogos

- `GET /api/v1/provinces?filter[name]=…&sort=name` y `GET /api/v1/provinces/{id}/cities`: en base de datos,
  cargadas por `ProvinceSeeder` desde `resources/catalogs/firmasegura/cities.json`.
- `GET /api/v1/catalogs/{application-types|periods}`: desde enums de spatie/laravel-enum.

## Producción

- Llaves y token del proveedor como secretos del servidor, o `.env` cifrado con `php artisan env:encrypt --env=production`.
- Workers de cola (`php artisan queue:work --queue=default,notifications`) y `php artisan schedule:work` (o cron).

## Pruebas

```bash
php artisan test
```
