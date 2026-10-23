# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- 162 files · ~37,992 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 934 nodes · 1907 edges · 71 communities (35 shown, 26 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 48 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `ea5336b5`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Tests\Concerns\InteractsWithSignedApi
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Dependencias Composer
- App\Models\ProviderAccount
- Tenant
- SignatureRequest
- Province
- .createRequest
- LimitExceededException
- ClientNotification
- Migraciones base
- App\Models\SignatureRequest
- Scripts Composer
- AuditLog
- UserFactory.php
- Enmascarado de logs
- Script de firma Apidog
- ApplicationType
- Modo de entrega
- .http
- Spatie\Enum\Laravel\Enum
- Eventos CloudEvents
- LinkedPartnerStatus
- Tipos de certificado TV
- Signing Gateway
- Estados Firmas Regulares
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- Errores de sesión
- Fallos de sesión
- LogMaskingTest
- Etapas de sesión
- Estados de sesión
- Tipo de términos
- Estados Transaccional
- Validación Transaccional
- Tipos de validación FT
- Configuración Scramble
- App\Models\ApiClient
- Illuminate\Support\Str
- Illuminate\Foundation\Testing\RefreshDatabase
- ApiClient
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Model
- Signing Gateway — Arquitectura
- ApiClientAuthenticationTest
- sanctum.php
- KeyRing
- App\Actions\IssueApiClientCredentials
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- static
- 7. Flujos clave
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor

## God Nodes (most connected - your core abstractions)
1. `Tenant` - 35 edges
2. `AuditLog` - 31 edges
3. `ApiClient` - 25 edges
4. `SignatureRequest` - 24 edges
5. `FirmaSeguraDriver` - 22 edges
6. `SignatureSessionFlowTest` - 19 edges
7. `ProductType` - 19 edges
8. `IssueCertificate` - 18 edges
9. `Province` - 18 edges
10. `Signing Gateway — Arquitectura` - 17 edges

## Surprising Connections (you probably didn't know these)
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `ApiClientAuthenticationTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/ApiClientAuthenticationTest.php → tests/Concerns/InteractsWithSignedApi.php
- `LocationCatalogTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/LocationCatalogTest.php → tests/Concerns/InteractsWithSignedApi.php
- `LogMaskingTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/LogMaskingTest.php → tests/TestCase.php
- `ApiClientAuthenticationTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ApiClientAuthenticationTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (71 total, 26 thin omitted)

### Community 0 - "Tests\Concerns\InteractsWithSignedApi"
Cohesion: 0.21
Nodes (6): App\Models\Tenant, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Testing\TestResponse, Tenant, Tests\Concerns\InteractsWithSignedApi, InteractsWithSignedApi

### Community 1 - "Illuminate\Http\Request"
Cohesion: 0.05
Nodes (38): App\Http\Controllers\Api\V1\CatalogController, CatalogController, App\Http\Controllers\Api\V1\PingController, PingController, App\Http\Controllers\Api\V1\ProvinceCityController, ProvinceCityController, App\Http\Controllers\Api\V1\ProvinceController, ProvinceController (+30 more)

### Community 2 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.15
Nodes (10): App\Models\ClientNotification, App\Models\PersonalAccessToken, PersonalAccessToken, SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Concerns\HasUuids (+2 more)

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 4 - "App\Models\ProviderAccount"
Cohesion: 0.08
Nodes (19): CreateSignatureSession, Environment, ProductType, NoProviderAvailableException, App\Models\ProviderAccount, ProviderAccount, App\Security\BlindIndex, TermsCatalog (+11 more)

### Community 5 - "Tenant"
Cohesion: 0.20
Nodes (7): LimitExceeded, Tenant, UsageGuard, DateTimeInterface, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels, Illuminate\Support\Facades\Event

### Community 6 - "SignatureRequest"
Cohesion: 0.12
Nodes (9): self, RequestStatus, InvalidStatusTransition, self, SignatureRequest, Illuminate\Contracts\Encryption\DecryptException, Illuminate\Database\Eloquent\Relations\HasOne, LogicException (+1 more)

### Community 7 - "Province"
Cohesion: 0.13
Nodes (10): App\Models\City, City, Province, CityFactory, ProvinceFactory, Database\Seeders\ProvinceSeeder, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 10 - "ClientNotification"
Cohesion: 0.07
Nodes (17): ClientNotification, self, ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter (+9 more)

### Community 11 - "Migraciones base"
Cohesion: 0.10
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "App\Models\SignatureRequest"
Cohesion: 0.05
Nodes (50): App\Enums\ProductType, App\Enums\RequestStatus, App\Enums\SessionStep, SessionStep, ProviderRequestException, CertificateController, LivenessCredentialController, LivenessResultController (+42 more)

### Community 13 - "Scripts Composer"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "AuditLog"
Cohesion: 0.11
Nodes (12): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, SetProviderCredentials, Provider, AuditLog, self (+4 more)

### Community 15 - "UserFactory.php"
Cohesion: 0.40
Nodes (3): static, UserFactory, Illuminate\Support\Facades\Hash

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "ApplicationType"
Cohesion: 0.08
Nodes (13): App\Enums\ApplicationType, ApplicationType, App\Enums\CertificatePeriod, CertificatePeriod, StoreLivenessResultRequest, StoreSignatureSessionRequest, Illuminate\Foundation\Http\FormRequest, Spatie\Enum\Laravel\Rules\EnumRule (+5 more)

### Community 20 - ".http"
Cohesion: 0.06
Nodes (13): DrivesApiFlow, CertificateData, CivilRegistryResult, LivenessCredentials, LivenessResult, SessionData, self, TermsData (+5 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.28
Nodes (3): AutoCancellationReason, SessionRejectionCode, Spatie\Enum\Laravel\Enum

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 32 - "LogMaskingTest"
Cohesion: 0.29
Nodes (3): Illuminate\Support\Facades\File, Illuminate\Support\Facades\Log, LogMaskingTest

### Community 50 - "App\Models\ApiClient"
Cohesion: 0.52
Nodes (4): App\Models\ApiClient, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Symfony\Component\HttpFoundation\IpUtils

### Community 52 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.23
Nodes (9): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\Bus, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Queue, Tests\Concerns\FakesFirmaSegura, Tests\TestCase (+1 more)

### Community 54 - "ApiClient"
Cohesion: 0.17
Nodes (6): ApiClient, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens, Spatie\Permission\Traits\HasRoles

### Community 56 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.16
Nodes (8): VaultEncrypted, App\Models\CertificateVaultEntry, CertificateVaultEntry, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Prunable, Illuminate\Support\Facades\Schedule

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 61 - "sanctum.php"
Cohesion: 0.40
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

### Community 62 - "KeyRing"
Cohesion: 0.17
Nodes (8): self, BlindIndex, App\Security\KeyRing, KeyRing, Encrypter, Illuminate\Encryption\Encrypter, Illuminate\Http\Client\Response, RuntimeException

### Community 63 - "App\Actions\IssueApiClientCredentials"
Cohesion: 0.08
Nodes (18): App\Actions\IssueApiClientCredentials, App\Enums\PermissionName, PermissionName, RoleName, Permission, Role, App\Security\SealedBox, SealedBox (+10 more)

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.22
Nodes (9): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7 API interna para el Flujo por API, 16.8 Preguntas para FirmaSegura (Flujo por API) (+1 more)

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

## Knowledge Gaps
- **115 isolated node(s):** `1. Decisiones base`, `2. Topología`, `3.1 Productos internos (independientes del proveedor)`, `3.2 Contratos por capacidad`, `3.3 Resolución de drivers` (+110 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 352 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **26 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Tenant` connect `Tenant` to `Tests\Concerns\InteractsWithSignedApi`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `App\Models\ProviderAccount`, `SignatureRequest`, `.createRequest`, `ClientNotification`, `AuditLog`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Model`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `App\Models\ProviderAccount`, `Tenant`, `SignatureRequest`, `App\Models\SignatureRequest`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Database\Eloquent\Model`, `ApiClientAuthenticationTest`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Why does `ProductType` connect `App\Models\ProviderAccount` to `Tenant`, `SignatureRequest`, `.createRequest`, `App\Models\SignatureRequest`, `Modo de entrega`, `Spatie\Enum\Laravel\Enum`?**
  _High betweenness centrality (0.031) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `AuditLog` (e.g. with `.show()` and `.show()`) actually correct?**
  _`AuditLog` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `1. Decisiones base`, `2. Topología`, `3.1 Productos internos (independientes del proveedor)` to the rest of the system?**
  _115 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.0504828797190518 - nodes in this community are weakly interconnected._
- **Should `Dependencias Composer` be split into smaller, more focused modules?**
  _Cohesion score 0.0425531914893617 - nodes in this community are weakly interconnected._