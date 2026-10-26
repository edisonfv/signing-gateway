# Signing Gateway

Microservicio aislado que emite certificados de firma electrónica para el SaaS.
Hoy integra FirmaSeguraEC (Flujo por API); está diseñado para sumar otros proveedores.

El diseño completo está en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md). La guía para integrar el SaaS está en [docs/INTEGRACION_SAAS.md](docs/INTEGRACION_SAAS.md).

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

Una credencial que solo lee el catálogo (la de la plataforma del SaaS) se limita con `--ability`, y
cualquier credencial se revoca al instante:

```bash
php artisan gateway:client:create plataforma "Catálogo (solo lectura)" --ability=catalogs.view
php artisan gateway:client:revoke <external_id> --list
php artisan gateway:client:revoke <id del cliente>
```

Productos contratados por tenant (condiciones comerciales). Sin configurar, aplica `GATEWAY_DEFAULT_PRODUCTS`:

```bash
php artisan gateway:tenant:products <external_id> HEADLESS REGULAR
```

El SaaS consulta `GET /api/v1/products` para saber qué flujos ofrecer y con qué requisitos, y envía
`product` (`HEADLESS` = Flujo por API con prueba de vida, `REGULAR` = Firmas Regulares con documentos)
al crear la solicitud.

El comando `gateway:client:create` muestra **una sola vez** el token de acceso, el secreto de firma y el secreto de
notificaciones, que se configuran en el admin del SaaS. Cada petición debe llevar:

- `Authorization: Bearer <token>`
- `X-Timestamp` (epoch en segundos), `X-Nonce` (16–64 caracteres, único)
- `X-Signature`: HMAC-SHA256 con el secreto de firma sobre
  `MÉTODO\n/ruta?query\nX-Timestamp\nX-Nonce\nsha256(cuerpo)`, con la query decodificada y en el orden enviado

Prueba de conexión: `GET /api/v1/ping`.

El gateway notifica al SaaS (`--notification-url`) con `X-Gateway-Timestamp` y
`X-Gateway-Signature` = HMAC-SHA256 de `timestamp\ncuerpo` con el secreto de notificaciones.

## Catálogos

- `GET /api/v1/provinces?filter[name]=…&sort=name` y `GET /api/v1/provinces/{id}/cities`: en base de datos,
  cargadas por `ProvinceSeeder` desde `resources/catalogs/firmasegura/cities.json`.
- `GET /api/v1/catalogs/{application-types|periods}`: desde enums de spatie/laravel-enum.

## Documentación y pruebas en Apidog

La especificación OpenAPI la genera Scramble desde el código:

- En local: `https://signing-gateway.local.com/docs/api` (UI) y `/docs/api.json`.
- Archivo para importar: `docs/openapi.json`, que se regenera con `php artisan scramble:export`.

Pasos en Apidog:

1. **Importar**: Configuración del proyecto → Importar datos → OpenAPI/Swagger → `docs/openapi.json`,
   con "sobrescribir los existentes" en cada actualización.
2. **Entorno** `local`: URL base `https://signing-gateway.local.com/api`; variables `token` y
   `signingSecret` como **valor local** (no se sincronizan a la nube).
3. **Firma**: pegar `docs/apidog/firma-hmac.js` como script personalizado en el pre-procesador
   del proyecto. Agrega `Authorization`, `X-Timestamp`, `X-Nonce` y `X-Signature` a cada petición.
4. Probar con **Diagnóstico → Probar credencial** (`GET /v1/ping`).

## Grafo de conocimiento (graphify)

`graphify-out/` contiene el grafo del proyecto para agentes de IA: `graph.json` y `GRAPH_REPORT.md`.

- Se actualiza solo después de cada `git commit` (hook post-commit, solo código, sin costo de LLM).
- Consultas: `graphify query "¿cómo se valida la firma HMAC?"`, `graphify path "ApiClient" "UsageGuard"`, `graphify explain "KeyRing"`.
- Si cambian los documentos (`docs/`, `README.md`), ejecutar `/graphify --update` desde Claude Code.
- `.graphifyignore` excluye lo generado (Boost, OpenAPI, `storage/`…). Instalación en otra máquina:
  `uv tool install graphifyy`, luego `graphify hook install` y `graphify claude install`.

## Producción

- Llaves y token del proveedor como secretos del servidor, o `.env` cifrado con `php artisan env:encrypt --env=production`.
- Workers de cola (`php artisan queue:work --queue=interactive,default,notifications`) y `php artisan schedule:work` (o cron).

## Pruebas

```bash
php artisan test
```
