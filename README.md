# Signing Gateway

Microservicio aislado que emite certificados de firma electrónica para el SaaS.
Hoy integra FirmaSeguraEC (Flujo por API); está diseñado para sumar otros proveedores.

El diseño completo está en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md).

## Stack

Laravel 13 · PHP 8.3+ · MySQL 8 · Sanctum · spatie/laravel-enum · spatie/laravel-permission · spatie/laravel-query-builder

## Puesta en marcha (desarrollo)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan gateway:keys:generate        # llaves de bóveda e índice ciego (distintas de APP_KEY)
php artisan migrate --seed               # tablas + roles y permisos
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
  `MÉTODO\n/ruta?query\nX-Timestamp\nX-Nonce\nsha256(cuerpo)`

Prueba de conexión: `GET /api/v1/ping`.

## Catálogos

Los enums de provincias y ciudades se generan desde `resources/catalogs/firmasegura/cities.json`:

```bash
php artisan gateway:catalogs:locations
```

## Pruebas

```bash
php artisan test
```
