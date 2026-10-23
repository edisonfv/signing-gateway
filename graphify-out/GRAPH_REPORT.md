# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- 166 files · ~39,125 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 957 nodes · 1973 edges · 75 communities (35 shown, 29 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 65 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `82314753`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- RequestSignature
- App\Models\SignatureRequest
- Illuminate\Database\Eloquent\Concerns\HasUuids
- Dependencias Composer
- ProductType
- Tenant
- SignatureRequest
- Province
- .createRequest
- LimitExceededException
- ApiDocsServiceProvider.php
- Migraciones base
- App\Jobs\IssueCertificate
- Scripts Composer
- AuditLog
- UserFactory.php
- Enmascarado de logs
- Script de firma Apidog
- CreateSignatureSession.php
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
- App\Models\Tenant
- Fallos de sesión
- ClientNotification
- Etapas de sesión
- SessionStatus
- Tipo de términos
- Estados Transaccional
- Validación Transaccional
- Tipos de validación FT
- Configuración Scramble
- SessionRejectionCode
- Illuminate\Foundation\Testing\RefreshDatabase
- RequestStatus
- ApiClient
- SignatureSessionFlowTest
- Illuminate\Database\Eloquent\Model
- Signing Gateway — Arquitectura
- ProviderAccount
- KeyRing
- App\Actions\IssueApiClientCredentials
- 13. F1 — Flujo Transaccional (FirmaSegura) en detalle
- 15. F1 — Tercero Vinculado (FirmaSegura) en detalle
- 16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación
- RegularValidationErrorCode
- 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)
- self
- DataProtectionTest.php
- static
- 7. Flujos clave
- liveness.jsx
- EvidenceType
- SessionFailureCode
- 3. Abstracción multi‑proveedor
- SignatureProvider

## God Nodes (most connected - your core abstractions)
1. `Tenant` - 36 edges
2. `AuditLog` - 31 edges
3. `ApiClient` - 25 edges
4. `SignatureRequest` - 24 edges
5. `FirmaSeguraDriver` - 22 edges
6. `ProductType` - 21 edges
7. `SignatureSessionFlowTest` - 19 edges
8. `RequestStatus` - 18 edges
9. `IssueCertificate` - 18 edges
10. `Province` - 18 edges

## Surprising Connections (you probably didn't know these)
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `LocationCatalogTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/LocationCatalogTest.php → tests/Concerns/InteractsWithSignedApi.php
- `RequestLivenessSession` --mixes_in--> `HandlesProviderSession`  [EXTRACTED]
  app/Jobs/RequestLivenessSession.php → app/Jobs/Concerns/HandlesProviderSession.php
- `ClientNotificationDeliveryTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → tests/TestCase.php
- `ProviderRoutingTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ProviderRoutingTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (75 total, 29 thin omitted)

### Community 0 - "RequestSignature"
Cohesion: 0.10
Nodes (7): App\Security\RequestSignature, RequestSignature, Illuminate\Testing\TestResponse, Tenant, Tests\Concerns\InteractsWithSignedApi, InteractsWithSignedApi, ApiClientAuthenticationTest

### Community 1 - "App\Models\SignatureRequest"
Cohesion: 0.06
Nodes (43): self, RequestStatus, App\Http\Controllers\Api\V1\CatalogController, CatalogController, CertificateController, LivenessCredentialController, LivenessResultController, LivenessSessionController (+35 more)

### Community 2 - "Illuminate\Database\Eloquent\Concerns\HasUuids"
Cohesion: 0.18
Nodes (10): App\Models\AuditLog, App\Models\ClientNotification, App\Models\PersonalAccessToken, PersonalAccessToken, SignatureRequestTransition, TermsAcceptance, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Attributes\Hidden (+2 more)

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 5 - "Tenant"
Cohesion: 0.20
Nodes (6): LimitExceeded, Tenant, UsageGuard, DateTimeInterface, Illuminate\Foundation\Events\Dispatchable, Illuminate\Queue\SerializesModels

### Community 6 - "SignatureRequest"
Cohesion: 0.19
Nodes (3): SignatureRequest, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Database\Eloquent\Relations\HasOne

### Community 7 - "Province"
Cohesion: 0.13
Nodes (9): App\Models\City, City, Province, CityFactory, ProvinceFactory, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory (+1 more)

### Community 10 - "ApiDocsServiceProvider.php"
Cohesion: 0.13
Nodes (14): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Dedoc\Scramble\Support\Generator\Schema (+6 more)

### Community 11 - "Migraciones base"
Cohesion: 0.10
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "App\Jobs\IssueCertificate"
Cohesion: 0.06
Nodes (39): App\Enums\RequestStatus, App\Enums\SessionStep, SessionStep, ProviderRequestException, App\Jobs\Concerns\HandlesProviderSession, HandlesProviderSession, RequestStatus, App\Jobs\CreateProviderSession (+31 more)

### Community 13 - "Scripts Composer"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "AuditLog"
Cohesion: 0.07
Nodes (21): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, AuditLog, self, EcuadorianIdentification, EcuadorianRuc, Closure (+13 more)

### Community 15 - "UserFactory.php"
Cohesion: 0.40
Nodes (3): static, UserFactory, Illuminate\Support\Facades\Hash

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "CreateSignatureSession.php"
Cohesion: 0.09
Nodes (18): CreateSignatureSession, App\Enums\ApplicationType, App\Enums\CertificatePeriod, Environment, App\Enums\ProductType, NoProviderAvailableException, TermsController, StoreLivenessResultRequest (+10 more)

### Community 20 - ".http"
Cohesion: 0.05
Nodes (22): InvalidStatusTransition, self, App\Models\ProviderAccount, DrivesApiFlow, App\Signing\Contracts\SignatureProvider, CertificateData, CivilRegistryResult, LivenessCredentials (+14 more)

### Community 21 - "Spatie\Enum\Laravel\Enum"
Cohesion: 0.28
Nodes (3): AutoCancellationReason, SessionErrorCode, Spatie\Enum\Laravel\Enum

### Community 25 - "Signing Gateway"
Cohesion: 0.18
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

### Community 32 - "ClientNotification"
Cohesion: 0.16
Nodes (4): DeliverClientNotification, ClientNotification, self, ClientNotificationDeliveryTest

### Community 51 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.18
Nodes (8): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\Client\Request, Illuminate\Http\Client\RequestException, Illuminate\Support\Facades\Queue, Illuminate\Support\Str, Pdo\Mysql, TestCase

### Community 54 - "ApiClient"
Cohesion: 0.16
Nodes (8): App\Models\ApiClient, ApiClient, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens, Spatie\Permission\Traits\HasRoles, Symfony\Component\HttpFoundation\IpUtils

### Community 55 - "SignatureSessionFlowTest"
Cohesion: 0.07
Nodes (12): Database\Seeders\ProvinceSeeder, Illuminate\Support\Facades\Bus, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Log, Tests\Concerns\FakesFirmaSegura, FakesFirmaSegura, ProviderAccount (+4 more)

### Community 56 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (8): VaultEncrypted, App\Models\CertificateVaultEntry, CertificateVaultEntry, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Prunable, Illuminate\Support\Facades\Schedule

### Community 58 - "Signing Gateway — Arquitectura"
Cohesion: 0.17
Nodes (12): 10. Escalabilidad, 11. Plan por fases, 12. Pendientes, 1. Decisiones base, 2. Topología, 4. Estructura del proyecto, 5. Modelo de datos, 6. Enums (+4 more)

### Community 62 - "KeyRing"
Cohesion: 0.10
Nodes (14): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, SetProviderCredentials, Provider, BlindIndex, App\Security\KeyRing (+6 more)

### Community 63 - "App\Actions\IssueApiClientCredentials"
Cohesion: 0.08
Nodes (18): App\Actions\IssueApiClientCredentials, App\Enums\PermissionName, PermissionName, RoleName, Permission, Role, App\Security\SealedBox, SealedBox (+10 more)

### Community 64 - "13. F1 — Flujo Transaccional (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 13.1 Endpoints del proveedor, 13.2 Datos de entrada (FormRequest `StoreTransactionalRequest`), 13.3 Mapeo de estados, 13.4 Secuencia, 13.5 Respaldo si no llega el webhook `GENERATED`, 13.6 Tareas F0 (base), 13.7 Tareas F1 (transaccional), 13.8 Preguntas específicas de F1 para FirmaSegura (+1 more)

### Community 65 - "15. F1 — Tercero Vinculado (FirmaSegura) en detalle"
Cohesion: 0.22
Nodes (9): 15.1 Qué cambia con este flujo, 15.2 Endpoints, 15.3 Tipos de certificado, 15.4 Mapeo de estados, 15.5 Paquete de evidencias, 15.6 Verificación de identidad (lo que el aliado debe hacer), 15.7 Bloqueantes a resolver con FirmaSegura, 15.8 Tareas F1 (Tercero Vinculado) (+1 more)

### Community 66 - "16. Rediseño 2026-10-22 — F1 Flujo por API, colas, cifrado, UUID y autenticación"
Cohesion: 0.22
Nodes (9): 16.1 El Flujo por API de FirmaSegura, 16.2 Diseño asíncrono (colas), 16.3 Catálogos como enums (spatie/laravel-enum), 16.4 UUID en todos los modelos, 16.5 Protección de firmas, contraseñas y datos personales, 16.6 Autenticación SaaS → gateway (token configurado en el admin del SaaS), 16.7 API interna para el Flujo por API, 16.8 Preguntas para FirmaSegura (Flujo por API) (+1 more)

### Community 69 - "14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)"
Cohesion: 0.29
Nodes (7): 14.1 Lo que pide FirmaSegura, 14.2 Checklist → dónde se cumple, 14.3 Reglas condicionales (flujo Regular, `POST /collector/request`), 14.4 Presupuesto de consultas de estado, 14.5 Datos de prueba en el proyecto, 14.6 Impacto en las fases, 14. Certificación FirmaSegura (correo de inicio de integración, 2026-10-18)

### Community 71 - "DataProtectionTest.php"
Cohesion: 0.22
Nodes (4): ApplicationType, CertificatePeriod, Illuminate\Contracts\Encryption\DecryptException, Illuminate\Support\Facades\Event

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
- **117 isolated node(s):** `Composer\\Config::disableProcessTimeout`, `composer install`, `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump`, `Illuminate\\Foundation\\ComposerScripts::prePackageUninstall`, `npm install --ignore-scripts` (+112 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 358 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **29 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Tenant` connect `Tenant` to `ClientNotification`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `DataProtectionTest.php`, `.createRequest`, `App\Jobs\IssueCertificate`, `CreateSignatureSession.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SignatureSessionFlowTest`, `Illuminate\Database\Eloquent\Model`, `KeyRing`, `ProviderAccount`, `App\Models\Tenant`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.035) - this node is a cross-community bridge._
- **Why does `AuditLog` connect `AuditLog` to `RequestSignature`, `App\Models\SignatureRequest`, `Illuminate\Database\Eloquent\Concerns\HasUuids`, `Tenant`, `DataProtectionTest.php`, `App\Jobs\IssueCertificate`, `CreateSignatureSession.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Database\Eloquent\Model`, `KeyRing`, `App\Actions\IssueApiClientCredentials`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Why does `ProductType` connect `ProductType` to `App\Models\SignatureRequest`, `DataProtectionTest.php`, `.createRequest`, `App\Jobs\IssueCertificate`, `CreateSignatureSession.php`, `Modo de entrega`, `.http`, `Spatie\Enum\Laravel\Enum`, `SignatureProvider`, `Illuminate\Foundation\Testing\RefreshDatabase`, `ProviderAccount`?**
  _High betweenness centrality (0.031) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `Tenant` (e.g. with `.makeRequest()` and `.test_requests_of_another_tenant_are_not_visible()`) actually correct?**
  _`Tenant` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 5 inferred relationships involving `AuditLog` (e.g. with `.show()` and `.show()`) actually correct?**
  _`AuditLog` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Composer\\Config::disableProcessTimeout`, `composer install`, `Illuminate\\Foundation\\ComposerScripts::postAutoloadDump` to the rest of the system?**
  _117 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `RequestSignature` be split into smaller, more focused modules?**
  _Cohesion score 0.09666666666666666 - nodes in this community are weakly interconnected._