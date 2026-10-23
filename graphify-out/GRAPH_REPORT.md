# Graph Report - signing-gateway  (2026-10-22)

## Corpus Check
- 129 files · ~32,227 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 716 nodes · 1279 edges · 50 communities (20 shown, 20 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 17 edges (avg confidence: 0.86)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5ecbbddc`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

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
2. `AuditLog` - 26 edges
3. `ApiClient` - 26 edges
4. `SignatureRequest` - 22 edges
5. `ProviderAccount` - 22 edges
6. `ProductType` - 21 edges
7. `Province` - 18 edges
8. `RequestSignature` - 17 edges
9. `DeliverClientNotification` - 16 edges
10. `TestCase` - 16 edges

## Surprising Connections (you probably didn't know these)
- `InteractsWithSignedApi` --references--> `Tenant`  [EXTRACTED]
  tests/Concerns/InteractsWithSignedApi.php → app/Models/Tenant.php
- `ClientNotificationDeliveryTest` --references--> `ApiClient`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → app/Models/ApiClient.php
- `LocationCatalogTest` --mixes_in--> `InteractsWithSignedApi`  [EXTRACTED]
  tests/Feature/LocationCatalogTest.php → tests/Concerns/InteractsWithSignedApi.php
- `ApiClientAuthenticationTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ApiClientAuthenticationTest.php → tests/TestCase.php
- `ClientNotificationDeliveryTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ClientNotificationDeliveryTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Internal API auth middleware pipeline** — docs_arquitectura_ensureinternalnetwork, docs_arquitectura_verifyrequestsignature, docs_arquitectura_apiclient_tenant_auth, docs_arquitectura_usageguard, docs_arquitectura_permission_role_enums [EXTRACTED 1.00]
- **Multi-provider driver abstraction** — docs_arquitectura_signatureprovidermanager, docs_arquitectura_capability_contracts, docs_arquitectura_providerrouter, docs_arquitectura_status_mapper, docs_arquitectura_firmaseguradriver, docs_arquitectura_provider_specific_enums [EXTRACTED 1.00]
- **One-time certificate custody and delivery flow** — docs_arquitectura_providerwebhookcontroller, docs_arquitectura_fetchcertificate_job, docs_arquitectura_certificatevault, docs_arquitectura_sealed_delivery, docs_arquitectura_client_notification_outbox [INFERRED 0.85]

## Communities (50 total, 20 thin omitted)

### Community 0 - "Middleware de autenticación API"
Cohesion: 0.06
Nodes (21): AssignRequestContext, EnsureActiveApiClient, VerifyRequestSignature, AuditLog, self, PersonalAccessToken, RequestSignature, Closure (+13 more)

### Community 1 - "Arquitectura (documentación)"
Cohesion: 0.06
Nodes (46): Flujo por API / HEADLESS (F1, §16), ApiClient tenant-scoped auth (Sanctum token + HMAC, TenantScope), Async job chains (Tramo A/B/C via Bus::chain), Blind index (identification_hash = HMAC-SHA256 with BLIND_INDEX_KEY), Capability contracts (SignatureProvider, RegistersRequests, QueriesStatus, HandlesWebhooks...), CertificateVault (aes-256-gcm, VAULT_KEY), Client notification outbox (DeliverClientNotification), Data model (api_clients, tenants, provider_accounts, signature_requests, certificate_vault, webhook_events, usage_ledger, client_notifications, audit_logs) (+38 more)

### Community 2 - "Modelos y bóveda cifrada"
Cohesion: 0.06
Nodes (24): VaultEncrypted, ApiClient, CertificateVaultEntry, City, Permission, SignatureRequestTransition, TermsAcceptance, User (+16 more)

### Community 3 - "Dependencias Composer"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 4 - "Máquina de estados de solicitudes"
Cohesion: 0.29
Nodes (3): Environment, ProductType, NoProviderAvailableException

### Community 5 - "Productos y ruteo de proveedores"
Cohesion: 0.07
Nodes (16): ProviderAccount, static, SignatureProvider, FirmaSeguraDriver, static, ProviderRouter, SignatureProviderManager, Illuminate\Foundation\Testing\TestCase (+8 more)

### Community 6 - "Enums de solicitud"
Cohesion: 0.05
Nodes (24): ApplicationType, CertificatePeriod, self, RequestStatus, LimitExceeded, InvalidStatusTransition, self, LimitExceededException (+16 more)

### Community 7 - "Provincias y ciudades"
Cohesion: 0.09
Nodes (10): Province, CityFactory, ProvinceFactory, static, UserFactory, ProvinceSeeder, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Relations\HasMany (+2 more)

### Community 8 - "Roles y permisos"
Cohesion: 0.09
Nodes (12): PermissionName, RoleName, Role, SealedBox, DatabaseSeeder, RolesAndPermissionsSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder (+4 more)

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
Cohesion: 0.14
Nodes (11): DeliverClientNotification, ClientNotification, self, Illuminate\Contracts\Queue\ShouldBeEncrypted, Illuminate\Contracts\Queue\ShouldBeUnique, Illuminate\Contracts\Queue\ShouldQueue, Illuminate\Foundation\Queue\Queueable, Illuminate\Http\Client\RequestException (+3 more)

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

### Community 23 - "Evidencias Tercero Vinculado"
Cohesion: 0.19
Nodes (4): EvidenceType, LinkedPartnerStatus, SessionFailureCode, Spatie\Enum\Laravel\Enum

### Community 25 - "Estados Tercero Vinculado"
Cohesion: 0.20
Nodes (9): Catálogos, Clientes del SaaS, Documentación y pruebas en Apidog, Grafo de conocimiento (graphify), Producción, Pruebas, Puesta en marcha (desarrollo), Signing Gateway (+1 more)

## Ambiguous Edges - Review These
- `Evidence package (EvidenceLog, EvidencePackageBuilder, EvidenceEncryptor, CMS)` → `VerifyRequestSignature / VerifyClientSignature middleware (HMAC + timestamp + nonce)`  [AMBIGUOUS]
  docs/ARQUITECTURA.md · relation: semantically_similar_to

## Knowledge Gaps
- **75 isolated node(s):** `Stack`, `Puesta en marcha (desarrollo)`, `Clientes del SaaS`, `Catálogos`, `Documentación y pruebas en Apidog` (+70 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 261 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **20 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Evidence package (EvidenceLog, EvidencePackageBuilder, EvidenceEncryptor, CMS)` and `VerifyRequestSignature / VerifyClientSignature middleware (HMAC + timestamp + nonce)`?**
  _Edge tagged AMBIGUOUS (relation: semantically_similar_to) - confidence is low._
- **Why does `Tenant` connect `Enums de solicitud` to `Middleware de autenticación API`, `Modelos y bóveda cifrada`, `Provincias y ciudades`, `Roles y permisos`, `Outbox de notificaciones`, `Comandos Artisan del gateway`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `ApiClient` connect `Modelos y bóveda cifrada` to `Middleware de autenticación API`, `Enums de solicitud`, `Roles y permisos`, `Controladores API v1`, `Proveedores de servicio y OpenAPI`, `Outbox de notificaciones`?**
  _High betweenness centrality (0.052) - this node is a cross-community bridge._
- **Why does `ProductType` connect `Máquina de estados de solicitudes` to `Modo de entrega`, `Productos y ruteo de proveedores`, `Enums de solicitud`, `Evidencias Tercero Vinculado`?**
  _High betweenness centrality (0.051) - this node is a cross-community bridge._
- **What connects `Stack`, `Puesta en marcha (desarrollo)`, `Clientes del SaaS` to the rest of the system?**
  _75 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Middleware de autenticación API` be split into smaller, more focused modules?**
  _Cohesion score 0.05858585858585859 - nodes in this community are weakly interconnected._
- **Should `Arquitectura (documentación)` be split into smaller, more focused modules?**
  _Cohesion score 0.06280193236714976 - nodes in this community are weakly interconnected._