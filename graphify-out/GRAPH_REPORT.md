# Graph Report - dns-migration-server-6479af  (2026-11-04)

## Corpus Check
- 260 files · ~70,068 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1448 nodes · 3524 edges · 127 communities (91 shown, 36 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 291 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `61f2b566`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- IssuanceLedgerEntry
- composer.json
- Illuminate\Database\Eloquent\Model
- Tenant
- SignatureRequest
- Province
- ProviderAccount
- ProviderRequestException
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- EcuadorianIdentification
- ApplicationType
- MaskSensitiveData
- firma-hmac.js
- .view
- DeliveryMode
- DrivesApiFlow.php
- Spatie\Enum\Laravel\Enum
- TenantProductsTest
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- SessionReconciler
- TrackingBudget
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- SessionReconciliationTest.php
- RegularRequestState
- TermsType
- CertificateData
- EmissionGuard
- SignatureDocument.php
- SessionRejectionCode
- Illuminate\Http\JsonResponse
- GenerateGatewayKeysTest
- RegularSignatureFlowTest
- RequestSignature
- LogMaskingTest
- Signing Gateway — Arquitectura
- dev
- GatewayHealth
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
- PlatformPairingTest
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Eloquent\Concerns\HasUuids
- EvidenceType
- KeyRing
- LinkedPartnerStatus
- AuditLog
- TestCase
- ProductType
- PermissionName
- SignatureProviderManager
- Illuminate\Http\Request
- PairPlatform
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- TransactionalValidationStatus
- .handle
- setup
- config
- DocumentType
- psr-4
- ApiClientAuthenticationTest
- UpsertTenantRequest
- SignatureProvider.php
- TransactionalRequestStatus
- Illuminate\Foundation\Http\FormRequest
- ClientNotification
- CloudEventType
- EnvironmentIsolationTest
- SignatureSessionResource
- TermsData
- SessionStatus
- SessionFailureCode

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 71 edges
4. `ProviderAccount` - 63 edges
5. `Controller` - 53 edges
6. `SignatureFlow` - 51 edges
7. `TestCase` - 51 edges
8. `ProductType` - 46 edges
9. `AuditLog` - 45 edges
10. `ApiClient` - 39 edges

## Surprising Connections (you probably didn't know these)
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `GatewayHealthTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/GatewayHealthTest.php → app/Models/ProviderAccount.php

## Import Cycles
- None detected.

## Communities (127 total, 36 thin omitted)

### Community 2 - "IssuanceLedgerEntry"
Cohesion: 0.15
Nodes (4): BackfillIssuanceLedger, VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.13
Nodes (5): VaultEncrypted, InvalidStatusTransition, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model, LogicException

### Community 5 - "Tenant"
Cohesion: 0.06
Nodes (14): SuspendTenant, LimitExceeded, EmissionHaltedException, LimitExceededException, TenantStatusController, Tenant, UsageGuard, DateTimeInterface (+6 more)

### Community 6 - "SignatureRequest"
Cohesion: 0.08
Nodes (10): CancelSignatureSession, PurgeApplicantData, SignatureFlow, LivenessResultController, RegularCorrectionController, LivenessTestController, RequestLivenessSession, SignatureRequest (+2 more)

### Community 7 - "Province"
Cohesion: 0.07
Nodes (16): Permission, Province, Role, CityFactory, ProvinceFactory, static, UserFactory, DatabaseSeeder (+8 more)

### Community 8 - "ProviderAccount"
Cohesion: 0.19
Nodes (5): ReconcileSignatureSessions, ProviderAccount, TermsCatalog, static, createFirmaSeguraAccount()

### Community 9 - "ProviderRequestException"
Cohesion: 0.15
Nodes (17): ProviderRequestException, CancelOpenRequests, advance(), middleware(), notifyClient(), reject(), runnableStatuses(), CreateProviderSession (+9 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.20
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.05
Nodes (9): self, RequestStatus, SessionStep, self, ApiFlowJobsTest, IssuanceLedgerTest, ProviderWebhookTest, SessionReconciliationTest (+1 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "EcuadorianIdentification"
Cohesion: 0.22
Nodes (5): EcuadorianIdentification, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, PHPUnit\Framework\TestCase, EcuadorianIdentificationTest

### Community 15 - "ApplicationType"
Cohesion: 0.20
Nodes (3): ApplicationType, CertificatePeriod, Illuminate\Validation\Validator

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.14
Nodes (11): provider(), cancel(), createSession(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CivilRegistryResult, LivenessCredentials (+3 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.14
Nodes (4): AutoCancellationReason, RequestType, TransactionalValidationType, Spatie\Enum\Laravel\Enum

### Community 23 - ".makeSignatureRequest"
Cohesion: 0.10
Nodes (4): CertificateVaultEntry, SealedBox, AdminProvisioningTest, DataProtectionTest

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.06
Nodes (28): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+20 more)

### Community 27 - "SessionReconciler"
Cohesion: 0.29
Nodes (3): SessionReconciler, trackSession(), SessionTracking

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.13
Nodes (8): CreateApiClient, CreateTenant, GenerateGatewayKeys, InstallGateway, PairPlatform, RevokeApiClient, SuspendTenant, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.13
Nodes (3): RevokeApiClient, ApiClient, Laravel\Sanctum\HasApiTokens

### Community 38 - "SignatureDocument.php"
Cohesion: 0.19
Nodes (3): SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 53 - "Illuminate\Http\JsonResponse"
Cohesion: 0.09
Nodes (16): ClientController, ProductController, CatalogController, CertificateController, LivenessCredentialController, LivenessSessionController, PairingController, PingController (+8 more)

### Community 56 - "RequestSignature"
Cohesion: 0.22
Nodes (4): RequestSignature, Illuminate\Testing\TestResponse, signedGet(), signedJson()

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.13
Nodes (15): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 19. Monitor de salud 2026-11-03, 1. Decisiones base, 2. Topología (+7 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "GatewayHealth"
Cohesion: 0.14
Nodes (8): RecordHeartbeat, QueueHeartbeat, RecordProviderCall, Heartbeat, GatewayHealth, Carbon\CarbonInterface, Illuminate\Http\Client\Events\ConnectionFailed, Illuminate\Http\Client\Events\ResponseReceived

### Community 63 - "Closure"
Cohesion: 0.19
Nodes (7): AssignRequestContext, EnsureActiveApiClient, RestrictSourceNetwork, VerifyRequestSignature, StopWhenEmissionBlocked, Closure, Symfony\Component\HttpFoundation\Response

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
Cohesion: 0.07
Nodes (14): City, PersonalAccessToken, SignatureRequestTransition, TenantProduct, TermsAcceptance, User, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Factories\HasFactory (+6 more)

### Community 88 - "KeyRing"
Cohesion: 0.21
Nodes (5): CreateSignatureSession, BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 90 - "AuditLog"
Cohesion: 0.16
Nodes (4): AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo, Throwable

### Community 91 - "TestCase"
Cohesion: 0.11
Nodes (10): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, fakeFirmaSegura(), testP12Base64(), DevToolsExposureTest, GatewayDoctorTest, GatewayHealthTest, NetworkHardeningTest (+2 more)

### Community 92 - "ProductType"
Cohesion: 0.05
Nodes (16): SetProviderCredentials, SetTenantProducts, Environment, ProductType, Provider, NoProviderAvailableException, TenantProductController, StoreSignatureSessionRequest (+8 more)

### Community 93 - "PermissionName"
Cohesion: 0.21
Nodes (4): IssueApiClientCredentials, PermissionName, RoleName, PlatformCredentialController

### Community 94 - "SignatureProviderManager"
Cohesion: 0.19
Nodes (5): correctRegularRequest(), registerRegularRequest(), regularRequestState(), SignatureProviderManager, Illuminate\Support\Manager

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (9): IssuanceController, ProvinceCityController, ProvinceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Request, Illuminate\Http\Resources\Json\AnonymousResourceCollection (+1 more)

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
Cohesion: 0.15
Nodes (4): DocumentType, StoreRegularCorrectionRequest, self, ValidDocument

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 117 - "SignatureProvider.php"
Cohesion: 0.33
Nodes (3): offering(), static, withAccount()

### Community 122 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): StoreApiClientRequest, StoreLivenessResultRequest, NotificationTarget, Illuminate\Foundation\Http\FormRequest

### Community 124 - "ClientNotification"
Cohesion: 0.19
Nodes (4): DeliverClientNotification, ClientNotification, self, ClientNotificationDeliveryTest

### Community 128 - "SignatureSessionResource"
Cohesion: 0.17
Nodes (5): SignatureSessionCancellationController, SignatureSessionController, SignatureSessionRefreshController, SignatureSessionResource, NextAction

### Community 131 - "TermsData"
Cohesion: 0.25
Nodes (3): currentTerms(), self, TermsData

### Community 136 - "SessionStatus"
Cohesion: 0.13
Nodes (4): self, SessionStage, SessionStatus, Illuminate\Http\Client\Response

## Knowledge Gaps
- **153 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+148 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **36 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `SignatureSessionResource`, `.http`, `IssuanceLedgerEntry`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Model`, `Tenant`, `ProviderAccount`, `ProviderRequestException`, `RequestStatus`, `ApplicationType`, `.view`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `SessionReconciler`, `SessionReconciliationTest.php`, `CertificateData`, `EmissionGuard`, `Illuminate\Http\JsonResponse`, `RegularSignatureFlowTest`, `Closure`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TestCase`, `SignatureProviderManager`, `DocumentType`, `ClientNotification`?**
  _High betweenness centrality (0.101) - this node is a cross-community bridge._
- **Why does `ProviderAccount` connect `ProviderAccount` to `.http`, `Illuminate\Database\Eloquent\Model`, `Tenant`, `ProviderRequestException`, `RequestStatus`, `ApplicationType`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `TrackingBudget`, `Illuminate\Console\Command`, `SessionReconciliationTest.php`, `Illuminate\Http\JsonResponse`, `GatewayHealth`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TestCase`, `ProductType`, `SignatureProviderManager`, `.handle`, `SignatureProvider.php`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Model`, `ProviderRequestException`, `RequestStatus`, `ApplicationType`, `UpsertTenantRequest`, `ClientNotification`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `.makeSignatureRequest`, `Illuminate\Foundation\Http\FormRequest`, `TestCase`, `ProductType`, `PermissionName`, `Illuminate\Console\Command`?**
  _High betweenness centrality (0.049) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `ProviderAccount` (e.g. with `.checkKeys()` and `.checkProvider()`) actually correct?**
  _`ProviderAccount` has 8 INFERRED edges - model-reasoned connections that need verification._