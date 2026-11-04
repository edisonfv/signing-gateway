# Graph Report - dns-migration-server-6479af  (2026-11-04)

## Corpus Check
- 261 files · ~70,263 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1455 nodes · 3534 edges · 125 communities (90 shown, 35 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 291 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `9757792e`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- UsageGuardTest.php
- IssuanceLedgerEntry
- composer.json
- Illuminate\Database\Eloquent\Model
- Tenant
- SignatureRequest
- Province
- ProviderAccount
- HandlesProviderSession.php
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- Closure
- CertificatePeriod
- MaskSensitiveData
- firma-hmac.js
- ProductType
- DeliveryMode
- DrivesApiFlow.php
- Spatie\Enum\Laravel\Enum
- TenantProductsTest
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- ProductCatalog.php
- SessionReconciliationTest
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- AuditLog.php
- SignatureRequest.php
- GatewayDoctor.php
- ProductCatalog
- ProviderWebhookTest
- SignatureDocument
- SessionRejectionCode
- Illuminate\Http\JsonResponse
- TestCase
- IssuanceLedgerEntry.php
- RequestSignature
- LivenessTestController.php
- Signing Gateway — Arquitectura
- dev
- GatewayHealth
- VerifyRequestSignature.php
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- RegularValidationType
- keywords
- 7. Flujos clave
- liveness.jsx
- StrictIdentificationConfigTest
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Eloquent\Concerns\HasUuids
- EvidenceType
- .handle
- TenantStatusController
- AuditLog
- Illuminate\Foundation\Testing\RefreshDatabase
- ApplicationType
- PermissionName
- SignatureProviderManager
- Illuminate\Http\Request
- UpdateTenantProductsRequest
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- EcuadorianIdentificationTest
- ProviderRoutingTest
- setup
- V1/ProductController.php
- config
- AutoCancellationReason
- DocumentType
- RequestType
- psr-4
- ApiClientAuthenticationTest
- Illuminate\Foundation\Http\FormRequest
- TransactionalRequestStatus
- StoreApiClientRequest
- ClientNotification
- CloudEventType
- .handle
- SessionFailureCode

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 71 edges
4. `ProviderAccount` - 63 edges
5. `Controller` - 53 edges
6. `TestCase` - 53 edges
7. `SignatureFlow` - 51 edges
8. `ProductType` - 46 edges
9. `AuditLog` - 45 edges
10. `ApiClient` - 39 edges

## Surprising Connections (you probably didn't know these)
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `signedGet()` --calls--> `RequestSignature`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Security/RequestSignature.php
- `signedJson()` --calls--> `RequestSignature`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Security/RequestSignature.php

## Import Cycles
- None detected.

## Communities (125 total, 35 thin omitted)

### Community 0 - ".http"
Cohesion: 0.06
Nodes (5): FirmaSeguraDriver, Illuminate\Http\Client\PendingRequest, NetworkHardeningTest, RegularSignatureFlowTest, SignatureSessionFlowTest

### Community 1 - "UsageGuardTest.php"
Cohesion: 0.14
Nodes (8): LimitExceeded, EmissionHaltedException, LimitExceededException, UsageGuard, DateTimeInterface, Exception, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 2 - "IssuanceLedgerEntry"
Cohesion: 0.15
Nodes (4): BackfillIssuanceLedger, VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.17
Nodes (6): SuspendTenant, VaultEncrypted, SuspendTenant, self, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model

### Community 5 - "Tenant"
Cohesion: 0.13
Nodes (3): Tenant, Illuminate\Database\Eloquent\Relations\HasMany, UsageGuardTest

### Community 6 - "SignatureRequest"
Cohesion: 0.09
Nodes (13): CancelSignatureSession, SignatureFlow, LivenessResultController, LivenessSessionController, RegularCorrectionController, SignatureSessionCancellationController, SignatureSessionController, SignatureSessionRefreshController (+5 more)

### Community 7 - "Province"
Cohesion: 0.06
Nodes (15): RoleName, Province, Role, CityFactory, ProvinceFactory, static, UserFactory, DatabaseSeeder (+7 more)

### Community 8 - "ProviderAccount"
Cohesion: 0.20
Nodes (4): ReconcileSignatureSessions, ProviderAccount, TrackingBudget, static

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.15
Nodes (16): CancelOpenRequests, advance(), middleware(), notifyClient(), reject(), runnableStatuses(), CreateProviderSession, IssueCertificate (+8 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.20
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.11
Nodes (4): self, RequestStatus, SessionStep, ApiFlowJobsTest

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "Closure"
Cohesion: 0.28
Nodes (5): StopWhenEmissionBlocked, EcuadorianIdentification, EcuadorianRuc, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.05
Nodes (21): provider(), SessionReconciler, cancel(), createSession(), currentTerms(), issueCertificate(), requestLivenessSession(), submitLiveness() (+13 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.14
Nodes (5): LinkedPartnerStatus, TermsType, TransactionalValidationStatus, TransactionalValidationType, Spatie\Enum\Laravel\Enum

### Community 23 - ".makeSignatureRequest"
Cohesion: 0.10
Nodes (4): CertificateVaultEntry, SealedBox, AdminProvisioningTest, DataProtectionTest

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.06
Nodes (28): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+20 more)

### Community 27 - "ProductCatalog.php"
Cohesion: 0.16
Nodes (6): NoProviderAvailableException, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Support\Collection, Illuminate\Support\Manager

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.21
Nodes (6): CreateApiClient, GenerateGatewayKeys, InstallGateway, PairPlatform, PurgeApplicantData, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.09
Nodes (10): RevokeApiClient, ApiClient, User, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens (+2 more)

### Community 33 - "AuditLog.php"
Cohesion: 0.13
Nodes (5): self, ProviderRequestException, TrackingBudgetExhaustedException, Illuminate\Http\Client\Response, RuntimeException

### Community 35 - "GatewayDoctor.php"
Cohesion: 0.27
Nodes (3): SetProviderCredentials, Environment, Provider

### Community 53 - "Illuminate\Http\JsonResponse"
Cohesion: 0.10
Nodes (14): ClientController, ProductController, TenantController, CatalogController, CertificateController, LivenessCredentialController, PairingController, PingController (+6 more)

### Community 54 - "TestCase"
Cohesion: 0.11
Nodes (6): Illuminate\Foundation\Testing\TestCase, DevToolsExposureTest, EnvironmentIsolationTest, GenerateGatewayKeysTest, LogMaskingTest, TestCase

### Community 55 - "IssuanceLedgerEntry.php"
Cohesion: 0.29
Nodes (3): InvalidStatusTransition, self, LogicException

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.13
Nodes (15): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 19. Monitor de salud 2026-11-03, 1. Decisiones base, 2. Topología (+7 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "GatewayHealth"
Cohesion: 0.10
Nodes (10): HaltEmission, RecordHeartbeat, QueueHeartbeat, RecordProviderCall, Heartbeat, EmissionGuard, GatewayHealth, Carbon\CarbonInterface (+2 more)

### Community 63 - "VerifyRequestSignature.php"
Cohesion: 0.22
Nodes (5): AssignRequestContext, EnsureActiveApiClient, RestrictSourceNetwork, VerifyRequestSignature, Symfony\Component\HttpFoundation\Response

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

### Community 86 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.09
Nodes (10): City, Permission, SignatureRequestTransition, TenantProduct, TermsAcceptance, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Relations\BelongsTo (+2 more)

### Community 88 - ".handle"
Cohesion: 0.19
Nodes (5): GatewayDoctor, BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 90 - "AuditLog"
Cohesion: 0.10
Nodes (7): PairPlatform, RevokeApiClient, CreateTenant, SetTenantProducts, AuditLog, PairingCode, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 91 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.10
Nodes (11): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Testing\TestResponse, createFirmaSeguraAccount(), fakeFirmaSegura(), testP12Base64(), issueApiCredentials(), signedGet(), signedJson() (+3 more)

### Community 92 - "ApplicationType"
Cohesion: 0.10
Nodes (8): ApplicationType, StoreSignatureSessionRequest, offering(), static, withAccount(), self, ProductOffering, FirmaSeguraOfferings

### Community 93 - "PermissionName"
Cohesion: 0.35
Nodes (3): IssueApiClientCredentials, PermissionName, PlatformCredentialController

### Community 94 - "SignatureProviderManager"
Cohesion: 0.18
Nodes (4): CreateSignatureSession, TermsCatalog, ProviderRouter, SignatureProviderManager

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (10): IssuanceController, ProviderEventController, ProvinceCityController, ProvinceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Request (+2 more)

### Community 97 - "Cambios en la plataforma (signature-saas) para el gateway endurecido"
Cohesion: 0.12
Nodes (16): 1. Ficha del proveedor: URL y código de emparejamiento, 2. Catálogo con la credencial de servicio, 3. Reenvío de los avisos del proveedor (nuevo), 4. Una sola URL de notificaciones (por el túnel), 5. Alta de afiliados sin credenciales huérfanas, 6. Suspender y bloquear afiliados, 7. Otros ajustes, 8.1 Dónde vive y quién lo ve (+8 more)

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

### Community 106 - "DocumentType"
Cohesion: 0.21
Nodes (3): DocumentType, self, ValidDocument

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 111 - "ApiClientAuthenticationTest"
Cohesion: 0.13
Nodes (3): PersonalAccessToken, Laravel\Sanctum\PersonalAccessToken, ApiClientAuthenticationTest

### Community 112 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpsertTenantRequest, Illuminate\Foundation\Http\FormRequest

### Community 124 - "ClientNotification"
Cohesion: 0.16
Nodes (5): DeliverClientNotification, ClientNotification, self, ClientNotificationDeliveryTest, Throwable

## Knowledge Gaps
- **153 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+148 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **35 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `UsageGuardTest.php`, `IssuanceLedgerEntry`, `Illuminate\Database\Eloquent\Model`, `Tenant`, `ProviderAccount`, `HandlesProviderSession.php`, `RequestStatus`, `CertificatePeriod`, `ProductType`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `ProductCatalog.php`, `SessionReconciliationTest`, `ApiClient`, `AuditLog.php`, `SignatureRequest.php`, `ProviderWebhookTest`, `Illuminate\Http\JsonResponse`, `IssuanceLedgerEntry.php`, `LivenessTestController.php`, `GatewayHealth`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SignatureProviderManager`, `Illuminate\Http\Request`, `DocumentType`, `ClientNotification`?**
  _High betweenness centrality (0.100) - this node is a cross-community bridge._
- **Why does `ProviderAccount` connect `ProviderAccount` to `.http`, `Illuminate\Database\Eloquent\Model`, `Tenant`, `CertificatePeriod`, `ProductType`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `ProductCatalog.php`, `SessionReconciliationTest`, `AuditLog.php`, `SignatureRequest.php`, `GatewayDoctor.php`, `ProductCatalog`, `ProviderWebhookTest`, `GatewayHealth`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `.handle`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ApplicationType`, `SignatureProviderManager`, `Illuminate\Http\Request`, `ProviderRoutingTest`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Why does `TestCase` connect `TestCase` to `.http`, `UsageGuardTest.php`, `Tenant`, `Province`, `RequestStatus`, `CertificatePeriod`, `ProductType`, `TenantProductsTest`, `.makeSignatureRequest`, `ProductCatalog.php`, `SessionReconciliationTest`, `ApiClient`, `AuditLog.php`, `SignatureRequest.php`, `ProviderWebhookTest`, `StrictIdentificationConfigTest`, `AuditLog`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ProviderRoutingTest`, `ApiClientAuthenticationTest`, `ClientNotification`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `ProviderAccount` (e.g. with `.checkKeys()` and `.checkProvider()`) actually correct?**
  _`ProviderAccount` has 8 INFERRED edges - model-reasoned connections that need verification._