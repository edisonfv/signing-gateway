# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- 166 files · ~38,930 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 952 nodes · 1957 edges · 84 communities (43 shown, 30 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 57 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `97ff91c9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- RequestSignature
- App\Models\SignatureRequest
- Illuminate\Database\Eloquent\Model
- Dependencias Composer
- ProductType
- LimitExceeded
- SignatureRequest
- Province
- Tenant
- LimitExceededException
- ApiDocsServiceProvider.php
- Migraciones base
- App\Jobs\IssueCertificate
- Scripts Composer
- AuditLog
- UserFactory.php
- Enmascarado de logs
- Script de firma Apidog
- EcuadorianIdentification
- Modo de entrega
- .http
- Spatie\Enum\Laravel\Enum
- Eventos CloudEvents
- LinkedPartnerStatus
- Tipos de certificado TV
- Signing Gateway
- Estados Firmas Regulares
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- Errores de sesión
- Fallos de sesión
- ClientNotification
- Etapas de sesión
- Estados de sesión
- Tipo de términos
- Estados Transaccional
- Validación Transaccional
- Tipos de validación FT
- Configuración Scramble
- DrivesApiFlow
- DataProtectionTest.php
- SignatureSessionFlowTest.php
- SignatureProviderManager
- ApiClient
- SignatureSessionFlowTest
- CertificateVaultEntry
- App\Models\ProviderAccount
- Signing Gateway — Arquitectura
- ProviderAccount
- ApiClientAuthenticationTest
- sanctum.php
- KeyRing
- App\Actions\IssueApiClientCredentials
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- CreateSignatureSession.php
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- FirmaSeguraDriver.php
- ApplicationType
- static
- Illuminate\Database\Eloquent\Concerns\HasUuids
- TermsData
- 7. Flujos clave
- liveness.jsx
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- VaultEncrypted
- CertificateData
- SignatureProvider

## God Nodes (most connected - your core abstractions)
1. `Tenant` - 35 edges
2. `AuditLog` - 31 edges
3. `ApiClient` - 25 edges
4. `SignatureRequest` - 24 edges
5. `FirmaSeguraDriver` - 22 edges
6. `ProductType` - 20 edges
7. `IssueCertificate` - 19 edges
8. `SignatureSessionFlowTest` - 19 edges
9. `RequestStatus` - 18 edges
10. `Province` - 18 edges

## Surprising Connections (you probably didn't know these)
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `ApiClientAuthenticationTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/ApiClientAuthenticationTest.php → tests/Concerns/InteractsWithSignedApi.php
- `LocationCatalogTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/LocationCatalogTest.php → tests/Concerns/InteractsWithSignedApi.php
- `ClientNotificationDeliveryTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → tests/TestCase.php
- `RequestLivenessSession` --mixes_in--> `HandlesProviderSession`  [EXTRACTED]
  app/Jobs/RequestLivenessSession.php → app/Jobs/Concerns/HandlesProviderSession.php

## Import Cycles
- None detected.

## Communities (84 total, 30 thin omitted)

### Community 0 - "RequestSignature"
Cohesion: 0.17
Nodes (7): App\Models\Tenant, App\Security\RequestSignature, RequestSignature, Illuminate\Testing\TestResponse, Tenant, Tests\Concerns\InteractsWithSignedApi, InteractsWithSignedApi

### Community 1 - "App\Models\SignatureRequest"
Cohesion: 0.07
Nodes (42): self, RequestStatus, App\Http\Controllers\Api\V1\CatalogController, CatalogController, CertificateController, LivenessCredentialController, LivenessResultController, LivenessSessionController (+34 more)

### Community 2 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.22
Nodes (7): App\Models\ClientNotification, SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 4 - "ProductType"
Cohesion: 0.21
Nodes (4): Environment, ProductType, NoProviderAvailableException, ProviderRoutingTest

### Community 5 - "LimitExceeded"
Cohesion: 0.60
Nodes (3): LimitExceeded, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 6 - "SignatureRequest"
Cohesion: 0.14
Nodes (5): InvalidStatusTransition, self, SignatureRequest, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.14
Nodes (8): App\Models\City, City, Province, CityFactory, ProvinceFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, LocationCatalogTest

### Community 9 - "LimitExceededException"
Cohesion: 0.24
Nodes (4): LimitExceededException, UsageGuard, DateTimeInterface, Exception

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.13
Nodes (14): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Dedoc\Scramble\Support\Generator\Schema (+6 more)

### Community 11 - "Migraciones base"
Cohesion: 0.10
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "App\Jobs\IssueCertificate"
Cohesion: 0.06
Nodes (37): App\Enums\ProductType, App\Enums\RequestStatus, App\Enums\SessionStep, SessionStep, ProviderRequestException, HandlesProviderSession, RequestStatus, App\Jobs\CreateProviderSession (+29 more)

### Community 13 - "Scripts Composer"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "AuditLog"
Cohesion: 0.07
Nodes (24): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, SetProviderCredentials, Provider, AssignRequestContext, EnsureActiveApiClient (+16 more)

### Community 15 - "UserFactory.php"
Cohesion: 0.40
Nodes (3): static, UserFactory, Illuminate\Support\Facades\Hash

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "EcuadorianIdentification"
Cohesion: 0.10
Nodes (13): App\Enums\ApplicationType, App\Enums\CertificatePeriod, StoreLivenessResultRequest, StoreSignatureSessionRequest, EcuadorianIdentification, EcuadorianRuc, Illuminate\Contracts\Validation\ValidationRule, Illuminate\Foundation\Http\FormRequest (+5 more)

### Community 20 - ".http"
Cohesion: 0.26
Nodes (4): FirmaSeguraDriver, Illuminate\Http\Client\PendingRequest, LogicException, ProductType

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.28
Nodes (3): AutoCancellationReason, SessionRejectionCode, Spatie\Enum\Laravel\Enum

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 32 - "ClientNotification"
Cohesion: 0.16
Nodes (4): DeliverClientNotification, ClientNotification, self, ClientNotificationDeliveryTest

### Community 50 - "DrivesApiFlow"
Cohesion: 0.15
Nodes (4): DrivesApiFlow, CivilRegistryResult, LivenessResult, SessionData

### Community 51 - "DataProtectionTest.php"
Cohesion: 0.20
Nodes (7): Illuminate\Contracts\Encryption\DecryptException, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Support\Facades\Event, Illuminate\Support\Str, Pdo\Mysql, TestCase

### Community 52 - "SignatureSessionFlowTest.php"
Cohesion: 0.21
Nodes (8): Illuminate\Http\Client\Request, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\Bus, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Queue, Tests\Concerns\FakesFirmaSegura, DevToolsExposureTest, Tests\TestCase

### Community 53 - "SignatureProviderManager"
Cohesion: 0.23
Nodes (4): App\Signing\Contracts\SignatureProvider, ProviderRouter, SignatureProviderManager, Illuminate\Support\Manager

### Community 54 - "ApiClient"
Cohesion: 0.16
Nodes (10): App\Models\ApiClient, ApiClient, User, SignatureRequestPolicy, Illuminate\Auth\Access\Response, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens (+2 more)

### Community 56 - "CertificateVaultEntry"
Cohesion: 0.21
Nodes (5): App\Models\CertificateVaultEntry, CertificateVaultEntry, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Prunable, Illuminate\Support\Facades\Schedule

### Community 57 - "App\Models\ProviderAccount"
Cohesion: 0.22
Nodes (4): App\Models\ProviderAccount, static, FakesFirmaSegura, ProviderAccount

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 61 - "sanctum.php"
Cohesion: 0.40
Nodes (4): Illuminate\Cookie\Middleware\EncryptCookies, Illuminate\Foundation\Http\Middleware\ValidateCsrfToken, Laravel\Sanctum\Http\Middleware\AuthenticateSession, Laravel\Sanctum\Sanctum

### Community 62 - "KeyRing"
Cohesion: 0.27
Nodes (5): BlindIndex, App\Security\KeyRing, KeyRing, Encrypter, Illuminate\Encryption\Encrypter

### Community 63 - "App\Actions\IssueApiClientCredentials"
Cohesion: 0.06
Nodes (21): App\Actions\IssueApiClientCredentials, App\Enums\PermissionName, PermissionName, RoleName, Role, App\Security\SealedBox, SealedBox, DatabaseSeeder (+13 more)

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.22
Nodes (9): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7 API interna para el Flujo por API, 16.8 Preguntas para FirmaSegura (Flujo por API) (+1 more)

### Community 68 - "CreateSignatureSession.php"
Cohesion: 0.40
Nodes (6): CreateSignatureSession, App\Security\BlindIndex, TermsCatalog, App\Signing\Limits\UsageGuard, App\Signing\Routing\ProviderRouter, Illuminate\Validation\ValidationException

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 70 - "FirmaSeguraDriver.php"
Cohesion: 0.27
Nodes (4): self, LivenessCredentials, Carbon\CarbonImmutable, Illuminate\Http\Client\Response

### Community 73 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.32
Nodes (6): Permission, App\Models\PersonalAccessToken, PersonalAccessToken, Illuminate\Database\Eloquent\Concerns\HasUuids, Laravel\Sanctum\PersonalAccessToken, Spatie\Permission\Models\Permission

### Community 75 - "7. Flujos clave"
Cohesion: 0.40
Nodes (5): 7.1 Alta de solicitud (SaaS → gateway → proveedor), 7.2 Webhook con certificado (crítico: entrega de una sola vez), 7.3 Entrega del certificado, 7.4 Conciliación (scheduler), 7. Flujos clave

### Community 76 - "liveness.jsx"
Cohesion: 0.38
Nodes (6): displayText, LivenessTest(), complete(), start(), postJson(), root

### Community 79 - "3. Abstracción multi‑proveedor"
Cohesion: 0.50
Nodes (4): 3.1 Productos internos (independientes del proveedor), 3.2 Contratos por capacidad, 3.3 Resolución de drivers, 3. Abstracción multi‑proveedor

## Knowledge Gaps
- **117 isolated node(s):** `displayText`, `root`, `Composer\\Config::disableProcessTimeout`, `composer install`, `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump` (+112 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 356 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **30 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Tenant` connect `Tenant` to `RequestSignature`, `ClientNotification`, `Illuminate\Database\Eloquent\Model`, `CreateSignatureSession.php`, `LimitExceeded`, `SignatureRequest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `LimitExceededException`, `AuditLog`, `DataProtectionTest.php`, `SignatureSessionFlowTest.php`, `SignatureSessionFlowTest`, `ProviderAccount`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `App\Models\SignatureRequest`, `Illuminate\Database\Eloquent\Model`, `CreateSignatureSession.php`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `LimitExceededException`, `App\Jobs\IssueCertificate`, `DataProtectionTest.php`, `ProviderAccount`, `ApiClientAuthenticationTest`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `ProductType` connect `ProductType` to `App\Models\SignatureRequest`, `CreateSignatureSession.php`, `Tenant`, `App\Jobs\IssueCertificate`, `SignatureProvider`, `Modo de entrega`, `DataProtectionTest.php`, `SignatureProviderManager`, `Spatie\Enum\Laravel\Enum`, `ProviderAccount`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `AuditLog` (e.g. with `.show()` and `.show()`) actually correct?**
  _`AuditLog` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `displayText`, `root`, `Composer\\Config::disableProcessTimeout` to the rest of the system?**
  _117 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `App\Models\SignatureRequest` be split into smaller, more focused modules?**
  _Cohesion score 0.06664388243335612 - nodes in this community are weakly interconnected._
- **Should `Dependencias Composer` be split into smaller, more focused modules?**
  _Cohesion score 0.0425531914893617 - nodes in this community are weakly interconnected._