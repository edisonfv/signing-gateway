# Graph Report - signing-gateway  (2026-10-23)

## Corpus Check
- 189 files · ~45,480 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1134 nodes · 2526 edges · 110 communities (65 shown, 34 thin omitted)
- Extraction: 96% EXTRACTED · 4% INFERRED · 0% AMBIGUOUS · INFERRED: 98 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `4dfcc41b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- App\Http\Controllers\Api\V1\SignatureSessionController
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- ProductType
- Tenant
- SignatureRequest
- Province
- SignatureProviderManager
- LimitExceededException
- ApiDocsServiceProvider.php
- Illuminate\Database\Migrations\Migration
- SignatureRequest.php
- scripts
- Illuminate\Http\Request
- Illuminate\Database\Eloquent\Factories\Factory
- Enmascarado de logs
- Script de firma Apidog
- StoreSignatureSessionRequest.php
- Modo de entrega
- FirmaSeguraDriver.php
- Spatie\Enum\Laravel\Enum
- Eventos CloudEvents
- App\Http\Controllers\Controller
- Tipos de certificado TV
- Signing Gateway
- RegularRequestStatus
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- Illuminate\Database\Eloquent\Attributes\Fillable
- Fallos de sesión
- ClientNotification
- SessionStatus
- SessionReconciler
- Tipo de términos
- Estados Transaccional
- RequestStatus
- Tipos de validación FT
- Configuración Scramble
- SessionRejectionCode
- DataProtectionTest.php
- RequestStatus
- CreateSignatureSession.php
- ApiClient
- RegularSignatureFlowTest
- Illuminate\Database\Eloquent\Model
- TestCase
- Signing Gateway — Arquitectura
- CertificateVaultEntry
- require
- require-dev
- App\Models\ProviderAccount
- Illuminate\Database\Eloquent\Concerns\HasUuids
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- setup
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- self
- App\Enums\ProductType
- static
- Illuminate\Http\JsonResponse
- config
- 7. Flujos clave
- liveness.jsx
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- psr-4
- CertificateData
- DocumentType
- SessionErrorCode
- ApiClientAuthenticationTest
- keywords
- post-autoload-dump
- KeyRing
- RequestSignature
- EcuadorianIdentification
- App\Actions\IssueApiClientCredentials
- ReconcileSignatureSessions.php
- AuditLog
- TermsData
- RegularRequestState
- RoleName
- SignatureSessionResource
- EcuadorianIdentificationTest.php
- sanctum.php
- TransactionalValidationStatus
- RequestStatus
- RequestStatus
- CertificateData
- CivilRegistryResult
- LivenessCredentials
- LivenessResult
- SessionData
- TermsData
- ProductType

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 94 edges
2. `Tenant` - 42 edges
3. `RequestStatus` - 39 edges
4. `AuditLog` - 36 edges
5. `DocumentType` - 27 edges
6. `FirmaSeguraDriver` - 27 edges
7. `ApiClient` - 25 edges
8. `SignatureProviderManager` - 24 edges
9. `RegularSignatureFlowTest` - 23 edges
10. `SignatureSessionFlowTest` - 19 edges

## Surprising Connections (you probably didn't know these)
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `SubmitRegularRequest` --references--> `SignatureRequest`  [EXTRACTED]
  app/Jobs/SubmitRegularRequest.php → app/Models/SignatureRequest.php
- `IssueCertificate` --references--> `SignatureRequest`  [EXTRACTED]
  app/Jobs/IssueCertificate.php → app/Models/SignatureRequest.php
- `ReconcileSignatureSession` --references--> `SignatureRequest`  [EXTRACTED]
  app/Jobs/ReconcileSignatureSession.php → app/Models/SignatureRequest.php
- `SessionReconciler` --references--> `SignatureProviderManager`  [EXTRACTED]
  app/Signing/ApiFlow/SessionReconciler.php → app/Signing/SignatureProviderManager.php

## Import Cycles
- None detected.

## Communities (110 total, 34 thin omitted)

### Community 0 - ".http"
Cohesion: 0.18
Nodes (8): FirmaSeguraDriver, SessionTracking, CertificateData, CivilRegistryResult, Illuminate\Http\Client\PendingRequest, LivenessCredentials, LivenessResult, SessionData

### Community 1 - "App\Http\Controllers\Api\V1\SignatureSessionController"
Cohesion: 0.17
Nodes (14): App\Http\Controllers\Api\V1\ProvinceCityController, ProvinceCityController, App\Http\Controllers\Api\V1\ProvinceController, ProvinceController, App\Http\Controllers\Api\V1\SignatureSessionController, Controller, CityResource, ProvinceResource (+6 more)

### Community 2 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.15
Nodes (5): App\Models\ClientNotification, SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "ProductType"
Cohesion: 0.12
Nodes (9): SetProviderCredentials, Environment, ProductType, Provider, App\Exceptions\NoProviderAvailableException, NoProviderAvailableException, ProviderAccount, static (+1 more)

### Community 5 - "Tenant"
Cohesion: 0.18
Nodes (6): LimitExceeded, ProductType, Tenant, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels, UsageGuardTest

### Community 6 - "SignatureRequest"
Cohesion: 0.12
Nodes (4): SignatureRequest, DrivesApiFlow, SessionTracking, Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.13
Nodes (6): App\Models\City, City, Province, CityFactory, ProvinceSeeder, LocationCatalogTest

### Community 8 - "SignatureProviderManager"
Cohesion: 0.15
Nodes (9): App\Exceptions\InvalidStatusTransition, InvalidStatusTransition, self, TermsCatalog, App\Signing\Contracts\SignatureProvider, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager (+1 more)

### Community 9 - "LimitExceededException"
Cohesion: 0.24
Nodes (4): LimitExceededException, UsageGuard, DateTimeInterface, Exception

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.12
Nodes (14): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Dedoc\Scramble\Support\Generator\Schema (+6 more)

### Community 11 - "Illuminate\Database\Migrations\Migration"
Cohesion: 0.08
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "SignatureRequest.php"
Cohesion: 0.06
Nodes (44): App\Enums\ApplicationType, App\Enums\CertificatePeriod, App\Enums\SessionStep, SessionStep, ProviderRequestException, App\Exceptions\TrackingBudgetExhaustedException, TrackingBudgetExhaustedException, App\Jobs\Concerns\HandlesProviderSession (+36 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (13): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, App\Security\RequestSignature, Closure, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware (+5 more)

### Community 15 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.27
Nodes (5): ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "StoreSignatureSessionRequest.php"
Cohesion: 0.19
Nodes (7): App\Http\Requests\StoreLivenessResultRequest, StoreLivenessResultRequest, StoreSignatureSessionRequest, Illuminate\Foundation\Http\FormRequest, Illuminate\Validation\Rule, Illuminate\Validation\Validator, Spatie\Enum\Laravel\Rules\EnumRule

### Community 20 - "FirmaSeguraDriver.php"
Cohesion: 0.15
Nodes (13): App\Exceptions\ProviderRequestException, App\Signing\Contracts\DrivesApiFlow, App\Signing\Data\CertificateData, App\Signing\Data\CivilRegistryResult, CivilRegistryResult, App\Signing\Data\LivenessCredentials, LivenessCredentials, App\Signing\Data\LivenessResult (+5 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.24
Nodes (3): AutoCancellationReason, LinkedPartnerStatus, Spatie\Enum\Laravel\Enum

### Community 23 - "App\Http\Controllers\Controller"
Cohesion: 0.19
Nodes (11): CertificateController, App\Http\Controllers\Api\V1\LivenessCredentialController, LivenessCredentialController, RegularCorrectionController, SignatureSessionCancellationController, App\Http\Controllers\Api\V1\SignatureSessionRefreshController, SignatureSessionRefreshController, App\Http\Controllers\Controller (+3 more)

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 32 - "ClientNotification"
Cohesion: 0.12
Nodes (7): DeliverClientNotification, ClientNotification, self, SessionStep, ClientNotificationDeliveryTest, RequestStatus, SessionReconciliationTest

### Community 33 - "SessionStatus"
Cohesion: 0.20
Nodes (4): App\Signing\Drivers\FirmaSegura\Enums\SessionStage, SessionStage, App\Signing\Drivers\FirmaSegura\Enums\SessionStatus, SessionStatus

### Community 34 - "SessionReconciler"
Cohesion: 0.35
Nodes (3): TrackingBudget, SessionReconciler, App\Signing\Data\SessionTracking

### Community 37 - "RequestStatus"
Cohesion: 0.13
Nodes (8): self, RequestStatus, LivenessResultController, LivenessTestController, Illuminate\Contracts\View\View, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Route, SignatureRequest

### Community 51 - "DataProtectionTest.php"
Cohesion: 0.18
Nodes (5): ApplicationType, Illuminate\Contracts\Encryption\DecryptException, Illuminate\Support\Facades\Event, Illuminate\Support\Str, Pdo\Mysql

### Community 53 - "CreateSignatureSession.php"
Cohesion: 0.47
Nodes (7): CreateSignatureSession, App\Security\BlindIndex, App\Signing\ApiFlow\TermsCatalog, App\Signing\Limits\UsageGuard, App\Signing\Routing\ProviderRouter, Illuminate\Support\Facades\Cache, Illuminate\Validation\ValidationException

### Community 54 - "ApiClient"
Cohesion: 0.16
Nodes (11): App\Models\ApiClient, ApiClient, User, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable (+3 more)

### Community 55 - "RegularSignatureFlowTest"
Cohesion: 0.06
Nodes (14): Database\Seeders\ProvinceSeeder, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Http\Client\Request, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\Bus, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Storage, FakesFirmaSegura (+6 more)

### Community 56 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.18
Nodes (8): VaultEncrypted, App\Models\CertificateVaultEntry, SignatureDocument, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Prunable, Illuminate\Support\Facades\Schedule

### Community 57 - "TestCase"
Cohesion: 0.25
Nodes (5): Illuminate\Foundation\Testing\TestCase, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Log, LogMaskingTest, TestCase

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 60 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 61 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 62 - "App\Models\ProviderAccount"
Cohesion: 0.17
Nodes (9): ProviderAccount, ReconcileSignatureSessions, App\Models\ProviderAccount, self, App\Signing\ApiFlow\TrackingBudget, TrackingBudget, Illuminate\Support\Facades\RateLimiter, static (+1 more)

### Community 63 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.17
Nodes (12): Permission, Role, DatabaseSeeder, Database\Seeders\RolesAndPermissionsSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Seeder (+4 more)

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.18
Nodes (11): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7.1 Reconciliación de estados (implementada 2026-10-23), 16.7.2 Firmas Regulares y elección de flujo por condiciones comerciales (2026-10-23) (+3 more)

### Community 68 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 71 - "App\Enums\ProductType"
Cohesion: 0.19
Nodes (7): SetTenantProducts, CertificatePeriod, App\Enums\ProductType, ProductType, ProductType, ProductCatalog, ApplicationType

### Community 73 - "Illuminate\Http\JsonResponse"
Cohesion: 0.18
Nodes (11): App\Http\Controllers\Api\V1\CatalogController, CatalogController, App\Http\Controllers\Api\V1\PingController, PingController, ProductController, App\Http\Controllers\Api\V1\TermsController, TermsController, Dedoc\Scramble\Attributes\Group (+3 more)

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

### Community 82 - "DocumentType"
Cohesion: 0.19
Nodes (3): DocumentType, StoreRegularCorrectionRequest, ValidDocument

### Community 85 - "ApiClientAuthenticationTest"
Cohesion: 0.14
Nodes (4): App\Models\PersonalAccessToken, PersonalAccessToken, Laravel\Sanctum\PersonalAccessToken, ApiClientAuthenticationTest

### Community 86 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 87 - "post-autoload-dump"
Cohesion: 0.67
Nodes (3): post-autoload-dump, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, @php artisan package:discover --ansi

### Community 88 - "KeyRing"
Cohesion: 0.24
Nodes (5): BlindIndex, App\Security\KeyRing, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 89 - "RequestSignature"
Cohesion: 0.19
Nodes (5): RequestSignature, Illuminate\Testing\TestResponse, Tenant, Tests\Concerns\InteractsWithSignedApi, InteractsWithSignedApi

### Community 90 - "EcuadorianIdentification"
Cohesion: 0.29
Nodes (6): App\Rules\EcuadorianIdentification, EcuadorianIdentification, App\Rules\EcuadorianRuc, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Translation\PotentiallyTranslatedString

### Community 91 - "App\Actions\IssueApiClientCredentials"
Cohesion: 0.29
Nodes (5): App\Actions\IssueApiClientCredentials, IssueApiClientCredentials, App\Security\SealedBox, SealedBox, InvalidArgumentException

### Community 92 - "ReconcileSignatureSessions.php"
Cohesion: 0.40
Nodes (6): CreateApiClient, CreateTenant, GenerateGatewayKeys, Illuminate\Console\Attributes\Description, Illuminate\Console\Attributes\Signature, Illuminate\Console\Command

### Community 93 - "AuditLog"
Cohesion: 0.22
Nodes (3): AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 94 - "TermsData"
Cohesion: 0.20
Nodes (4): App\Signing\Data\TermsData, self, TermsData, TermsData

### Community 96 - "RoleName"
Cohesion: 0.22
Nodes (3): App\Enums\PermissionName, PermissionName, RoleName

### Community 97 - "SignatureSessionResource"
Cohesion: 0.31
Nodes (4): LivenessSessionController, SignatureRequest, SignatureSessionController, SignatureSessionResource

### Community 98 - "EcuadorianIdentificationTest.php"
Cohesion: 0.40
Nodes (3): PHPUnit\Framework\Attributes\DataProvider, PHPUnit\Framework\TestCase, EcuadorianIdentificationTest

### Community 99 - "sanctum.php"
Cohesion: 0.40
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

## Knowledge Gaps
- **119 isolated node(s):** `Stack`, `Puesta en marcha (desarrollo)`, `Clientes del SaaS`, `Catálogos`, `Documentación y pruebas en Apidog` (+114 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 398 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **34 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Tenant`, `Province`, `SignatureProviderManager`, `LimitExceededException`, `SignatureRequest.php`, `FirmaSeguraDriver.php`, `App\Http\Controllers\Controller`, `Illuminate\Database\Eloquent\Attributes\Fillable`, `ClientNotification`, `SessionReconciler`, `RequestStatus`, `DataProtectionTest.php`, `CreateSignatureSession.php`, `RegularSignatureFlowTest`, `Illuminate\Database\Eloquent\Model`, `CertificateVaultEntry`, `App\Models\ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `ReconcileSignatureSessions.php`, `RegularRequestState`, `SignatureSessionResource`?**
  _High betweenness centrality (0.122) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `ClientNotification`, `App\Enums\ProductType`, `LimitExceededException`, `CertificateVaultEntry`, `SignatureRequest.php`, `DataProtectionTest.php`, `CreateSignatureSession.php`, `RegularSignatureFlowTest`, `Illuminate\Database\Eloquent\Model`, `App\Actions\IssueApiClientCredentials`, `ReconcileSignatureSessions.php`, `AuditLog`, `Illuminate\Database\Eloquent\Attributes\Fillable`, `Illuminate\Database\Eloquent\Concerns\HasUuids`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `RequestSignature` connect `RequestSignature` to `ClientNotification`, `ApiDocsServiceProvider.php`, `SignatureRequest.php`, `Illuminate\Http\Request`, `ApiClientAuthenticationTest`, `RegularSignatureFlowTest`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `Tenant` (e.g. with `.makeRequest()` and `.test_requests_of_another_tenant_are_not_visible()`) actually correct?**
  _`Tenant` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `RequestStatus` (e.g. with `.result()` and `.session()`) actually correct?**
  _`RequestStatus` has 3 INFERRED edges - model-reasoned connections that need verification._
- **Are the 10 inferred relationships involving `AuditLog` (e.g. with `.handle()` and `.show()`) actually correct?**
  _`AuditLog` has 10 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Stack`, `Puesta en marcha (desarrollo)`, `Clientes del SaaS` to the rest of the system?**
  _119 weakly-connected nodes found - possible documentation gaps or missing edges._