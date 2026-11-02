# Graph Report - gateway-production-guide-4d0375  (2026-11-02)

## Corpus Check
- 211 files · ~54,346 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1156 nodes · 2642 edges · 105 communities (78 shown, 27 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 211 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `dc6cb96a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- composer.json
- Environment
- Tenant
- SignatureRequest
- Province
- ProductType
- HandlesProviderSession.php
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- DocumentType
- SignatureFlow
- MaskSensitiveData
- firma-hmac.js
- Controller
- DeliveryMode
- DrivesApiFlow.php
- EvidenceType
- CloudEventType
- Illuminate\Database\Seeder
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- Spatie\Enum\Laravel\Enum
- ClientNotification
- RequestType
- RevocationStatus
- .__invoke
- RegularValidationStatus
- AuditLog
- SessionStage
- RegularRequestState
- TermsType
- TransactionalRequestStatus
- LivenessTestController.php
- AutoCancellationReason
- SessionRejectionCode
- SignatureProviderManager
- User.php
- RegularSignatureFlowTest
- SignatureDocument.php
- autoload-dev
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- Illuminate\Database\Eloquent\Concerns\HasUuids
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- SessionStatus
- 7. Flujos clave
- liveness.jsx
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- CertificatePeriod
- KeyRing
- ProductCatalog
- ProviderRequestException
- TestCase
- RuntimeException
- Illuminate\Database\Eloquent\Model
- TermsData
- ProvinceCityController.php
- .__invoke
- require
- require-dev
- TransactionalValidationStatus
- setup
- config
- psr-4
- LinkedPartnerStatus
- TransactionalValidationType

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 120 edges
2. `RequestStatus` - 66 edges
3. `Tenant` - 56 edges
4. `SignatureFlow` - 43 edges
5. `ProviderAccount` - 42 edges
6. `ProductType` - 39 edges
7. `Controller` - 39 edges
8. `AuditLog` - 33 edges
9. `TestCase` - 31 edges
10. `ApiClient` - 30 edges

## Surprising Connections (you probably didn't know these)
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `SessionReconciliationTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/SessionReconciliationTest.php → app/Models/ProviderAccount.php

## Import Cycles
- None detected.

## Communities (105 total, 27 thin omitted)

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

### Community 4 - "Environment"
Cohesion: 0.29
Nodes (3): SetProviderCredentials, Environment, Provider

### Community 5 - "Tenant"
Cohesion: 0.06
Nodes (13): LimitExceeded, LimitExceededException, Tenant, TenantProduct, UsageGuard, DateTimeInterface, Exception, Illuminate\Database\Eloquent\Relations\HasMany (+5 more)

### Community 6 - "SignatureRequest"
Cohesion: 0.11
Nodes (8): SignatureRequest, SessionReconciler, trackSession(), correctRegularRequest(), registerRegularRequest(), regularRequestState(), SessionTracking, Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.22
Nodes (6): Province, CityFactory, ProvinceFactory, static, UserFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 8 - "ProductType"
Cohesion: 0.11
Nodes (7): ApplicationType, ProductType, StoreSignatureSessionRequest, offering(), self, ProductOffering, FirmaSeguraOfferings

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.20
Nodes (15): middleware(), runnableStatuses(), CreateProviderSession, DeliverClientNotification, IssueCertificate, ReconcileSignatureSession, RequestLivenessSession, SubmitFaceLiveness (+7 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.20
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.06
Nodes (9): self, RequestStatus, SessionStep, advance(), reject(), CertificateVaultEntry, ApiFlowJobsTest, DataProtectionTest (+1 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "DocumentType"
Cohesion: 0.06
Nodes (18): DocumentType, TenantController, AssignRequestContext, EnsureActiveApiClient, StoreApiClientRequest, StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpsertTenantRequest (+10 more)

### Community 15 - "SignatureFlow"
Cohesion: 0.16
Nodes (4): SignatureFlow, LivenessResultController, LivenessSessionController, NextAction

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "Controller"
Cohesion: 0.14
Nodes (11): ProductController, CatalogController, CertificateController, LivenessCredentialController, PingController, ProductController, TermsController, Controller (+3 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.11
Nodes (13): provider(), cancel(), createSession(), issueCertificate(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CertificateData (+5 more)

### Community 23 - "Illuminate\Database\Seeder"
Cohesion: 0.36
Nodes (4): DatabaseSeeder, ProvinceSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.07
Nodes (25): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+17 more)

### Community 26 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.24
Nodes (3): RegularRequestStatus, RegularValidationType, Spatie\Enum\Laravel\Enum

### Community 27 - "ClientNotification"
Cohesion: 0.17
Nodes (4): notifyClient(), ClientNotification, self, ClientNotificationDeliveryTest

### Community 30 - ".__invoke"
Cohesion: 0.15
Nodes (3): City, sessionPayload(), LocationCatalogTest

### Community 32 - "AuditLog"
Cohesion: 0.07
Nodes (17): IssueApiClientCredentials, RevokeApiClient, CreateApiClient, CreateTenant, GenerateGatewayKeys, PurgeApplicantData, RevokeApiClient, SetTenantProducts (+9 more)

### Community 53 - "SignatureProviderManager"
Cohesion: 0.15
Nodes (5): CreateSignatureSession, BlindIndex, ProviderRouter, SignatureProviderManager, Illuminate\Support\Collection

### Community 54 - "User.php"
Cohesion: 0.36
Nodes (5): User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles

### Community 56 - "SignatureDocument.php"
Cohesion: 0.22
Nodes (3): SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 57 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.10
Nodes (9): ReconcileSignatureSessions, ProviderAccount, TermsCatalog, TrackingBudget, static, withAccount(), static, createFirmaSeguraAccount() (+1 more)

### Community 63 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.19
Nodes (8): Permission, PersonalAccessToken, Role, RolesAndPermissionsSeeder, Illuminate\Database\Eloquent\Concerns\HasUuids, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role

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

### Community 70 - "SessionStatus"
Cohesion: 0.22
Nodes (3): self, SessionStatus, Illuminate\Http\Client\Response

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 87 - "CertificatePeriod"
Cohesion: 0.14
Nodes (4): CertificatePeriod, UpdateTenantProductsRequest, Illuminate\Support\Manager, Illuminate\Validation\Validator

### Community 88 - "KeyRing"
Cohesion: 0.36
Nodes (3): KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 90 - "ProviderRequestException"
Cohesion: 0.15
Nodes (6): ProviderRequestException, RegularCorrectionController, SignatureSessionCancellationController, SignatureSessionController, SignatureSessionRefreshController, SignatureSessionResource

### Community 91 - "TestCase"
Cohesion: 0.06
Nodes (14): VerifyRequestSignature, RequestSignature, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Testing\TestResponse, fakeFirmaSegura(), testP12Base64(), signedGet() (+6 more)

### Community 92 - "RuntimeException"
Cohesion: 0.25
Nodes (3): NoProviderAvailableException, TrackingBudgetExhaustedException, RuntimeException

### Community 93 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.25
Nodes (4): VaultEncrypted, SignatureRequestTransition, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model

### Community 94 - "TermsData"
Cohesion: 0.13
Nodes (6): InvalidStatusTransition, self, currentTerms(), self, TermsData, LogicException

### Community 95 - "ProvinceCityController.php"
Cohesion: 0.22
Nodes (6): ProvinceCityController, ProvinceController, CityResource, ProvinceResource, Illuminate\Http\Resources\Json\AnonymousResourceCollection, Illuminate\Http\Resources\Json\JsonResource

### Community 96 - ".__invoke"
Cohesion: 0.15
Nodes (3): PermissionName, RoleName, SealedBox

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

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

## Knowledge Gaps
- **133 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+128 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **27 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Tenant`, `HandlesProviderSession.php`, `RequestStatus`, `DocumentType`, `SignatureFlow`, `Controller`, `DrivesApiFlow.php`, `ClientNotification`, `.__invoke`, `AuditLog`, `LivenessTestController.php`, `SignatureProviderManager`, `RegularSignatureFlowTest`, `ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `CertificatePeriod`, `ProviderRequestException`, `TestCase`, `Illuminate\Database\Eloquent\Model`, `TermsData`?**
  _High betweenness centrality (0.145) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `.__invoke`, `AuditLog`, `SignatureSessionFlowTest`, `ProductType`, `ClientNotification`, `RequestStatus`, `DocumentType`, `Controller`, `SignatureProviderManager`, `ProductCatalog`, `TestCase`, `Illuminate\Database\Eloquent\Model`, `ProviderAccount`, `Illuminate\Database\Eloquent\Concerns\HasUuids`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Why does `RequestStatus` connect `RequestStatus` to `AuditLog`, `SignatureSessionFlowTest`, `ProviderRequestException`, `LivenessTestController.php`, `SignatureRequest`, `Tenant`, `HandlesProviderSession.php`, `SignatureFlow`, `Controller`, `.__invoke`, `TermsData`, `Spatie\Enum\Laravel\Enum`, `TestCase`, `ProviderAccount`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Are the 14 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 14 INFERRED edges - model-reasoned connections that need verification._
- **Are the 32 inferred relationships involving `RequestStatus` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`RequestStatus` has 32 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _133 weakly-connected nodes found - possible documentation gaps or missing edges._