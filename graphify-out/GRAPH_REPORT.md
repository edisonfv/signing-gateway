# Graph Report - gateway-production-guide-4d0375  (2026-11-02)

## Corpus Check
- 250 files · ~66,262 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1378 nodes · 3342 edges · 139 communities (95 shown, 44 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 282 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2b93841c`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- SignatureRequest.php
- Tenant
- SignatureRequest
- Province
- ProductOffering
- HandlesProviderSession.php
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- EcuadorianIdentification
- ApplicationType
- MaskSensitiveData
- firma-hmac.js
- SignatureFlow
- DeliveryMode
- DrivesApiFlow.php
- EvidenceType
- Spatie\Enum\Laravel\Enum
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- ProviderWebhookTest.php
- RequestType
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- ProviderRequestException
- RegularRequestState
- TermsType
- LinkedPartnerStatus
- EmissionGuard
- CertificateVaultEntry
- SessionRejectionCode
- Controller
- LimitExceededException.php
- RegularSignatureFlowTest
- InteractsWithSignedApi.php
- TestCase
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- ApiClient.php
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
- User.php
- KeyRing
- Illuminate\Http\JsonResponse
- AuditLog
- Illuminate\Foundation\Testing\RefreshDatabase
- GatewayDoctor.php
- PermissionName
- SignatureProviderManager
- Illuminate\Http\Request
- PlatformPairingTest
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- TransactionalValidationStatus
- .handle
- setup
- TenantSuspensionTest
- config
- PairingCode
- DocumentType
- TransactionalValidationType
- psr-4
- ApiClientAuthenticationTest
- Illuminate\Foundation\Http\FormRequest
- RoleName
- SessionReconciliationTest
- ProductType
- TransactionalRequestStatus
- RevokeApiClient
- ClientNotification
- IssuanceLedgerTest
- CertificatePeriod
- ProviderWebhookTest
- SignatureSessionResource
- AdminProvisioningTest
- NetworkHardeningTest
- TermsData
- .makeRequest
- IssueApiClientCredentials.php
- StoreSignatureSessionRequest
- UpdateTenantProductsRequest
- .handle
- Illuminate\Http\Client\Response
- SessionFailureCode

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
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `IssuanceLedgerTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/IssuanceLedgerTest.php → app/Models/ProviderAccount.php

## Import Cycles
- None detected.

## Communities (139 total, 44 thin omitted)

### Community 2 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (5): VerifyIssuanceLedger, IssuanceLedgerEntry, TenantProduct, IssuanceLedger, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "SignatureRequest.php"
Cohesion: 0.19
Nodes (3): InvalidStatusTransition, self, LogicException

### Community 5 - "Tenant"
Cohesion: 0.08
Nodes (11): SetTenantProducts, LimitExceeded, TenantProductController, Tenant, UsageGuard, ProductCatalog, DateTimeInterface, Illuminate\Database\Eloquent\Relations\HasMany (+3 more)

### Community 6 - "SignatureRequest"
Cohesion: 0.12
Nodes (7): SignatureSessionRefreshController, SignatureRequest, SessionReconciler, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.12
Nodes (7): City, Province, CityFactory, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\HasFactory, sessionPayload(), LocationCatalogTest

### Community 8 - "ProductOffering"
Cohesion: 0.20
Nodes (3): self, ProductOffering, FirmaSeguraOfferings

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.23
Nodes (15): CancelOpenRequests, advance(), middleware(), notifyClient(), reject(), runnableStatuses(), CreateProviderSession, IssueCertificate (+7 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.22
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.12
Nodes (3): self, RequestStatus, SessionStep

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "EcuadorianIdentification"
Cohesion: 0.22
Nodes (5): EcuadorianIdentification, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, PHPUnit\Framework\TestCase, EcuadorianIdentificationTest

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "SignatureFlow"
Cohesion: 0.12
Nodes (5): CancelSignatureSession, PurgeApplicantData, SignatureFlow, NextAction, Illuminate\Contracts\View\View

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.09
Nodes (15): provider(), cancel(), createSession(), issueCertificate(), requestLivenessSession(), submitLiveness(), trackSession(), validateCivilRegistry() (+7 more)

### Community 22 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.24
Nodes (3): AutoCancellationReason, CloudEventType, Spatie\Enum\Laravel\Enum

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.06
Nodes (28): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+20 more)

### Community 27 - "ProviderWebhookTest.php"
Cohesion: 0.16
Nodes (5): DeliverClientNotification, ReconcileSignatureSession, Illuminate\Contracts\Queue\ShouldBeUnique, RuntimeException, Throwable

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.13
Nodes (8): CreateApiClient, CreateTenant, GenerateGatewayKeys, InstallGateway, PairPlatform, RevokeApiClient, SuspendTenant, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.18
Nodes (4): ApiClient, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Laravel\Sanctum\HasApiTokens

### Community 38 - "CertificateVaultEntry"
Cohesion: 0.13
Nodes (4): CertificateVaultEntry, SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 53 - "Controller"
Cohesion: 0.10
Nodes (11): ProductController, TenantController, CatalogController, LivenessCredentialController, LivenessResultController, LivenessSessionController, PingController, RegularCorrectionController (+3 more)

### Community 54 - "LimitExceededException.php"
Cohesion: 0.28
Nodes (3): EmissionHaltedException, LimitExceededException, Exception

### Community 56 - "InteractsWithSignedApi.php"
Cohesion: 0.14
Nodes (6): NotificationTarget, RequestSignature, Illuminate\Testing\TestResponse, issueApiCredentials(), signedGet(), signedJson()

### Community 57 - "TestCase"
Cohesion: 0.20
Nodes (4): Illuminate\Foundation\Testing\TestCase, DevToolsExposureTest, LogMaskingTest, TestCase

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.14
Nodes (14): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto (+6 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.10
Nodes (7): ReconcileSignatureSessions, ProviderAccount, TrackingBudget, static, withAccount(), static, TenantProductsTest

### Community 63 - "ApiClient.php"
Cohesion: 0.20
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

### Community 78 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.21
Nodes (4): VaultEncrypted, SignatureRequestTransition, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 86 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.21
Nodes (6): Permission, Role, TermsAcceptance, Illuminate\Database\Eloquent\Concerns\HasUuids, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role

### Community 87 - "User.php"
Cohesion: 0.21
Nodes (6): PersonalAccessToken, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Traits\HasRoles

### Community 88 - "KeyRing"
Cohesion: 0.24
Nodes (4): BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 89 - "Illuminate\Http\JsonResponse"
Cohesion: 0.21
Nodes (6): SuspendTenant, TenantStatusController, ProductController, ProviderEventController, LivenessTestController, Illuminate\Http\JsonResponse

### Community 90 - "AuditLog"
Cohesion: 0.13
Nodes (6): BackfillIssuanceLedger, CertificateController, StopWhenEmissionBlocked, AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 91 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.18
Nodes (6): Illuminate\Foundation\Testing\RefreshDatabase, createFirmaSeguraAccount(), fakeFirmaSegura(), testP12Base64(), GatewayDoctorTest, PlatformCredentialTest

### Community 92 - "GatewayDoctor.php"
Cohesion: 0.14
Nodes (5): SetProviderCredentials, Environment, Provider, NoProviderAvailableException, Illuminate\Support\Collection

### Community 93 - "PermissionName"
Cohesion: 0.18
Nodes (5): IssueApiClientCredentials, PairPlatform, PermissionName, PlatformCredentialController, PairingController

### Community 94 - "SignatureProviderManager"
Cohesion: 0.17
Nodes (5): CreateSignatureSession, TermsCatalog, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.14
Nodes (10): IssuanceController, ProductController, ProvinceCityController, ProvinceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Request (+2 more)

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

### Community 105 - "PairingCode"
Cohesion: 0.18
Nodes (5): PairingCode, ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 106 - "DocumentType"
Cohesion: 0.21
Nodes (3): DocumentType, self, ValidDocument

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 112 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.18
Nodes (4): StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpsertTenantRequest, Illuminate\Foundation\Http\FormRequest

### Community 116 - "RoleName"
Cohesion: 0.21
Nodes (5): RoleName, DatabaseSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 118 - "ProductType"
Cohesion: 0.19
Nodes (3): ProductType, offering(), ProviderRoutingTest

### Community 122 - "RevokeApiClient"
Cohesion: 0.21
Nodes (3): RevokeApiClient, ClientController, StoreApiClientRequest

### Community 128 - "SignatureSessionResource"
Cohesion: 0.29
Nodes (3): SignatureSessionCancellationController, SignatureSessionController, SignatureSessionResource

### Community 131 - "TermsData"
Cohesion: 0.25
Nodes (3): currentTerms(), self, TermsData

## Knowledge Gaps
- **147 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+142 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **44 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `SignatureSessionResource`, `.http`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `SignatureSessionFlowTest`, `SignatureRequest.php`, `Tenant`, `.makeRequest`, `Province`, `HandlesProviderSession.php`, `RequestStatus`, `ApplicationType`, `SignatureFlow`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `ProviderWebhookTest.php`, `ApiClient`, `ProviderRequestException`, `EmissionGuard`, `Controller`, `RegularSignatureFlowTest`, `ProviderAccount`, `ApiClient.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\JsonResponse`, `AuditLog`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SignatureProviderManager`, `TenantSuspensionTest`, `DocumentType`, `Illuminate\Foundation\Http\FormRequest`, `SessionReconciliationTest`, `RequestStatus.php`, `ClientNotification`, `IssuanceLedgerTest`, `CertificatePeriod`, `ProviderWebhookTest`?**
  _High betweenness centrality (0.097) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `AdminProvisioningTest`, `SignatureSessionFlowTest`, `SignatureRequest.php`, `IssueApiClientCredentials.php`, `.makeRequest`, `HandlesProviderSession.php`, `ApplicationType`, `.makeSignatureRequest`, `Illuminate\Console\Command`, `Controller`, `InteractsWithSignedApi.php`, `ApiClient.php`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Illuminate\Http\JsonResponse`, `AuditLog`, `GatewayDoctor.php`, `PermissionName`, `ProductType`, `RevokeApiClient`, `ClientNotification`, `IssuanceLedgerTest`?**
  _High betweenness centrality (0.053) - this node is a cross-community bridge._
- **Why does `RequestStatus` connect `RequestStatus` to `SignatureSessionResource`, `SignatureSessionFlowTest`, `SignatureRequest.php`, `.makeRequest`, `SignatureRequest`, `Province`, `.handle`, `HandlesProviderSession.php`, `Tenant`, `ApplicationType`, `SignatureFlow`, `Spatie\Enum\Laravel\Enum`, `.makeSignatureRequest`, `ProviderWebhookTest.php`, `Controller`, `ProviderAccount`, `Illuminate\Http\JsonResponse`, `AuditLog`, `Illuminate\Foundation\Testing\RefreshDatabase`, `TenantSuspensionTest`, `Illuminate\Foundation\Http\FormRequest`, `SessionReconciliationTest`, `RequestStatus.php`, `IssuanceLedgerTest`, `CertificatePeriod`, `ProviderWebhookTest`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _147 weakly-connected nodes found - possible documentation gaps or missing edges._