# Graph Report - signing-gateway  (2026-10-23)

## Corpus Check
- 175 files · ~41,169 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1023 nodes · 2187 edges · 88 communities (49 shown, 28 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 75 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `cb73dfda`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- App\Http\Controllers\Api\V1\CatalogController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- DataProtectionTest.php
- Tenant
- SignatureRequest
- Province
- LogicException
- LimitExceededException
- AppServiceProvider.php
- Illuminate\Database\Migrations\Migration
- SignatureRequest.php
- scripts
- AuditLog
- UserFactory.php
- Enmascarado de logs
- Script de firma Apidog
- SessionReconciliationTest.php
- Modo de entrega
- FirmaSeguraDriver.php
- AutoCancellationReason
- Eventos CloudEvents
- LinkedPartnerStatus
- Tipos de certificado TV
- Signing Gateway
- Spatie\Enum\Laravel\Enum
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- App\Models\Tenant
- Fallos de sesión
- ClientNotification
- .handle
- SessionReconciler
- Tipo de términos
- Estados Transaccional
- .makeRequest
- Tipos de validación FT
- Configuración Scramble
- SessionRejectionCode
- Illuminate\Support\Str
- RequestStatus
- CreateSignatureSession.php
- Illuminate\Database\Eloquent\Concerns\HasUuids
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Model
- TestCase
- Signing Gateway — Arquitectura
- RequestStatus
- require
- require-dev
- App\Models\ProviderAccount
- App\Actions\IssueApiClientCredentials
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- setup
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- self
- UsageGuardTest.php
- static
- App\Models\ApiClient
- config
- 7. Flujos clave
- liveness.jsx
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- psr-4
- CertificateData
- SignatureProvider
- SessionErrorCode
- Illuminate\Http\Client\Response
- keywords
- post-autoload-dump

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 58 edges
2. `Tenant` - 36 edges
3. `AuditLog` - 34 edges
4. `ApiClient` - 25 edges
5. `FirmaSeguraDriver` - 23 edges
6. `ProductType` - 20 edges
7. `SignatureSessionFlowTest` - 19 edges
8. `IssueCertificate` - 18 edges
9. `SessionReconciliationTest` - 18 edges
10. `ClientNotification` - 18 edges

## Surprising Connections (you probably didn't know these)
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `IssueCertificate` --references--> `SignatureRequest`  [EXTRACTED]
  app/Jobs/IssueCertificate.php → app/Models/SignatureRequest.php
- `ReconcileSignatureSession` --references--> `SignatureRequest`  [EXTRACTED]
  app/Jobs/ReconcileSignatureSession.php → app/Models/SignatureRequest.php
- `SessionReconciler` --references--> `TrackingBudget`  [EXTRACTED]
  app/Signing/ApiFlow/SessionReconciler.php → app/Signing/ApiFlow/TrackingBudget.php
- `FirmaSeguraDriver` --implements--> `DrivesApiFlow`  [EXTRACTED]
  app/Signing/Drivers/FirmaSegura/FirmaSeguraDriver.php → app/Signing/Contracts/DrivesApiFlow.php

## Import Cycles
- None detected.

## Communities (88 total, 28 thin omitted)

### Community 0 - ".http"
Cohesion: 0.17
Nodes (9): FirmaSeguraDriver, CertificateData, CivilRegistryResult, LivenessCredentials, LivenessResult, SessionData, TermsData, Illuminate\Http\Client\PendingRequest (+1 more)

### Community 1 - "App\Http\Controllers\Api\V1\CatalogController"
Cohesion: 0.13
Nodes (16): App\Http\Controllers\Api\V1\CatalogController, CatalogController, PingController, App\Http\Controllers\Api\V1\ProvinceCityController, ProvinceCityController, App\Http\Controllers\Api\V1\ProvinceController, ProvinceController, Controller (+8 more)

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "DataProtectionTest.php"
Cohesion: 0.21
Nodes (5): ProductType, ProviderAccount, Illuminate\Contracts\Encryption\DecryptException, Illuminate\Foundation\Testing\RefreshDatabase, ProviderRoutingTest

### Community 5 - "Tenant"
Cohesion: 0.22
Nodes (5): LimitExceeded, Tenant, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels, UsageGuardTest

### Community 6 - "SignatureRequest"
Cohesion: 0.20
Nodes (3): RequestStatus, SignatureRequest, Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.13
Nodes (10): App\Models\City, City, Province, CityFactory, ProvinceFactory, Database\Seeders\ProvinceSeeder, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\Factory (+2 more)

### Community 8 - "LogicException"
Cohesion: 0.13
Nodes (7): App\Exceptions\InvalidStatusTransition, InvalidStatusTransition, self, App\Signing\Contracts\SignatureProvider, self, TermsData, LogicException

### Community 9 - "LimitExceededException"
Cohesion: 0.24
Nodes (4): LimitExceededException, UsageGuard, DateTimeInterface, Exception

### Community 10 - "AppServiceProvider.php"
Cohesion: 0.07
Nodes (26): ApiDocsServiceProvider, AppServiceProvider, BlindIndex, App\Security\KeyRing, KeyRing, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation (+18 more)

### Community 11 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.09
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "SignatureRequest.php"
Cohesion: 0.05
Nodes (64): App\Enums\ProductType, App\Enums\RequestStatus, App\Enums\SessionStep, SessionStep, ProviderRequestException, App\Http\Controllers\Api\V1\CertificateController, CertificateController, App\Http\Controllers\Api\V1\LivenessCredentialController (+56 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "AuditLog"
Cohesion: 0.06
Nodes (23): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, AuditLog, self, App\Security\RequestSignature, RequestSignature, Closure (+15 more)

### Community 15 - "UserFactory.php"
Cohesion: 0.40
Nodes (3): static, UserFactory, Illuminate\Support\Facades\Hash

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "SessionReconciliationTest.php"
Cohesion: 0.10
Nodes (13): App\Enums\ApplicationType, App\Enums\CertificatePeriod, StoreLivenessResultRequest, StoreSignatureSessionRequest, EcuadorianIdentification, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Foundation\Http\FormRequest (+5 more)

### Community 20 - "FirmaSeguraDriver.php"
Cohesion: 0.12
Nodes (12): DrivesApiFlow, App\Signing\Data\CertificateData, App\Signing\Data\CivilRegistryResult, CivilRegistryResult, App\Signing\Data\LivenessCredentials, LivenessCredentials, App\Signing\Data\LivenessResult, LivenessResult (+4 more)

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 26 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.28
Nodes (3): RegularRequestStatus, TransactionalValidationStatus, Spatie\Enum\Laravel\Enum

### Community 32 - "ClientNotification"
Cohesion: 0.12
Nodes (7): DeliverClientNotification, ClientNotification, self, SessionStep, ClientNotificationDeliveryTest, RequestStatus, SessionReconciliationTest

### Community 34 - "SessionReconciler"
Cohesion: 0.16
Nodes (7): App\Exceptions\ProviderRequestException, RequestStatus, SessionReconciler, SessionTracking, App\Signing\Drivers\FirmaSegura\Enums\SessionStatus, SessionStatus, App\Signing\SignatureProviderManager

### Community 37 - ".makeRequest"
Cohesion: 0.20
Nodes (6): NoProviderAvailableException, TrackingBudgetExhaustedException, RequestStatus, RuntimeException, ApiFlowJobsTest, SignatureRequest

### Community 53 - "CreateSignatureSession.php"
Cohesion: 0.23
Nodes (9): CreateSignatureSession, App\Security\BlindIndex, TermsCatalog, App\Signing\Limits\UsageGuard, App\Signing\Routing\ProviderRouter, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager (+1 more)

### Community 54 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.16
Nodes (10): ApiClient, App\Models\PersonalAccessToken, PersonalAccessToken, User, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens (+2 more)

### Community 55 - "SignatureSessionFlowTest"
Cohesion: 0.12
Nodes (3): DevToolsExposureTest, SignatureSessionFlowTest, Tests\TestCase

### Community 56 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (10): VaultEncrypted, App\Models\AuditLog, App\Models\CertificateVaultEntry, App\Models\ClientNotification, TermsAcceptance, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden (+2 more)

### Community 57 - "TestCase"
Cohesion: 0.18
Nodes (7): Illuminate\Foundation\Testing\TestCase, Illuminate\Http\Client\Request, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Log, LogMaskingTest, TestCase

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 59 - "RequestStatus"
Cohesion: 0.13
Nodes (5): self, RequestStatus, CertificateVaultEntry, Illuminate\Database\Eloquent\Prunable, DataProtectionTest

### Community 60 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 61 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 62 - "App\Models\ProviderAccount"
Cohesion: 0.07
Nodes (18): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, ProviderAccount, ReconcileSignatureSessions, SetProviderCredentials, Environment (+10 more)

### Community 63 - "App\Actions\IssueApiClientCredentials"
Cohesion: 0.09
Nodes (18): App\Actions\IssueApiClientCredentials, App\Enums\PermissionName, PermissionName, RoleName, Permission, Role, App\Security\SealedBox, SealedBox (+10 more)

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.20
Nodes (10): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7.1 Reconciliación de estados (implementada 2026-10-23), 16.7 API interna para el Flujo por API (+2 more)

### Community 68 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 71 - "UsageGuardTest.php"
Cohesion: 0.25
Nodes (3): ApplicationType, CertificatePeriod, Illuminate\Support\Facades\Event

### Community 73 - "App\Models\ApiClient"
Cohesion: 0.52
Nodes (4): App\Models\ApiClient, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Symfony\Component\HttpFoundation\IpUtils

### Community 74 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 76 - "liveness.jsx"
Cohesion: 0.38
Nodes (6): displayText, LivenessTest(), complete(), start(), postJson(), root

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 80 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 86 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 87 - "post-autoload-dump"
Cohesion: 0.67
Nodes (3): post-autoload-dump, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, @php artisan package:discover --ansi

## Knowledge Gaps
- **118 isolated node(s):** `1. Decisiones base`, `2. Topología`, `3.1 Productos internos (independientes del proveedor)`, `3.2 Contratos por capacidad`, `3.3 Resolución de drivers` (+113 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 373 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **28 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `ClientNotification`, `.http`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `SessionReconciler`, `DataProtectionTest.php`, `Tenant`, `UsageGuardTest.php`, `LogicException`, `LimitExceededException`, `SignatureRequest.php`, `SessionReconciliationTest.php`, `FirmaSeguraDriver.php`, `CreateSignatureSession.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Model`, `RequestStatus`, `App\Models\ProviderAccount`?**
  _High betweenness centrality (0.103) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `ClientNotification`, `.handle`, `SessionReconciler`, `DataProtectionTest.php`, `LimitExceededException`, `SignatureRequest.php`, `Illuminate\Support\Str`, `CreateSignatureSession.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Database\Eloquent\Model`, `RequestStatus`, `App\Models\ProviderAccount`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `ProductType` connect `DataProtectionTest.php` to `.makeRequest`, `Tenant`, `UsageGuardTest.php`, `LogicException`, `SignatureRequest.php`, `SignatureProvider`, `Modo de entrega`, `CreateSignatureSession.php`, `Spatie\Enum\Laravel\Enum`, `RequestStatus`, `App\Models\ProviderAccount`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `Tenant` (e.g. with `.makeRequest()` and `.test_requests_of_another_tenant_are_not_visible()`) actually correct?**
  _`Tenant` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `AuditLog` (e.g. with `.show()` and `.show()`) actually correct?**
  _`AuditLog` has 8 INFERRED edges - model-reasoned connections that need verification._
- **What connects `1. Decisiones base`, `2. Topología`, `3.1 Productos internos (independientes del proveedor)` to the rest of the system?**
  _118 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `App\Http\Controllers\Api\V1\CatalogController` be split into smaller, more focused modules?**
  _Cohesion score 0.13043478260869565 - nodes in this community are weakly interconnected._