# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- 162 files · ~37,856 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 925 nodes · 1925 edges · 80 communities (39 shown, 31 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 17 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `55a52ed4`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Http\Request
- SignatureSessionController.php
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Dependencias Composer
- ProductType
- ProviderAccount
- Tenant
- Province
- RolesAndPermissionsSeeder.php
- Controller
- ApiDocsServiceProvider.php
- Migraciones base
- RequestStatus
- Scripts Composer
- KeyRing
- Illuminate\Support\Str
- Enmascarado de logs
- Script de firma Apidog
- EcuadorianIdentification
- Modo de entrega
- FirmaSeguraDriver.php
- Cancelación automática FR
- Eventos CloudEvents
- Spatie\Enum\Laravel\Enum
- Tipos de certificado TV
- Signing Gateway
- Estados Firmas Regulares
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- Errores de sesión
- Fallos de sesión
- Rechazos de sesión
- Etapas de sesión
- Estados de sesión
- Tipo de términos
- Estados Transaccional
- Validación Transaccional
- Tipos de validación FT
- Configuración Scramble
- SignatureRequest
- .http
- TestCase
- DataProtectionTest.php
- ApiClient.php
- SignatureSessionFlowTest
- CertificateVaultEntry
- ApiClient
- Signing Gateway — Arquitectura
- TermsData
- ApiClientAuthenticationTest
- ClientNotification
- Illuminate\Database\Eloquent\Factories\Factory
- PermissionName
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- IssueApiClientCredentials.php
- AuditLog
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- VaultEncrypted
- ApplicationType
- CertificateData
- FakesFirmaSegura
- LogicException
- 7. Flujos clave
- Illuminate\Database\Eloquent\Relations\HasMany
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 78 edges
2. `RequestStatus` - 50 edges
3. `Tenant` - 41 edges
4. `ApiClient` - 35 edges
5. `AuditLog` - 35 edges
6. `ProductType` - 29 edges
7. `ProviderAccount` - 29 edges
8. `Controller` - 23 edges
9. `FirmaSeguraDriver` - 22 edges
10. `SessionStep` - 20 edges

## Surprising Connections (you probably didn't know these)
- `InteractsWithSignedApi` --references--> `Tenant`  [EXTRACTED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `LivenessResultController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/LivenessResultController.php → app/Http/Controllers/Controller.php
- `ProvinceCityController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/ProvinceCityController.php → app/Http/Controllers/Controller.php
- `ProvinceController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/ProvinceController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (80 total, 31 thin omitted)

### Community 0 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (13): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, RequestSignature, Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware (+5 more)

### Community 1 - "SignatureSessionController.php"
Cohesion: 0.13
Nodes (12): ProvinceCityController, ProvinceController, SignatureSessionController, CityResource, ProvinceResource, SignatureSessionResource, Dedoc\Scramble\Attributes\HeaderParameter, Dedoc\Scramble\Attributes\QueryParameter (+4 more)

### Community 2 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.25
Nodes (6): SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 4 - "ProductType"
Cohesion: 0.08
Nodes (15): CreateSignatureSession, Environment, ProductType, LimitExceededException, NoProviderAvailableException, TermsController, BlindIndex, TermsCatalog (+7 more)

### Community 5 - "ProviderAccount"
Cohesion: 0.11
Nodes (6): ProviderAccount, static, SignatureProvider, static, SignatureProviderManager, Illuminate\Support\Manager

### Community 6 - "Tenant"
Cohesion: 0.20
Nodes (5): LimitExceeded, Tenant, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels, UsageGuardTest

### Community 7 - "Province"
Cohesion: 0.16
Nodes (5): City, Province, CityFactory, Illuminate\Database\Eloquent\Factories\HasFactory, LocationCatalogTest

### Community 8 - "RolesAndPermissionsSeeder.php"
Cohesion: 0.10
Nodes (13): Permission, Role, DatabaseSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Log (+5 more)

### Community 9 - "Controller"
Cohesion: 0.13
Nodes (14): CatalogController, CertificateController, LivenessCredentialController, LivenessSessionController, PingController, SignatureSessionCancellationController, Controller, Dedoc\Scramble\Attributes\Group (+6 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.11
Nodes (15): self, ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response (+7 more)

### Community 11 - "Migraciones base"
Cohesion: 0.10
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "RequestStatus"
Cohesion: 0.06
Nodes (32): self, RequestStatus, SessionStep, self, ProviderRequestException, LivenessResultController, HandlesProviderSession, CreateProviderSession (+24 more)

### Community 13 - "Scripts Composer"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "KeyRing"
Cohesion: 0.12
Nodes (12): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, SetProviderCredentials, Provider, KeyRing, Encrypter (+4 more)

### Community 15 - "Illuminate\Support\Str"
Cohesion: 0.21
Nodes (4): Illuminate\Support\Str, Illuminate\Testing\TestResponse, Pdo\Mysql, InteractsWithSignedApi

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "EcuadorianIdentification"
Cohesion: 0.11
Nodes (11): StoreLivenessResultRequest, StoreSignatureSessionRequest, EcuadorianIdentification, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Foundation\Http\FormRequest, Illuminate\Translation\PotentiallyTranslatedString, PHPUnit\Framework\Attributes\DataProvider (+3 more)

### Community 20 - "FirmaSeguraDriver.php"
Cohesion: 0.12
Nodes (7): DrivesApiFlow, CivilRegistryResult, LivenessCredentials, LivenessResult, SessionData, Carbon\CarbonImmutable, Illuminate\Http\Client\Response

### Community 23 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.24
Nodes (3): LinkedPartnerStatus, RegularValidationErrorCode, Spatie\Enum\Laravel\Enum

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 50 - "SignatureRequest"
Cohesion: 0.15
Nodes (4): SignatureRequest, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Database\Eloquent\Relations\HasOne

### Community 52 - "TestCase"
Cohesion: 0.24
Nodes (9): ProvinceSeeder, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\Client\Request, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Queue (+1 more)

### Community 53 - "DataProtectionTest.php"
Cohesion: 0.21
Nodes (3): CertificatePeriod, Illuminate\Contracts\Encryption\DecryptException, DataProtectionTest

### Community 54 - "ApiClient.php"
Cohesion: 0.19
Nodes (7): PersonalAccessToken, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Traits\HasRoles, Symfony\Component\HttpFoundation\IpUtils

### Community 56 - "CertificateVaultEntry"
Cohesion: 0.21
Nodes (4): CertificateVaultEntry, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable, Illuminate\Support\Facades\Schedule

### Community 57 - "ApiClient"
Cohesion: 0.18
Nodes (3): ApiClient, Laravel\Sanctum\HasApiTokens, ClientNotificationDeliveryTest

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 59 - "TermsData"
Cohesion: 0.18
Nodes (3): self, TermsData, Illuminate\Support\Facades\Cache

### Community 62 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.27
Nodes (5): ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.22
Nodes (9): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7 API interna para el Flujo por API, 16.8 Preguntas para FirmaSegura (Flujo por API) (+1 more)

### Community 68 - "AuditLog"
Cohesion: 0.29
Nodes (3): AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 74 - "LogicException"
Cohesion: 0.50
Nodes (3): InvalidStatusTransition, self, LogicException

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

## Knowledge Gaps
- **115 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+110 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 341 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **31 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `SignatureSessionController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `ProductType`, `ProviderAccount`, `Tenant`, `ApplicationType`, `CertificateData`, `Controller`, `LogicException`, `ApiDocsServiceProvider.php`, `RequestStatus`, `.http`, `FirmaSeguraDriver.php`, `TestCase`, `DataProtectionTest.php`, `SignatureSessionFlowTest`, `TermsData`, `ClientNotification`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Why does `RequestStatus` connect `RequestStatus` to `Illuminate\Database\Eloquent\Concerns\HasUuids`, `ProductType`, `Tenant`, `ApplicationType`, `Controller`, `LogicException`, `SignatureRequest`, `TestCase`, `DataProtectionTest.php`, `Spatie\Enum\Laravel\Enum`, `CertificateVaultEntry`?**
  _High betweenness centrality (0.065) - this node is a cross-community bridge._
- **Why does `ProductType` connect `ProductType` to `SignatureSessionController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `ProviderAccount`, `Tenant`, `ApplicationType`, `RequestStatus`, `Modo de entrega`, `FirmaSeguraDriver.php`, `TestCase`, `DataProtectionTest.php`, `Spatie\Enum\Laravel\Enum`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _115 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.13105413105413105 - nodes in this community are weakly interconnected._
- **Should `SignatureSessionController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.12923076923076923 - nodes in this community are weakly interconnected._
- **Should `Dependencias Composer` be split into smaller, more focused modules?**
  _Cohesion score 0.0425531914893617 - nodes in this community are weakly interconnected._