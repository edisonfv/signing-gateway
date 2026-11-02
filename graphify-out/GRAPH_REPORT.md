# Graph Report - gateway-production-guide-4d0375  (2026-11-02)

## Corpus Check
- 250 files · ~65,781 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1373 nodes · 3325 edges · 122 communities (92 shown, 30 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 284 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `84b75f41`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- SignatureFlow
- Tenant
- SignatureRequest
- Province
- ProductType
- HandlesProviderSession.php
- TrackingBudget
- RequestStatus
- scripts
- DocumentType
- CertificatePeriod
- MaskSensitiveData
- firma-hmac.js
- ApiClient.php
- DeliveryMode
- DrivesApiFlow.php
- EvidenceType
- CloudEventType
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- IssuanceLedger
- RequestType
- RevocationStatus
- Illuminate\Console\Command
- Spatie\Enum\Laravel\Enum
- ApiClient
- SessionStatus
- SessionReconciler
- TermsType
- LinkedPartnerStatus
- EmissionGuard
- SignatureDocument.php
- SessionRejectionCode
- Controller
- AuditLog.php
- RegularSignatureFlowTest
- ApiClientAuthenticationTest
- LogMaskingTest
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- Illuminate\Http\Request
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- RegularValidationType
- keywords
- 7. Flujos clave
- liveness.jsx
- Signing Gateway
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Eloquent\Concerns\HasUuids
- User.php
- KeyRing
- Illuminate\Http\JsonResponse
- AuditLog
- TestCase
- SessionReconciler.php
- PairPlatform
- CreateSignatureSession
- IssuanceController.php
- PairingCode
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- TransactionalValidationStatus
- .handle
- setup
- TenantSuspensionTest
- config
- Illuminate\Database\Eloquent\Factories\Factory
- AutoCancellationReason
- TransactionalValidationType
- psr-4
- LivenessCredentials
- TenantController.php
- Illuminate\Database\Seeder
- 2. Configuración por cliente (admin del SaaS)
- ProviderRoutingTest
- TransactionalRequestStatus

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 71 edges
4. `ProviderAccount` - 56 edges
5. `SignatureFlow` - 51 edges
6. `Controller` - 51 edges
7. `ProductType` - 46 edges
8. `AuditLog` - 45 edges
9. `TestCase` - 45 edges
10. `ApiClient` - 39 edges

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

## Communities (122 total, 30 thin omitted)

### Community 0 - ".http"
Cohesion: 0.12
Nodes (7): submitLiveness(), validateCivilRegistry(), CivilRegistryResult, LivenessResult, FirmaSeguraDriver, static, Illuminate\Http\Client\PendingRequest

### Community 2 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.11
Nodes (4): IssuanceLedgerEntry, SignatureRequestTransition, TenantProduct, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "SignatureFlow"
Cohesion: 0.15
Nodes (4): SignatureFlow, InvalidStatusTransition, ProviderRouter, LogicException

### Community 5 - "Tenant"
Cohesion: 0.05
Nodes (13): PermissionName, RoleName, LimitExceeded, Tenant, SealedBox, UsageGuard, DateTimeInterface, Illuminate\Database\Eloquent\Relations\HasMany (+5 more)

### Community 6 - "SignatureRequest"
Cohesion: 0.09
Nodes (8): CancelSignatureSession, SignatureRequest, correctRegularRequest(), registerRegularRequest(), regularRequestState(), SignatureProviderManager, Illuminate\Database\Eloquent\Relations\HasOne, Illuminate\Support\Manager

### Community 7 - "Province"
Cohesion: 0.15
Nodes (6): City, Province, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\HasFactory, sessionPayload(), LocationCatalogTest

### Community 8 - "ProductType"
Cohesion: 0.11
Nodes (7): ApplicationType, ProductType, StoreSignatureSessionRequest, offering(), self, ProductOffering, FirmaSeguraOfferings

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.08
Nodes (25): ReconcileSignatureSessions, SessionStep, self, ProviderRequestException, CancelOpenRequests, advance(), middleware(), notifyClient() (+17 more)

### Community 10 - "TrackingBudget"
Cohesion: 0.14
Nodes (7): ApiDocsServiceProvider, AppServiceProvider, TrackingBudget, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.07
Nodes (8): self, RequestStatus, self, self, ApiFlowJobsTest, IssuanceLedgerTest, ProviderWebhookTest, SessionReconciliationTest

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "DocumentType"
Cohesion: 0.09
Nodes (11): DocumentType, StoreRegularCorrectionRequest, StopWhenEmissionBlocked, self, EcuadorianIdentification, EcuadorianRuc, ValidDocument, Closure (+3 more)

### Community 15 - "CertificatePeriod"
Cohesion: 0.17
Nodes (4): CertificatePeriod, UpdateTenantProductsRequest, Illuminate\Foundation\Http\FormRequest, Illuminate\Validation\Validator

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "ApiClient.php"
Cohesion: 0.20
Nodes (3): CertificateController, PingController, TermsController

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.10
Nodes (9): provider(), cancel(), createSession(), currentTerms(), issueCertificate(), CertificateData, SessionData, self (+1 more)

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.14
Nodes (14): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales, 5. Catálogos (+6 more)

### Community 27 - "IssuanceLedger"
Cohesion: 0.28
Nodes (3): BackfillIssuanceLedger, VerifyIssuanceLedger, IssuanceLedger

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.11
Nodes (9): CreateApiClient, CreateTenant, GenerateGatewayKeys, InstallGateway, PairPlatform, PurgeApplicantData, RevokeApiClient, SuspendTenant (+1 more)

### Community 31 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.20
Nodes (3): RegularValidationStatus, SessionFailureCode, Spatie\Enum\Laravel\Enum

### Community 32 - "ApiClient"
Cohesion: 0.06
Nodes (10): ApiClient, ClientNotification, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Contracts\View\View, Illuminate\Foundation\Auth\User, Laravel\Sanctum\HasApiTokens, ClientNotificationDeliveryTest (+2 more)

### Community 34 - "SessionReconciler"
Cohesion: 0.18
Nodes (4): SessionReconciler, trackSession(), RegularRequestState, SessionTracking

### Community 38 - "SignatureDocument.php"
Cohesion: 0.16
Nodes (4): VaultEncrypted, SignatureDocument, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Prunable

### Community 53 - "Controller"
Cohesion: 0.10
Nodes (10): LivenessResultController, LivenessSessionController, RegularCorrectionController, SignatureSessionCancellationController, SignatureSessionController, SignatureSessionRefreshController, Controller, LivenessTestController (+2 more)

### Community 54 - "AuditLog.php"
Cohesion: 0.13
Nodes (5): EmissionHaltedException, LimitExceededException, LivenessCredentialController, ProviderEventController, Exception

### Community 56 - "ApiClientAuthenticationTest"
Cohesion: 0.07
Nodes (7): StoreApiClientRequest, NotificationTarget, RequestSignature, Illuminate\Testing\TestResponse, signedGet(), signedJson(), ApiClientAuthenticationTest

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.14
Nodes (14): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto (+6 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.14
Nodes (5): ProviderAccount, static, withAccount(), createFirmaSeguraAccount(), TenantProductsTest

### Community 63 - "Illuminate\Http\Request"
Cohesion: 0.19
Nodes (7): TenantStatusController, AssignRequestContext, EnsureActiveApiClient, RestrictSourceNetwork, VerifyRequestSignature, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

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

### Community 78 - "Signing Gateway"
Cohesion: 0.17
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 86 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.15
Nodes (7): Permission, Role, TermsAcceptance, RolesAndPermissionsSeeder, Illuminate\Database\Eloquent\Concerns\HasUuids, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role

### Community 87 - "User.php"
Cohesion: 0.24
Nodes (5): PersonalAccessToken, User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Traits\HasRoles

### Community 88 - "KeyRing"
Cohesion: 0.39
Nodes (3): KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 89 - "Illuminate\Http\JsonResponse"
Cohesion: 0.14
Nodes (9): SetTenantProducts, ProductController, TenantProductController, CatalogController, ProductController, ProductController, ProductCatalog, Illuminate\Http\JsonResponse (+1 more)

### Community 90 - "AuditLog"
Cohesion: 0.21
Nodes (5): SuspendTenant, AuditLog, self, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 91 - "TestCase"
Cohesion: 0.14
Nodes (8): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, fakeFirmaSegura(), testP12Base64(), DevToolsExposureTest, GatewayDoctorTest, PlatformCredentialTest, TestCase

### Community 92 - "SessionReconciler.php"
Cohesion: 0.11
Nodes (7): SetProviderCredentials, Environment, Provider, NoProviderAvailableException, TrackingBudgetExhaustedException, Illuminate\Support\Collection, RuntimeException

### Community 93 - "PairPlatform"
Cohesion: 0.16
Nodes (6): IssueApiClientCredentials, PairPlatform, RevokeApiClient, ClientController, PlatformCredentialController, PairingController

### Community 94 - "CreateSignatureSession"
Cohesion: 0.24
Nodes (3): CreateSignatureSession, BlindIndex, TermsCatalog

### Community 95 - "IssuanceController.php"
Cohesion: 0.14
Nodes (8): IssuanceController, ProvinceCityController, ProvinceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Resources\Json\AnonymousResourceCollection, Illuminate\Http\Resources\Json\JsonResource

### Community 97 - "Cambios en la plataforma (signature-saas) para el gateway endurecido"
Cohesion: 0.18
Nodes (10): 1. Ficha del proveedor: URL y código de emparejamiento, 2. Catálogo con la credencial de servicio, 3. Reenvío de los avisos del proveedor (nuevo), 4. Una sola URL de notificaciones (por el túnel), 5. Alta de afiliados sin credenciales huérfanas, 6. Suspender y bloquear afiliados, 7. Otros ajustes, 8. Variables nuevas del gateway (producción) (+2 more)

### Community 98 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 99 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, brianium/paratest, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery (+2 more)

### Community 102 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 104 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 105 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.21
Nodes (5): CityFactory, ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 111 - "LivenessCredentials"
Cohesion: 0.38
Nodes (3): requestLivenessSession(), LivenessCredentials, Carbon\CarbonImmutable

### Community 116 - "Illuminate\Database\Seeder"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 117 - "2. Configuración por cliente (admin del SaaS)"
Cohesion: 0.40
Nodes (5): 2. Configuración por cliente (admin del SaaS), Alta automática desde el backoffice del SaaS, Credencial de servicio y reenvío de avisos del proveedor, Instalación y emparejamiento, Registro de emisiones (para facturar y conciliar)

## Knowledge Gaps
- **147 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+142 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `SignatureFlow`, `Tenant`, `ProductType`, `HandlesProviderSession.php`, `RequestStatus`, `DocumentType`, `CertificatePeriod`, `ApiClient.php`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `IssuanceLedger`, `Illuminate\Console\Command`, `ApiClient`, `SessionReconciler`, `EmissionGuard`, `Controller`, `AuditLog.php`, `RegularSignatureFlowTest`, `ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `AuditLog`, `TestCase`, `SessionReconciler.php`, `IssuanceController.php`, `TenantSuspensionTest`?**
  _High betweenness centrality (0.121) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `ApiClient`, `SignatureSessionFlowTest`, `SignatureFlow`, `HandlesProviderSession.php`, `RequestStatus`, `CertificatePeriod`, `TenantController.php`, `.makeSignatureRequest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `AuditLog.php`, `Illuminate\Http\JsonResponse`, `AuditLog`, `TestCase`, `SessionReconciler.php`, `PairPlatform`, `Illuminate\Console\Command`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Why does `RequestStatus` connect `RequestStatus` to `SignatureSessionFlowTest`, `SignatureFlow`, `Tenant`, `SignatureRequest`, `HandlesProviderSession.php`, `DocumentType`, `CertificatePeriod`, `ApiClient.php`, `.makeSignatureRequest`, `IssuanceLedger`, `Illuminate\Console\Command`, `Spatie\Enum\Laravel\Enum`, `ApiClient`, `SessionStatus`, `SessionReconciler`, `Controller`, `TestCase`, `SessionReconciler.php`, `TenantSuspensionTest`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _147 weakly-connected nodes found - possible documentation gaps or missing edges._