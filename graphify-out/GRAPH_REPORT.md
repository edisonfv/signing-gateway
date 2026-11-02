# Graph Report - gateway-production-guide-4d0375  (2026-11-02)

## Corpus Check
- 222 files · ~57,664 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1220 nodes · 2887 edges · 105 communities (79 shown, 26 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 229 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `54f75ac6`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Concerns\HasUuids
- composer.json
- .__invoke
- Tenant
- SignatureRequest
- Province
- SignatureFlow
- HandlesProviderSession.php
- SessionReconciler
- RequestStatus
- scripts
- DocumentType
- AuditLog.php
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
- .handle
- RegularRequestState
- TermsType
- Spatie\Enum\Laravel\Enum
- TermsCatalog
- Signing Gateway
- SessionRejectionCode
- SignatureProviderManager
- UsageGuard.php
- RegularSignatureFlowTest
- ApiClientAuthenticationTest
- LogMaskingTest
- Signing Gateway — Arquitectura
- dev
- ProviderAccount
- ProviderRoutingTest
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
- Illuminate\Foundation\Http\FormRequest
- KeyRing
- ProductCatalog
- AuditLog
- TestCase
- ProductType
- TermsData
- Illuminate\Http\Request
- .__invoke
- require
- require-dev
- TransactionalValidationStatus
- setup
- config
- psr-4
- LinkedPartnerStatus

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 129 edges
2. `RequestStatus` - 83 edges
3. `Tenant` - 60 edges
4. `ProviderAccount` - 50 edges
5. `SignatureFlow` - 48 edges
6. `ProductType` - 45 edges
7. `Controller` - 43 edges
8. `AuditLog` - 36 edges
9. `TestCase` - 35 edges
10. `ApplicationType` - 33 edges

## Surprising Connections (you probably didn't know these)
- `sessionPayload()` --calls--> `City`  [INFERRED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/City.php
- `issueApiCredentials()` --calls--> `Tenant`  [INFERRED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `createFirmaSeguraAccount()` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Concerns/FakesFirmaSegura.php → app/Models/ProviderAccount.php
- `ProviderWebhookTest` --references--> `ProviderAccount`  [EXTRACTED]
  tests/Feature/ProviderWebhookTest.php → app/Models/ProviderAccount.php

## Import Cycles
- None detected.

## Communities (105 total, 26 thin omitted)

### Community 0 - ".http"
Cohesion: 0.21
Nodes (3): FirmaSeguraDriver, static, Illuminate\Http\Client\PendingRequest

### Community 2 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.10
Nodes (10): VaultEncrypted, City, SignatureRequestTransition, TenantProduct, TermsAcceptance, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Model (+2 more)

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - ".__invoke"
Cohesion: 0.29
Nodes (3): SetProviderCredentials, Provider, FirmaSeguraWebhookController

### Community 5 - "Tenant"
Cohesion: 0.15
Nodes (3): Tenant, issueApiCredentials(), UsageGuardTest

### Community 6 - "SignatureRequest"
Cohesion: 0.13
Nodes (5): SignatureRequest, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.06
Nodes (19): PersonalAccessToken, Province, User, CityFactory, ProvinceFactory, static, UserFactory, DatabaseSeeder (+11 more)

### Community 8 - "SignatureFlow"
Cohesion: 0.12
Nodes (8): ApplicationType, CertificatePeriod, SignatureFlow, self, ProductOffering, FirmaSeguraOfferings, Illuminate\Validation\Validator, Spatie\Enum\Enum

### Community 9 - "HandlesProviderSession.php"
Cohesion: 0.07
Nodes (25): ReconcileSignatureSessions, SessionStep, self, ProviderRequestException, LivenessTestController, advance(), middleware(), notifyClient() (+17 more)

### Community 10 - "SessionReconciler"
Cohesion: 0.08
Nodes (12): SignatureSessionRefreshController, ApiDocsServiceProvider, AppServiceProvider, SessionReconciler, TrackingBudget, trackSession(), SessionTracking, SessionStatus (+4 more)

### Community 12 - "RequestStatus"
Cohesion: 0.06
Nodes (9): self, RequestStatus, self, ClientNotification, self, ApiFlowJobsTest, ClientNotificationDeliveryTest, ProviderWebhookTest (+1 more)

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "DocumentType"
Cohesion: 0.08
Nodes (12): DocumentType, self, SignatureDocument, EcuadorianIdentification, EcuadorianRuc, ValidDocument, Closure, Illuminate\Contracts\Validation\ValidationRule (+4 more)

### Community 15 - "AuditLog.php"
Cohesion: 0.16
Nodes (3): InvalidStatusTransition, TrackingBudgetExhaustedException, LogicException

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "Controller"
Cohesion: 0.10
Nodes (14): ProductController, TenantController, TenantProductController, CatalogController, CertificateController, LivenessCredentialController, LivenessResultController, LivenessSessionController (+6 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.11
Nodes (13): provider(), cancel(), createSession(), issueCertificate(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CertificateData (+5 more)

### Community 23 - ".makeSignatureRequest"
Cohesion: 0.10
Nodes (4): CertificateVaultEntry, SealedBox, AdminProvisioningTest, DataProtectionTest

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.12
Nodes (17): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 2. Configuración por cliente (admin del SaaS), 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales (+9 more)

### Community 27 - "IssuanceLedgerEntry"
Cohesion: 0.19
Nodes (3): VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.19
Nodes (7): IssueApiClientCredentials, BackfillIssuanceLedger, CreateApiClient, CreateTenant, PurgeApplicantData, SetTenantProducts, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.15
Nodes (7): RevokeApiClient, RevokeApiClient, ClientController, ApiClient, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Laravel\Sanctum\HasApiTokens

### Community 36 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.15
Nodes (4): AutoCancellationReason, TransactionalRequestStatus, TransactionalValidationType, Spatie\Enum\Laravel\Enum

### Community 37 - "TermsCatalog"
Cohesion: 0.22
Nodes (5): CreateSignatureSession, BlindIndex, TermsCatalog, UsageGuard, DateTimeInterface

### Community 38 - "Signing Gateway"
Cohesion: 0.17
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 53 - "SignatureProviderManager"
Cohesion: 0.16
Nodes (5): offering(), static, withAccount(), SignatureProviderManager, Illuminate\Support\Manager

### Community 54 - "UsageGuard.php"
Cohesion: 0.24
Nodes (5): LimitExceeded, LimitExceededException, Exception, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.12
Nodes (6): ProviderAccount, createFirmaSeguraAccount(), fakeFirmaSegura(), testP12Base64(), IssuanceLedgerTest, TenantProductsTest

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

### Community 87 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.12
Nodes (6): RegularCorrectionController, StoreApiClientRequest, StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpdateTenantProductsRequest, Illuminate\Foundation\Http\FormRequest

### Community 88 - "KeyRing"
Cohesion: 0.23
Nodes (4): GenerateGatewayKeys, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 90 - "AuditLog"
Cohesion: 0.17
Nodes (4): SignatureSessionCancellationController, AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo

### Community 91 - "TestCase"
Cohesion: 0.17
Nodes (5): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, RuntimeException, DevToolsExposureTest, TestCase

### Community 92 - "ProductType"
Cohesion: 0.23
Nodes (5): Environment, ProductType, NoProviderAvailableException, ProviderRouter, Illuminate\Support\Collection

### Community 94 - "TermsData"
Cohesion: 0.18
Nodes (3): currentTerms(), self, TermsData

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.06
Nodes (21): IssuanceController, ProvinceCityController, ProvinceController, SignatureSessionController, AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, StoreSignatureSessionRequest (+13 more)

### Community 96 - ".__invoke"
Cohesion: 0.12
Nodes (7): PermissionName, RoleName, Permission, Role, RolesAndPermissionsSeeder, Spatie\Permission\Models\Permission, Spatie\Permission\Models\Role

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
- **134 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+129 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **26 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `.__invoke`, `Tenant`, `Province`, `SignatureFlow`, `HandlesProviderSession.php`, `SessionReconciler`, `RequestStatus`, `DocumentType`, `AuditLog.php`, `Controller`, `DrivesApiFlow.php`, `.makeSignatureRequest`, `IssuanceLedgerEntry`, `Illuminate\Console\Command`, `ApiClient`, `TermsCatalog`, `SignatureProviderManager`, `RegularSignatureFlowTest`, `ProviderAccount`, `Illuminate\Foundation\Http\FormRequest`, `AuditLog`, `TestCase`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.142) - this node is a cross-community bridge._
- **Why does `RequestStatus` connect `RequestStatus` to `.handle`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `SignatureSessionFlowTest`, `Spatie\Enum\Laravel\Enum`, `Tenant`, `SignatureRequest`, `SignatureFlow`, `HandlesProviderSession.php`, `SessionReconciler`, `AuditLog.php`, `Controller`, `.makeSignatureRequest`, `Illuminate\Foundation\Http\FormRequest`, `ProviderAccount`, `AuditLog`, `TestCase`, `Illuminate\Console\Command`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Why does `Tenant` connect `Tenant` to `.__invoke`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `TermsCatalog`, `Province`, `SignatureFlow`, `RequestStatus`, `Controller`, `UsageGuard.php`, `.makeSignatureRequest`, `ProductCatalog`, `TestCase`, `ProductType`, `Illuminate\Console\Command`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 33 inferred relationships involving `RequestStatus` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`RequestStatus` has 33 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `ProviderAccount` (e.g. with `.handle()` and `.makeSignatureRequest()`) actually correct?**
  _`ProviderAccount` has 4 INFERRED edges - model-reasoned connections that need verification._