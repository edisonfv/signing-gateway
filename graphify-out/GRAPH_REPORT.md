# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- Corpus is ~32,138 words - fits in a single context window. You may not need a graph.

## Summary
- 712 nodes · 1282 edges · 50 communities (19 shown, 21 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 22 edges (avg confidence: 0.86)
- Token cost: 108,668 input · 0 output

## Community Hubs (Navigation)
- Middleware de autenticación API
- Arquitectura (documentación)
- Modelos y bóveda cifrada
- Dependencias Composer
- Máquina de estados de solicitudes
- Productos y ruteo de proveedores
- Enums de solicitud
- Provincias y ciudades
- Roles y permisos
- Controladores API v1
- Proveedores de servicio y OpenAPI
- Migraciones base
- Outbox de notificaciones
- Scripts Composer
- Comandos Artisan del gateway
- Llaves e índice ciego
- Enmascarado de logs
- Script de firma Apidog
- Validación Firmas Regulares
- Modo de entrega
- Pasos Flujo por API
- Cancelación automática FR
- Eventos CloudEvents
- Evidencias Tercero Vinculado
- Tipos de certificado TV
- Estados Tercero Vinculado
- Estados Firmas Regulares
- Tipos de validación FR
- Tipo de solicitud
- Estados de revocación
- Errores de sesión
- Fallos de sesión
- Rechazos de sesión
- Etapas de sesión
- Estados de sesión
- Tipo de términos
- Estados Transaccional
- Validación Transaccional
- Tipos de validación FT
- Configuración Scramble

## God Nodes (most connected - your core abstractions)
1. `Tenant` - 37 edges
2. `ApiClient` - 26 edges
3. `AuditLog` - 26 edges
4. `ProviderAccount` - 22 edges
5. `SignatureRequest` - 22 edges
6. `ProductType` - 21 edges
7. `Province` - 18 edges
8. `RequestSignature` - 17 edges
9. `DeliverClientNotification` - 16 edges
10. `TestCase` - 16 edges

## Surprising Connections (you probably didn't know these)
- `Gateway notification signature (X-Gateway-Signature)` --semantically_similar_to--> `VerifyRequestSignature / VerifyClientSignature middleware (HMAC + timestamp + nonce)`  [INFERRED] [semantically similar]
  README.md → docs/ARQUITECTURA.md
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `InteractsWithSignedApi` --references--> `Tenant`  [EXTRACTED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `Signing Gateway (README)` --references--> `Signing Gateway Architecture`  [EXTRACTED]
  README.md → docs/ARQUITECTURA.md
- `gateway:* Artisan commands (keys:generate, provider:credentials, tenant:create, client:create)` --implements--> `ApiClient tenant-scoped auth (Sanctum token + HMAC, TenantScope)`  [INFERRED]
  README.md → docs/ARQUITECTURA.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Multi-provider driver abstraction** — docs_arquitectura_signatureprovidermanager, docs_arquitectura_capability_contracts, docs_arquitectura_providerrouter, docs_arquitectura_status_mapper, docs_arquitectura_firmaseguradriver, docs_arquitectura_provider_specific_enums [EXTRACTED 1.00]
- **One-time certificate custody and delivery flow** — docs_arquitectura_providerwebhookcontroller, docs_arquitectura_fetchcertificate_job, docs_arquitectura_certificatevault, docs_arquitectura_sealed_delivery, docs_arquitectura_client_notification_outbox [INFERRED 0.85]
- **Internal API auth middleware pipeline** — docs_arquitectura_ensureinternalnetwork, docs_arquitectura_verifyrequestsignature, docs_arquitectura_apiclient_tenant_auth, docs_arquitectura_usageguard, docs_arquitectura_permission_role_enums [EXTRACTED 1.00]

## Communities (50 total, 21 thin omitted)

### Community 0 - "Middleware de autenticación API"
Cohesion: 0.06
Nodes (22): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, AuditLog, self, PersonalAccessToken, RequestSignature, Closure (+14 more)

### Community 1 - "Arquitectura (documentación)"
Cohesion: 0.06
Nodes (52): Flujo por API / HEADLESS (F1, §16), ApiClient tenant-scoped auth (Sanctum token + HMAC, TenantScope), Async job chains (Tramo A/B/C via Bus::chain), Blind index (identification_hash = HMAC-SHA256 with BLIND_INDEX_KEY), Capability contracts (SignatureProvider, RegistersRequests, QueriesStatus, HandlesWebhooks...), CertificateVault (aes-256-gcm, VAULT_KEY), Client notification outbox (DeliverClientNotification), Data model (api_clients, tenants, provider_accounts, signature_requests, certificate_vault, webhook_events, usage_ledger, client_notifications, audit_logs) (+44 more)

### Community 2 - "Modelos y bóveda cifrada"
Cohesion: 0.10
Nodes (19): VaultEncrypted, ApiClient, City, SignatureRequestTransition, TermsAcceptance, User, Illuminate\Contracts\Database\Eloquent\CastsAttributes, Illuminate\Database\Eloquent\Attributes\Fillable (+11 more)

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 4 - "Máquina de estados de solicitudes"
Cohesion: 0.07
Nodes (13): self, RequestStatus, InvalidStatusTransition, self, CertificateVaultEntry, SignatureRequest, Illuminate\Contracts\Encryption\DecryptException, Illuminate\Database\Eloquent\Builder (+5 more)

### Community 5 - "Productos y ruteo de proveedores"
Cohesion: 0.09
Nodes (14): Environment, ProductType, NoProviderAvailableException, ProviderAccount, static, SignatureProvider, FirmaSeguraDriver, static (+6 more)

### Community 6 - "Enums de solicitud"
Cohesion: 0.09
Nodes (12): ApplicationType, CertificatePeriod, LimitExceeded, LimitExceededException, Tenant, UsageGuard, DateTimeInterface, Exception (+4 more)

### Community 7 - "Provincias y ciudades"
Cohesion: 0.07
Nodes (14): Province, CityFactory, ProvinceFactory, static, UserFactory, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Foundation\Testing\TestCase (+6 more)

### Community 8 - "Roles y permisos"
Cohesion: 0.08
Nodes (15): PermissionName, RoleName, Permission, Role, SealedBox, DatabaseSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents (+7 more)

### Community 9 - "Controladores API v1"
Cohesion: 0.11
Nodes (16): CatalogController, PingController, ProvinceCityController, ProvinceController, Controller, CityResource, ProvinceResource, Dedoc\Scramble\Attributes\Group (+8 more)

### Community 10 - "Proveedores de servicio y OpenAPI"
Cohesion: 0.08
Nodes (21): ApiDocsServiceProvider, AppServiceProvider, Dedoc\Scramble\Scramble, Dedoc\Scramble\Support\Generator\OpenApi, Dedoc\Scramble\Support\Generator\Operation, Dedoc\Scramble\Support\Generator\Parameter, Dedoc\Scramble\Support\Generator\Response, Dedoc\Scramble\Support\Generator\Schema (+13 more)

### Community 11 - "Migraciones base"
Cohesion: 0.10
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 12 - "Outbox de notificaciones"
Cohesion: 0.13
Nodes (12): DeliverClientNotification, ClientNotification, self, Illuminate\Contracts\Queue\ShouldBeEncrypted, Illuminate\Contracts\Queue\ShouldBeUnique, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Foundation\Queue\Queueable, Illuminate\Foundation\Testing\RefreshDatabase (+4 more)

### Community 13 - "Scripts Composer"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 14 - "Comandos Artisan del gateway"
Cohesion: 0.16
Nodes (9): IssueApiClientCredentials, CreateApiClient, CreateTenant, GenerateGatewayKeys, SetProviderCredentials, Provider, Illuminate\Console\Attributes\Description, Illuminate\Console\Attributes\Signature (+1 more)

### Community 15 - "Llaves e índice ciego"
Cohesion: 0.16
Nodes (7): BlindIndex, KeyRing, Encrypter, Illuminate\Encryption\Encrypter, Illuminate\Support\Str, Pdo\Mysql, RuntimeException

### Community 16 - "Enmascarado de logs"
Cohesion: 0.23
Nodes (7): MaskSensitiveData, Illuminate\Log\Logger, Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\LogRecord, Monolog\Processor\PsrLogMessageProcessor

### Community 17 - "Script de firma Apidog"
Cohesion: 0.20
Nodes (9): canonical, CryptoJS, nonce, secret, separator, signature, timestamp, token (+1 more)

### Community 18 - "Validación Firmas Regulares"
Cohesion: 0.28
Nodes (3): RegularValidationErrorCode, RegularValidationStatus, Spatie\Enum\Laravel\Enum

## Ambiguous Edges - Review These
- `Evidence package (EvidenceLog, EvidencePackageBuilder, EvidenceEncryptor, CMS)` → `VerifyRequestSignature / VerifyClientSignature middleware (HMAC + timestamp + nonce)`  [AMBIGUOUS]
  docs/ARQUITECTURA.md · relation: semantically_similar_to

## Knowledge Gaps
- **65 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+60 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 250 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **21 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Evidence package (EvidenceLog, EvidencePackageBuilder, EvidenceEncryptor, CMS)` and `VerifyRequestSignature / VerifyClientSignature middleware (HMAC + timestamp + nonce)`?**
  _Edge tagged AMBIGUOUS (relation: semantically_similar_to) - confidence is low._
- **Why does `Tenant` connect `Enums de solicitud` to `Middleware de autenticación API`, `Modelos y bóveda cifrada`, `Máquina de estados de solicitudes`, `Roles y permisos`, `Outbox de notificaciones`, `Comandos Artisan del gateway`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `ApiClient` connect `Modelos y bóveda cifrada` to `Middleware de autenticación API`, `Máquina de estados de solicitudes`, `Enums de solicitud`, `Roles y permisos`, `Controladores API v1`, `Proveedores de servicio y OpenAPI`, `Outbox de notificaciones`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `ProductType` connect `Productos y ruteo de proveedores` to `Modelos y bóveda cifrada`, `Máquina de estados de solicitudes`, `Enums de solicitud`, `Validación Firmas Regulares`, `Modo de entrega`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _65 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Middleware de autenticación API` be split into smaller, more focused modules?**
  _Cohesion score 0.05844155844155844 - nodes in this community are weakly interconnected._
- **Should `Arquitectura (documentación)` be split into smaller, more focused modules?**
  _Cohesion score 0.058069381598793365 - nodes in this community are weakly interconnected._