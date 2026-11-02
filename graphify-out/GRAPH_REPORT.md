# Graph Report - gateway-production-guide-4d0375  (2026-11-02)

## Corpus Check
- 240 files · ~63,031 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1321 nodes · 3199 edges · 114 communities (87 shown, 27 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 270 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b4c1dea3`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- .firmaSegura
- Tenant
- SignatureRequest
- Province
- SignatureFlow
- AuditLog.php
- TrackingBudget
- RequestStatus
- scripts
- DocumentType
- ProductType.php
- MaskSensitiveData
- firma-hmac.js
- Controller
- DeliveryMode
- DrivesApiFlow.php
- EvidenceType
- CloudEventType
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- IssuanceLedgerEntry
- RequestType
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- SessionStatus
- SessionReconciler
- TermsType
- Spatie\Enum\Laravel\Enum
- EmissionGuard
- CertificateVaultEntry
- SessionRejectionCode
- SignatureSessionResource
- LimitExceededException.php
- RegularSignatureFlowTest
- ApiClientAuthenticationTest
- LogMaskingTest
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- Closure
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- RegularValidationType
- keywords
- 7. Flujos clave
- liveness.jsx
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Seeder
- Illuminate\Database\Eloquent\Concerns\HasUuids
- KeyRing
- Illuminate\Http\JsonResponse
- AuditLog
- TestCase
- ProductType
- ClientController.php
- SignatureProviderManager
- Illuminate\Http\Request
- .__invoke
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- TransactionalValidationStatus
- Illuminate\Http\Resources\Json\AnonymousResourceCollection
- setup
- TenantSuspensionTest
- config
- Illuminate\Database\Eloquent\Factories\Factory
- AutoCancellationReason
- TransactionalValidationType
- psr-4

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 78 edges
4. `ProviderAccount` - 53 edges
5. `SignatureFlow` - 51 edges
6. `Controller` - 49 edges
7. `ProductType` - 46 edges
8. `AuditLog` - 42 edges
9. `TestCase` - 41 edges
10. `ApplicationType` - 35 edges

## Surprising Connections (you probably didn't know these)
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `IssuanceLedgerTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/IssuanceLedgerTest.php → app/Models/ProviderAccount.php
- `ProviderWebhookTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/ProviderWebhookTest.php → app/Models/ProviderAccount.php

## Import Cycles
- None detected.

## Communities (114 total, 27 thin omitted)

### Community 0 - ".http"
Cohesion: 0.24
Nodes (3): FirmaSeguraDriver, static, Illuminate\Http\Client\PendingRequest

### Community 2 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.18
Nodes (3): SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 5 - "Tenant"
Cohesion: 0.05
Nodes (14): VaultEncrypted, LimitExceeded, Tenant, TenantProduct, UsageGuard, DateTimeInterface, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model (+6 more)

### Community 6 - "SignatureRequest"
Cohesion: 0.13
Nodes (6): CancelSignatureSession, SignatureRequest, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.14
Nodes (6): City, Province, CityFactory, Illuminate\Database\Eloquent\Factories\HasFactory, sessionPayload(), LocationCatalogTest

### Community 8 - "SignatureFlow"
Cohesion: 0.09
Nodes (9): ApplicationType, CertificatePeriod, SignatureFlow, StoreSignatureSessionRequest, UpdateTenantProductsRequest, self, ProductOffering, FirmaSeguraOfferings (+1 more)

### Community 9 - "AuditLog.php"
Cohesion: 0.06
Nodes (27): ReconcileSignatureSessions, SessionStep, self, ProviderRequestException, TrackingBudgetExhaustedException, CancelOpenRequests, advance(), middleware() (+19 more)

### Community 10 - "TrackingBudget"
Cohesion: 0.16
Nodes (7): ApiDocsServiceProvider, AppServiceProvider, TrackingBudget, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.07
Nodes (8): self, RequestStatus, self, self, ApiFlowJobsTest, IssuanceLedgerTest, ProviderWebhookTest, SessionReconciliationTest

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "DocumentType"
Cohesion: 0.06
Nodes (14): DocumentType, RegularCorrectionController, StoreApiClientRequest, StoreLivenessResultRequest, StoreRegularCorrectionRequest, self, EcuadorianIdentification, EcuadorianRuc (+6 more)

### Community 15 - "ProductType.php"
Cohesion: 0.18
Nodes (3): SetProviderCredentials, InvalidStatusTransition, LogicException

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "Controller"
Cohesion: 0.10
Nodes (10): TenantController, CatalogController, CertificateController, LivenessCredentialController, LivenessSessionController, PingController, TermsController, Controller (+2 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.09
Nodes (13): provider(), cancel(), createSession(), issueCertificate(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CertificateData (+5 more)

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.07
Nodes (27): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+19 more)

### Community 27 - "IssuanceLedgerEntry"
Cohesion: 0.18
Nodes (3): VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.21
Nodes (5): CreateApiClient, CreateTenant, RevokeApiClient, SuspendTenant, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.05
Nodes (13): ApiClient, ClientNotification, SignatureRequestPolicy, RequestSignature, Illuminate\Auth\Access\Response, Illuminate\Contracts\View\View, Illuminate\Foundation\Auth\User, Illuminate\Testing\TestResponse (+5 more)

### Community 34 - "SessionReconciler"
Cohesion: 0.18
Nodes (4): SessionReconciler, trackSession(), RegularRequestState, SessionTracking

### Community 36 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.20
Nodes (3): LinkedPartnerStatus, TransactionalRequestStatus, Spatie\Enum\Laravel\Enum

### Community 37 - "EmissionGuard"
Cohesion: 0.26
Nodes (3): CreateSignatureSession, HaltEmission, EmissionGuard

### Community 38 - "CertificateVaultEntry"
Cohesion: 0.15
Nodes (4): CertificateVaultEntry, SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 53 - "SignatureSessionResource"
Cohesion: 0.18
Nodes (5): LivenessResultController, SignatureSessionCancellationController, SignatureSessionController, SignatureSessionRefreshController, SignatureSessionResource

### Community 54 - "LimitExceededException.php"
Cohesion: 0.28
Nodes (3): EmissionHaltedException, LimitExceededException, Exception

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.15
Nodes (13): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos (+5 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.11
Nodes (6): ProviderAccount, offering(), static, withAccount(), createFirmaSeguraAccount(), TenantProductsTest

### Community 63 - "Closure"
Cohesion: 0.27
Nodes (6): AssignRequestContext, EnsureActiveApiClient, RestrictSourceNetwork, VerifyRequestSignature, Closure, Symfony\Component\HttpFoundation\Response

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.18
Nodes (11): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7.1 Reconciliación de estados (implementada 2026-10-23), 16.7.2 Firmas Regulares y elección de flujo por condiciones comerciales (2026-10-23) (+3 more)

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 71 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 86 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (7): Role, DatabaseSeeder, ProvinceSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder, Spatie\Permission\Models\Role

### Community 87 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.21
Nodes (8): Permission, PersonalAccessToken, User, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Notifications\Notifiable, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Models\Permission, Spatie\Permission\Traits\HasRoles

### Community 88 - "KeyRing"
Cohesion: 0.18
Nodes (5): GenerateGatewayKeys, BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 89 - "Illuminate\Http\JsonResponse"
Cohesion: 0.17
Nodes (7): ProductController, TenantProductController, ProductController, ProductController, LivenessTestController, ProductCatalog, Illuminate\Http\JsonResponse

### Community 90 - "AuditLog"
Cohesion: 0.12
Nodes (7): BackfillIssuanceLedger, PurgeApplicantData, SetTenantProducts, StopWhenEmissionBlocked, AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 91 - "TestCase"
Cohesion: 0.20
Nodes (7): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, fakeFirmaSegura(), testP12Base64(), DevToolsExposureTest, PlatformCredentialTest, TestCase

### Community 92 - "ProductType"
Cohesion: 0.17
Nodes (5): Environment, ProductType, NoProviderAvailableException, Illuminate\Support\Collection, ProviderRoutingTest

### Community 93 - "ClientController.php"
Cohesion: 0.25
Nodes (4): IssueApiClientCredentials, RevokeApiClient, ClientController, PlatformCredentialController

### Community 94 - "SignatureProviderManager"
Cohesion: 0.10
Nodes (7): TermsCatalog, currentTerms(), self, TermsData, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.21
Nodes (7): SuspendTenant, IssuanceController, TenantStatusController, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Request, Illuminate\Http\Resources\Json\JsonResource

### Community 96 - ".__invoke"
Cohesion: 0.17
Nodes (3): PermissionName, RoleName, SealedBox

### Community 97 - "Cambios en la plataforma (signature-saas) para el gateway endurecido"
Cohesion: 0.18
Nodes (10): 1. Ficha del proveedor: solo URL y credencial de alta, 2. Catálogo con la credencial de servicio, 3. Reenvío de los avisos del proveedor (nuevo), 4. Una sola URL de notificaciones (por el túnel), 5. Alta de afiliados sin credenciales huérfanas, 6. Suspender y bloquear afiliados, 7. Otros ajustes, 8. Variables nuevas del gateway (producción) (+2 more)

### Community 98 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 99 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, brianium/paratest, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery (+2 more)

### Community 101 - "Illuminate\Http\Resources\Json\AnonymousResourceCollection"
Cohesion: 0.27
Nodes (4): ProvinceCityController, ProvinceController, CityResource, Illuminate\Http\Resources\Json\AnonymousResourceCollection

### Community 102 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 104 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 105 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.32
Nodes (4): ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

## Knowledge Gaps
- **145 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+140 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **27 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `.firmaSegura`, `Tenant`, `SignatureFlow`, `AuditLog.php`, `RequestStatus`, `DocumentType`, `ProductType.php`, `Controller`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `IssuanceLedgerEntry`, `ApiClient`, `SessionReconciler`, `EmissionGuard`, `SignatureSessionResource`, `RegularSignatureFlowTest`, `ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\JsonResponse`, `AuditLog`, `TestCase`, `SignatureProviderManager`, `TenantSuspensionTest`?**
  _High betweenness centrality (0.166) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `SignatureSessionFlowTest`, `SignatureFlow`, `AuditLog.php`, `RequestStatus`, `ProductType.php`, `Controller`, `.makeSignatureRequest`, `Illuminate\Console\Command`, `ApiClient`, `ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\JsonResponse`, `AuditLog`, `TestCase`, `ProductType`, `ClientController.php`, `Illuminate\Http\Request`, `.__invoke`, `TenantSuspensionTest`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `ProviderAccount` connect `ProviderAccount` to `.http`, `.firmaSegura`, `Tenant`, `TenantSuspensionTest`, `SignatureFlow`, `AuditLog.php`, `TrackingBudget`, `RequestStatus`, `ProductType.php`, `.makeSignatureRequest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TestCase`, `ProductType`, `SignatureProviderManager`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 21 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 21 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _145 weakly-connected nodes found - possible documentation gaps or missing edges._