# Graph Report - dns-migration-server-6479af  (2026-11-05)

## Corpus Check
- 268 files · ~76,219 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1529 nodes · 3687 edges · 153 communities (98 shown, 55 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 291 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5d01e841`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- .http
- UsageGuardTest.php
- IssuanceLedgerEntry
- composer.json
- Illuminate\Database\Eloquent\Model
- Tenant
- SignatureFlow
- Province
- bootstrap.sh
- SignatureRequest.php
- ApiDocsServiceProvider.php
- RequestStatus
- scripts
- Closure
- TenantSuspensionTest
- MaskSensitiveData
- firma-hmac.js
- IssuanceLedgerTest
- DeliveryMode
- DrivesApiFlow.php
- Spatie\Enum\Laravel\Enum
- TenantProductsTest
- .makeSignatureRequest
- LinkedPartnerCertificateType
- Integración del SaaS con el Signing Gateway
- RegularRequestStatus
- Environment
- SessionReconciliationTest
- RevocationStatus
- Illuminate\Console\Command
- RegularValidationStatus
- ApiClient
- AuditLog.php
- SignatureRequest
- ProviderAccount.php
- ProductCatalog
- ProviderWebhookTest
- DocumentType
- SessionRejectionCode
- Illuminate\Http\JsonResponse
- TestCase
- CertificatePeriod
- RequestSignature
- LivenessTestController.php
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
- RegularSignatureFlowTest
- 3. Abstracción multi‑proveedor
- SessionErrorCode
- Illuminate\Database\Eloquent\Concerns\HasUuids
- EvidenceType
- .handle
- TenantStatusController
- PairPlatform
- Illuminate\Foundation\Testing\RefreshDatabase
- ProductType
- PermissionName
- SignatureProviderManager
- Illuminate\Http\Request
- AuditLog
- Cambios en la plataforma (signature-saas) para el gateway endurecido
- require
- require-dev
- EcuadorianIdentificationTest
- SignatureSessionFlowTest
- setup
- Signing Gateway — Instalación del servidor
- config
- AutoCancellationReason
- ApiClient.php
- RequestType
- psr-4
- ApiClientAuthenticationTest
- ProductCatalog.php
- InteractsWithSignedApi.php
- SessionStep
- SessionReconciler
- TransactionalRequestStatus
- .store
- Signing Gateway
- ClientNotification
- CloudEventType
- CreateSignatureSession
- EmissionGuard
- AdminProvisioningTest
- KeyRing
- GatewayHealthTest
- NetworkHardeningTest
- PlatformPairingTest
- PlatformTlsTrustTest
- .createRequest
- SessionStatus
- tls-rotate.sh
- SessionFailureCode
- CancelSignatureSession
- TenantProduct
- CertificateData
- GatewayDoctor.php
- LimitExceeded.php
- .show
- RegularRequestState
- UsageGuard
- 2. Configuración por cliente (admin del SaaS)
- LogMaskingTest
- LinkedPartnerStatus
- TermsType
- .handle
- gw

## God Nodes (most connected - your core abstractions)
1. `SignatureRequest` - 137 edges
2. `RequestStatus` - 91 edges
3. `Tenant` - 71 edges
4. `ProviderAccount` - 63 edges
5. `Controller` - 55 edges
6. `TestCase` - 55 edges
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

## Communities (153 total, 55 thin omitted)

### Community 1 - "UsageGuardTest.php"
Cohesion: 0.24
Nodes (3): EmissionHaltedException, LimitExceededException, Exception

### Community 2 - "IssuanceLedgerEntry"
Cohesion: 0.23
Nodes (3): VerifyIssuanceLedger, IssuanceLedgerEntry, IssuanceLedger

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.22
Nodes (4): SuspendTenant, VaultEncrypted, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Model

### Community 5 - "Tenant"
Cohesion: 0.15
Nodes (3): CancelOpenRequests, Tenant, Illuminate\Database\Eloquent\Relations\HasMany

### Community 6 - "SignatureFlow"
Cohesion: 0.12
Nodes (7): SignatureFlow, LivenessResultController, LivenessSessionController, RegularCorrectionController, SignatureSessionController, SignatureSessionRefreshController, SignatureSessionResource

### Community 7 - "Province"
Cohesion: 0.06
Nodes (16): RoleName, City, Province, CityFactory, ProvinceFactory, static, UserFactory, DatabaseSeeder (+8 more)

### Community 8 - "bootstrap.sh"
Cohesion: 0.28
Nodes (22): artisan(), as_app(), ask(), configure(), fail(), fcontext(), info(), install_app() (+14 more)

### Community 9 - "SignatureRequest.php"
Cohesion: 0.19
Nodes (14): middleware(), runnableStatuses(), CreateProviderSession, DeliverClientNotification, IssueCertificate, ReconcileSignatureSession, RequestLivenessSession, SubmitFaceLiveness (+6 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.20
Nodes (6): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Illuminate\Support\ServiceProvider, Parameter

### Community 12 - "RequestStatus"
Cohesion: 0.14
Nodes (3): self, RequestStatus, ApiFlowJobsTest

### Community 13 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 14 - "Closure"
Cohesion: 0.27
Nodes (5): EcuadorianIdentification, EcuadorianRuc, ValidDocument, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 17 - "firma-hmac.js"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 20 - "DrivesApiFlow.php"
Cohesion: 0.09
Nodes (15): provider(), cancel(), createSession(), currentTerms(), requestLivenessSession(), submitLiveness(), validateCivilRegistry(), CivilRegistryResult (+7 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.24
Nodes (3): TransactionalValidationStatus, TransactionalValidationType, Spatie\Enum\Laravel\Enum

### Community 25 - "Integración del SaaS con el Signing Gateway"
Cohesion: 0.14
Nodes (14): 10. Firmas Regulares (REGULAR), 11. Notificaciones del gateway hacia el SaaS, 12. Consideraciones técnicas para el SaaS, 13. Checklist de implementación, 1. Qué es el gateway y qué NO debe hacer el SaaS, 3. Autenticación de cada petición al gateway, 4. Elegir el flujo según las condiciones comerciales, 5. Catálogos (+6 more)

### Community 27 - "Environment"
Cohesion: 0.23
Nodes (4): Environment, NoProviderAvailableException, ProviderRouter, Illuminate\Support\Collection

### Community 30 - "Illuminate\Console\Command"
Cohesion: 0.15
Nodes (7): BackfillIssuanceLedger, CreateApiClient, GenerateGatewayKeys, PairPlatform, RevokeApiClient, SuspendTenant, Illuminate\Console\Command

### Community 32 - "ApiClient"
Cohesion: 0.17
Nodes (4): RevokeApiClient, ApiClient, SignatureRequestPolicy, Illuminate\Auth\Access\Response

### Community 33 - "AuditLog.php"
Cohesion: 0.10
Nodes (7): self, ProviderRequestException, TrackingBudgetExhaustedException, advance(), notifyClient(), reject(), RuntimeException

### Community 34 - "SignatureRequest"
Cohesion: 0.13
Nodes (5): SignatureRequest, correctRegularRequest(), registerRegularRequest(), regularRequestState(), Illuminate\Database\Eloquent\Relations\HasOne

### Community 35 - "ProviderAccount.php"
Cohesion: 0.22
Nodes (3): InstallGateway, SetProviderCredentials, Provider

### Community 36 - "ProductCatalog"
Cohesion: 0.18
Nodes (4): SetTenantProducts, TenantProductController, ProductController, ProductCatalog

### Community 38 - "DocumentType"
Cohesion: 0.10
Nodes (6): DocumentType, CertificateVaultEntry, self, SignatureDocument, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable

### Community 53 - "Illuminate\Http\JsonResponse"
Cohesion: 0.09
Nodes (18): ProductController, TenantController, CatalogController, CertificateController, LivenessCredentialController, PairingController, PingController, HealthController (+10 more)

### Community 54 - "TestCase"
Cohesion: 0.11
Nodes (6): Illuminate\Foundation\Testing\TestCase, DevToolsExposureTest, EnvironmentIsolationTest, GenerateGatewayKeysTest, StrictIdentificationConfigTest, TestCase

### Community 55 - "CertificatePeriod"
Cohesion: 0.16
Nodes (4): CertificatePeriod, InvalidStatusTransition, self, LogicException

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.12
Nodes (16): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 17. Endurecimiento 2026-11-02 — gateway cerrado y control de afiliados, 18. Instalación y emparejamiento 2026-11-02, 19. Monitor de salud 2026-11-03, 1. Decisiones base, 20. Red privada, certificado por emparejamiento y puesta en marcha 2026-11-05 (+8 more)

### Community 59 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 62 - "ProviderAccount"
Cohesion: 0.06
Nodes (15): ReconcileSignatureSessions, RecordHeartbeat, ProviderEventController, QueueHeartbeat, RecordProviderCall, Heartbeat, ProviderAccount, TlsTrust (+7 more)

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

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

### Community 86 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.11
Nodes (6): Permission, SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Concerns\HasUuids, Illuminate\Database\Eloquent\Relations\BelongsTo, Spatie\Permission\Models\Permission

### Community 91 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.16
Nodes (6): Illuminate\Foundation\Testing\RefreshDatabase, createFirmaSeguraAccount(), fakeFirmaSegura(), testP12Base64(), GatewayDoctorTest, PlatformCredentialTest

### Community 92 - "ProductType"
Cohesion: 0.10
Nodes (9): ApplicationType, ProductType, StoreSignatureSessionRequest, offering(), static, withAccount(), self, ProductOffering (+1 more)

### Community 93 - "PermissionName"
Cohesion: 0.35
Nodes (3): IssueApiClientCredentials, PermissionName, PlatformCredentialController

### Community 95 - "Illuminate\Http\Request"
Cohesion: 0.25
Nodes (6): IssuanceController, CityResource, IssuanceLedgerEntryResource, ProvinceResource, Illuminate\Http\Request, Illuminate\Http\Resources\Json\JsonResource

### Community 96 - "AuditLog"
Cohesion: 0.12
Nodes (6): CreateTenant, PurgeApplicantData, AuditLog, self, Illuminate\Database\Eloquent\Relations\MorphTo, Throwable

### Community 97 - "Cambios en la plataforma (signature-saas) para el gateway endurecido"
Cohesion: 0.11
Nodes (18): 0. Requisito de red (antes de todo), 1.1 Certificado de la API: viaja con el emparejamiento, 1. Ficha del proveedor: URL y código de emparejamiento, 2. Catálogo con la credencial de servicio, 3. Reenvío de los avisos del proveedor (nuevo), 4. Una sola URL de notificaciones (dominio central), 5. Alta de afiliados sin credenciales huérfanas, 6. Suspender y bloquear afiliados (+10 more)

### Community 98 - "require"
Cohesion: 0.22
Nodes (9): require, dedoc/scramble, laravel/framework, laravel/sanctum, laravel/tinker, php, spatie/laravel-enum, spatie/laravel-permission (+1 more)

### Community 99 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, brianium/paratest, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery (+2 more)

### Community 102 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 103 - "Signing Gateway — Instalación del servidor"
Cohesion: 0.12
Nodes (16): 0. Datos de la instalación, A1. Red privada en Proxmox (VNet), A2. IP privada en Plesk (VM 100), A3. AlmaLinux 9 en el gateway (VM 102), A. Prerrequisitos (una vez, a mano), Administración diaria: `gw`, B1. Llevar el script al servidor, B2. Ejecutarlo (+8 more)

### Community 104 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 106 - "ApiClient.php"
Cohesion: 0.20
Nodes (7): PersonalAccessToken, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Traits\HasRoles

### Community 108 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 112 - "ProductCatalog.php"
Cohesion: 0.13
Nodes (6): StoreLivenessResultRequest, StoreRegularCorrectionRequest, UpdateTenantProductsRequest, UpsertTenantRequest, Illuminate\Foundation\Http\FormRequest, Illuminate\Validation\Validator

### Community 116 - "InteractsWithSignedApi.php"
Cohesion: 0.24
Nodes (4): Illuminate\Testing\TestResponse, issueApiCredentials(), signedGet(), signedJson()

### Community 118 - "SessionReconciler"
Cohesion: 0.29
Nodes (3): SessionReconciler, trackSession(), SessionTracking

### Community 122 - ".store"
Cohesion: 0.20
Nodes (3): ClientController, StoreApiClientRequest, NotificationTarget

### Community 123 - "Signing Gateway"
Cohesion: 0.17
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 124 - "ClientNotification"
Cohesion: 0.25
Nodes (3): ClientNotification, self, ClientNotificationDeliveryTest

### Community 126 - "CreateSignatureSession"
Cohesion: 0.24
Nodes (3): CreateSignatureSession, BlindIndex, TermsCatalog

### Community 130 - "KeyRing"
Cohesion: 0.39
Nodes (3): KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 137 - "tls-rotate.sh"
Cohesion: 0.48
Nodes (5): expires_within_days(), generate(), next_acknowledged(), reload_nginx(), tls-rotate.sh script

### Community 143 - "LimitExceeded.php"
Cohesion: 0.60
Nodes (3): LimitExceeded, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 147 - "2. Configuración por cliente (admin del SaaS)"
Cohesion: 0.40
Nodes (5): 2. Configuración por cliente (admin del SaaS), Alta automática desde el backoffice del SaaS, Credencial de servicio y reenvío de avisos del proveedor, Instalación y emparejamiento, Registro de emisiones (para facturar y conciliar)

## Knowledge Gaps
- **167 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+162 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **55 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `SignatureRequest` connect `SignatureRequest` to `.http`, `EmissionGuard`, `IssuanceLedgerEntry`, `UsageGuardTest.php`, `Illuminate\Database\Eloquent\Model`, `Tenant`, `SignatureFlow`, `.createRequest`, `SignatureRequest.php`, `CancelSignatureSession`, `RequestStatus`, `CertificateData`, `.show`, `UsageGuard`, `IssuanceLedgerTest`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `SessionReconciliationTest`, `Illuminate\Console\Command`, `ApiClient`, `AuditLog.php`, `ProviderWebhookTest`, `DocumentType`, `Illuminate\Http\JsonResponse`, `CertificatePeriod`, `LivenessTestController.php`, `ProviderAccount`, `RegularSignatureFlowTest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `SignatureProviderManager`, `AuditLog`, `SignatureSessionFlowTest`, `InteractsWithSignedApi.php`, `SessionStep`, `SessionReconciler`, `ClientNotification`?**
  _High betweenness centrality (0.070) - this node is a cross-community bridge._
- **Why does `ProviderAccount` connect `ProviderAccount` to `.http`, `GatewayHealthTest`, `Illuminate\Database\Eloquent\Model`, `.createRequest`, `SignatureRequest.php`, `TenantSuspensionTest`, `IssuanceLedgerTest`, `DrivesApiFlow.php`, `TenantProductsTest`, `.makeSignatureRequest`, `Environment`, `SessionReconciliationTest`, `AuditLog.php`, `SignatureRequest`, `ProviderAccount.php`, `ProductCatalog`, `ProviderWebhookTest`, `CertificatePeriod`, `LivenessTestController.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `.handle`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ProductType`, `SignatureProviderManager`, `ProductCatalog.php`, `InteractsWithSignedApi.php`, `CreateSignatureSession`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `TestCase` connect `TestCase` to `AdminProvisioningTest`, `UsageGuardTest.php`, `GatewayHealthTest`, `NetworkHardeningTest`, `PlatformPairingTest`, `PlatformTlsTrustTest`, `Province`, `.createRequest`, `SignatureRequest.php`, `RequestStatus`, `TenantSuspensionTest`, `IssuanceLedgerTest`, `LogMaskingTest`, `TenantProductsTest`, `.makeSignatureRequest`, `Environment`, `SessionReconciliationTest`, `AuditLog.php`, `ProviderWebhookTest`, `CertificatePeriod`, `ProviderAccount`, `RegularSignatureFlowTest`, `PairPlatform`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SignatureSessionFlowTest`, `ApiClient.php`, `ApiClientAuthenticationTest`, `InteractsWithSignedApi.php`, `ClientNotification`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Are the 17 inferred relationships involving `SignatureRequest` (e.g. with `.__invoke()` and `.handle()`) actually correct?**
  _`SignatureRequest` has 17 INFERRED edges - model-reasoned connections that need verification._
- **Are the 36 inferred relationships involving `RequestStatus` (e.g. with `.canCancel()` and `.__invoke()`) actually correct?**
  _`RequestStatus` has 36 INFERRED edges - model-reasoned connections that need verification._
- **Are the 15 inferred relationships involving `Tenant` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Tenant` has 15 INFERRED edges - model-reasoned connections that need verification._
- **Are the 8 inferred relationships involving `ProviderAccount` (e.g. with `.checkKeys()` and `.checkProvider()`) actually correct?**
  _`ProviderAccount` has 8 INFERRED edges - model-reasoned connections that need verification._