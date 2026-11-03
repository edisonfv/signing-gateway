# Graph Report - gateway-production-guide-4d0375  (2026-11-03)

## Corpus Check
- 259 files · ~69,238 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1437 nodes · 3512 edges · 137 communities (95 shown, 42 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 291 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `94c92b1b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- IssuanceLedgerEntry
- composer.json
- AuditLog.php
- Tenant
- SignatureRequest
- Province
- Illuminate\Http\Request
- HandlesProviderSession.php
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- Closure
- SignatureFlow
- MaskSensitiveData
- firma-hmac.js
- .show
- DeliveryMode
- DrivesApiFlow.php
- Spatie\Enum\Laravel\Enum
- AutoCancellationReason
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- SessionReconciler
- RequestType
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- ProviderRequestException
- RegularRequestState
- TermsType
- ProductCatalog
- EmissionGuard
- SignatureDocument.php
- SessionRejectionCode
- Illuminate\Http\JsonResponse
- EmissionHaltedException.php
- RegularSignatureFlowTest
- RequestSignature
- LogMaskingTest
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- Symfony\Component\HttpFoundation\Response
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- RegularValidationType
- keywords
- 7. Flujos clave
- liveness.jsx
- Illuminate\Database\Eloquent\Model
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Eloquent\Concerns\HasUuids
- GatewayDoctor.php
- KeyRing
- TenantStatusController
- AuditLog
- TestCase
- ProductType
- PermissionName
- SignatureProviderManager
- IssuanceController.php
- PlatformPairingTest
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- TransactionalValidationStatus
- .handle
- setup
- UsageGuard
- config
- ProviderAccount.php
- DocumentType
- TransactionalValidationType
- psr-4
- ApiClientAuthenticationTest
- Illuminate\Foundation\Http\FormRequest
- RoleName
- SignatureProvider.php
- ProviderRoutingTest
- TransactionalRequestStatus
- StoreApiClientRequest
- EcuadorianIdentificationTest
- ClientNotification
- CloudEventType
- EnvironmentIsolationTest
- SignatureSessionResource
- AdminProvisioningTest
- NetworkHardeningTest
- TermsData
- .__invoke
- ProductOffering
- ProductCatalog.php
- SessionStatus
- SessionFailureCode

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 71 edges
4. `ProviderAccount` - 63 edges
5. `Controller` - 53 edges
6. `SignatureFlow` - 51 edges
7. `TestCase` - 49 edges
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

## Communities (137 total, 42 thin omitted)

### Community 2 - "IssuanceLedgerEntry"
Cohesion: 0.15
Nodes (4): BackfillIssuanceLedger, VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 5 - "Tenant"
Cohesion: 0.12
Nodes (5): CancelOpenRequests, Tenant, Illuminate\Database\Eloquent\Relations\HasMany, issueApiCredentials(), UsageGuardTest

### Community 6 - "SignatureRequest"
Cohesion: 0.13
Nodes (6): RequestLivenessSession, SignatureRequest, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.07
Nodes (19): City, Province, User, CityFactory, ProvinceFactory, static, UserFactory, DatabaseSeeder (+11 more)

### Community 8 - "Illuminate\Http\Request"
Cohesion: 0.21
Nodes (5): PairingController, PingController, ProviderEventController, ProductController, Illuminate\Http\Request

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.17
Nodes (16): advance(), middleware(), notifyClient(), reject(), runnableStatuses(), CreateProviderSession, DeliverClientNotification, IssueCertificate (+8 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.20
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.05
Nodes (10): self, RequestStatus, SessionStep, self, NextAction, ApiFlowJobsTest, IssuanceLedgerTest, ProviderWebhookTest (+2 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "Closure"
Cohesion: 0.28
Nodes (5): StopWhenEmissionBlocked, EcuadorianIdentification, EcuadorianRuc, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 15 - "SignatureFlow"
Cohesion: 0.16
Nodes (4): ApplicationType, CertificatePeriod, SignatureFlow, FirmaSeguraOfferings

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - ".show"
Cohesion: 0.43
Nodes (3): SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Contracts\View\View

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.11
Nodes (13): provider(), cancel(), createSession(), issueCertificate(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CertificateData (+5 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.20
Nodes (3): EvidenceType, LinkedPartnerStatus, Spatie\Enum\Laravel\Enum

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.06
Nodes (28): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+20 more)

### Community 27 - "SessionReconciler"
Cohesion: 0.22
Nodes (4): SignatureSessionRefreshController, SessionReconciler, trackSession(), SessionTracking

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.12
Nodes (9): CreateApiClient, CreateTenant, GenerateGatewayKeys, InstallGateway, PairPlatform, PurgeApplicantData, RevokeApiClient, SuspendTenant (+1 more)

### Community 32 - "ApiClient"
Cohesion: 0.12
Nodes (4): RevokeApiClient, ApiClient, self, Laravel\Sanctum\HasApiTokens

### Community 33 - "ProviderRequestException"
Cohesion: 0.12
Nodes (5): self, ProviderRequestException, TrackingBudgetExhaustedException, Illuminate\Http\Client\Response, RuntimeException

### Community 36 - "ProductCatalog"
Cohesion: 0.26
Nodes (3): SetTenantProducts, TenantProductController, ProductCatalog

### Community 38 - "SignatureDocument.php"
Cohesion: 0.18
Nodes (3): SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 53 - "Illuminate\Http\JsonResponse"
Cohesion: 0.10
Nodes (14): ClientController, ProductController, TenantController, CatalogController, CertificateController, LivenessCredentialController, LivenessResultController, LivenessSessionController (+6 more)

### Community 54 - "EmissionHaltedException.php"
Cohesion: 0.29
Nodes (3): EmissionHaltedException, LimitExceededException, Exception

### Community 56 - "RequestSignature"
Cohesion: 0.18
Nodes (5): VerifyRequestSignature, RequestSignature, Illuminate\Testing\TestResponse, signedGet(), signedJson()

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.13
Nodes (15): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 19. Monitor de salud 2026-11-03, 1. Decisiones base, 2. Topología (+7 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.06
Nodes (15): ReconcileSignatureSessions, RecordHeartbeat, QueueHeartbeat, RecordProviderCall, Heartbeat, ProviderAccount, TermsCatalog, TrackingBudget (+7 more)

### Community 63 - "Symfony\Component\HttpFoundation\Response"
Cohesion: 0.27
Nodes (4): AssignRequestContext, EnsureActiveApiClient, RestrictSourceNetwork, Symfony\Component\HttpFoundation\Response

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

### Community 78 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.27
Nodes (4): SuspendTenant, VaultEncrypted, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 86 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.11
Nodes (7): Permission, SignatureRequestTransition, TenantProduct, TermsAcceptance, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Relations\BelongsTo, Spatie\Permission\Models\Permission

### Community 87 - "GatewayDoctor.php"
Cohesion: 0.20
Nodes (4): PersonalAccessToken, Role, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Models\Role

### Community 88 - "KeyRing"
Cohesion: 0.21
Nodes (5): CreateSignatureSession, BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 90 - "AuditLog"
Cohesion: 0.16
Nodes (4): AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo, Throwable

### Community 91 - "TestCase"
Cohesion: 0.13
Nodes (9): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, fakeFirmaSegura(), testP12Base64(), DevToolsExposureTest, GatewayDoctorTest, GatewayHealthTest, PlatformCredentialTest (+1 more)

### Community 92 - "ProductType"
Cohesion: 0.23
Nodes (4): Environment, ProductType, NoProviderAvailableException, Illuminate\Support\Collection

### Community 93 - "PermissionName"
Cohesion: 0.38
Nodes (3): IssueApiClientCredentials, PermissionName, PlatformCredentialController

### Community 94 - "SignatureProviderManager"
Cohesion: 0.17
Nodes (4): CancelSignatureSession, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager

### Community 95 - "IssuanceController.php"
Cohesion: 0.14
Nodes (8): IssuanceController, ProvinceCityController, ProvinceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Resources\Json\AnonymousResourceCollection, Illuminate\Http\Resources\Json\JsonResource

### Community 96 - "PlatformPairingTest"
Cohesion: 0.15
Nodes (3): PairPlatform, PairingCode, PlatformPairingTest

### Community 97 - "Cambios en la plataforma (signature-saas) para el gateway endurecido"
Cohesion: 0.17
Nodes (11): 1. Ficha del proveedor: URL y código de emparejamiento, 2. Catálogo con la credencial de servicio, 3. Reenvío de los avisos del proveedor (nuevo), 4. Una sola URL de notificaciones (por el túnel), 5. Alta de afiliados sin credenciales huérfanas, 6. Suspender y bloquear afiliados, 7. Otros ajustes, 8. Monitor del gateway y alertas por correo (+3 more)

### Community 98 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 99 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, brianium/paratest, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery (+2 more)

### Community 102 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 103 - "UsageGuard"
Cohesion: 0.29
Nodes (5): LimitExceeded, UsageGuard, DateTimeInterface, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 104 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 106 - "DocumentType"
Cohesion: 0.21
Nodes (3): DocumentType, self, ValidDocument

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 112 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.19
Nodes (4): StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpsertTenantRequest, Illuminate\Foundation\Http\FormRequest

### Community 117 - "SignatureProvider.php"
Cohesion: 0.33
Nodes (3): offering(), static, withAccount()

### Community 128 - "SignatureSessionResource"
Cohesion: 0.19
Nodes (5): RegularCorrectionController, SignatureSessionCancellationController, SignatureSessionController, LivenessTestController, SignatureSessionResource

### Community 131 - "TermsData"
Cohesion: 0.25
Nodes (3): currentTerms(), self, TermsData

### Community 134 - "ProductOffering"
Cohesion: 0.20
Nodes (3): StoreSignatureSessionRequest, self, ProductOffering

## Knowledge Gaps
- **149 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+144 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **42 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `SignatureSessionResource`, `.http`, `IssuanceLedgerEntry`, `SignatureSessionFlowTest`, `AuditLog.php`, `Tenant`, `Illuminate\Http\Request`, `HandlesProviderSession.php`, `RequestStatus`, `SignatureFlow`, `.show`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `SessionReconciler`, `Illuminate\Console\Command`, `ApiClient`, `ProviderRequestException`, `EmissionGuard`, `Illuminate\Http\JsonResponse`, `RegularSignatureFlowTest`, `ProviderAccount`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TestCase`, `SignatureProviderManager`, `IssuanceController.php`, `UsageGuard`, `DocumentType`?**
  _High betweenness centrality (0.092) - this node is a cross-community bridge._
- **Why does `ProviderAccount` connect `ProviderAccount` to `.http`, `AuditLog.php`, `Tenant`, `ProductCatalog.php`, `Illuminate\Http\Request`, `HandlesProviderSession.php`, `RequestStatus`, `SignatureFlow`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `Illuminate\Console\Command`, `ProductCatalog`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TestCase`, `ProductType`, `SignatureProviderManager`, `.handle`, `ProviderAccount.php`, `SignatureProvider.php`, `ProviderRoutingTest`?**
  _High betweenness centrality (0.063) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `AdminProvisioningTest`, `SignatureSessionFlowTest`, `AuditLog.php`, `.__invoke`, `ProductCatalog.php`, `HandlesProviderSession.php`, `RequestStatus`, `SignatureFlow`, `.makeSignatureRequest`, `Illuminate\Console\Command`, `ApiClient`, `ProductCatalog`, `Illuminate\Http\JsonResponse`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TenantStatusController`, `TestCase`, `ProductType`, `UsageGuard`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `ProviderAccount` (e.g. with `.checkKeys()` and `.checkProvider()`) actually correct?**
  _`ProviderAccount` has 8 INFERRED edges - model-reasoned connections that need verification._