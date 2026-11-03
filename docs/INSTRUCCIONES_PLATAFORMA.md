# Cambios en la plataforma (signature-saas) para el gateway endurecido

Tareas para `signature-saas` tras el endurecimiento del gateway (2026-11-02). Referencia completa de la
API: `docs/INTEGRACION_SAAS.md`. Los nombres de clases son los actuales de `Modules/Gateway` y
`Modules/Catalog`.

**Objetivo:** el gateway solo habla con la plataforma (por el túnel) y con FirmaSegura. Nada de él queda
en internet. El operador configura una sola credencial; todo lo demás lo hace la plataforma.

---

## 1. Ficha del proveedor: URL y código de emparejamiento

**Hoy:** la pantalla pide la credencial de catálogo y la de alta, y el operador copia tokens y secretos.
**Cambio:** pedir solo **URL del microservicio** y **código de emparejamiento** (p. ej. `K7PX-92QM`, lo
genera el operador con `php artisan gateway:pair`), con un botón **«Emparejar»**. No guardar el código.

Al emparejar:

1. `POST {url}/v1/pair` con `{"code": "..."}` (sin token ni firma). Respuesta `201`:
   `{client, token, signing_secret, token_expires_at}` = **credencial de alta**. `422` = código inválido,
   usado o vencido (pedir uno nuevo); `403` = la IP de la plataforma no está en `GATEWAY_ALLOWED_SOURCES`.
2. Guardarla cifrada en `Provider::credentials['provisioning']` y probarla con `GET /v1/ping`
   (responde `platform: true`).
3. Con ella, emitir la **credencial de servicio**: `POST /v1/admin/platform-credential` →
   `{client, token, signing_secret, token_expires_at}`. Guardarla cifrada en `Provider::credentials['service']`.
4. Si había `credentials['catalog']`, borrarla: emparejar ya revocó todas las credenciales anteriores
   de la plataforma en el gateway.

Emparejar otra vez (otro servidor, rotación o credencial perdida) repite estos pasos; los afiliados no
se tocan, sus credenciales siguen igual.

- Quitar de la pantalla los campos de token y secreto de catálogo y de alta (ya no se escriben a mano).
- Renovación en `gateway:renew-credentials` (diario), con el mismo patrón que `GatewayProvisioning::connect`
  (emitir → guardar → probar con `GET /v1/ping` → revocar la anterior con `DELETE /v1/admin/clients/{client}`),
  cuando falten menos de `gateway.renew_days_before` días para su `token_expires_at`:
  - **credencial de alta:** `POST /v1/admin/provisioning-credential` (con la de alta vigente). Si se deja
    vencer, ya no se puede renovar: hay que volver a emparejar con un código.
  - **credencial de servicio:** `POST /v1/admin/platform-credential`.

  Si la renovación falla, avisar al staff cada día (como hoy con los afiliados). `php artisan gateway:doctor`
  también avisa en el gateway cuando a la credencial de alta le quedan menos de 30 días.

## 2. Catálogo con la credencial de servicio

- `GatewayCatalog` (o donde se lea el catálogo de la plataforma) pasa a `GET /v1/platform/products` con la
  credencial de servicio. Trae **todo** lo que el gateway vende, con `flow`, `periods`, `application_types`,
  documentos y `provider_accounts`. Ya no depende de que el tenant «plataforma» tenga productos.
- El catálogo de **cada afiliado** sigue siendo `GET /v1/products` con su propia credencial.

## 3. Reenvío de los avisos del proveedor (nuevo)

FirmaSegura avisa a la plataforma; la plataforma reenvía al gateway por el túnel, **sin leer el aviso**.

**Ruta pública** (dominio central, no el del afiliado), fuera de los middlewares de sesión y CSRF:

```php
Route::post('provider-webhooks/{provider}/{account}/{token}', ProviderWebhookRelayController::class)
    ->where('provider', '[a-z0-9-]{1,30}')
    ->whereUuid('account')
    ->where('token', '[A-Za-z0-9]{1,64}')
    ->middleware('throttle:provider-webhooks'); // p. ej. 300/min por IP
```

**Controlador:**

1. Rechazar cuerpos de más de 64 KB (`413`).
2. Encolar `RelayProviderEvent($provider, $account, $token, $request->getContent())` y responder `202` de inmediato.
3. **No** decodificar, validar, guardar en base ni registrar en logs el cuerpo: algunos flujos de
   FirmaSegura envían el certificado y su contraseña en el aviso.

**Job `RelayProviderEvent`** (`ShouldQueue`, `ShouldBeEncrypted`; `tries = 5`, `backoff = [10, 30, 60, 300]`):

- `POST {base_url}/v1/platform/provider-events/{provider}/{account}/{token}` con la **credencial de
  servicio**, firmado como cualquier llamada (token + HMAC). El cuerpo es el **original exacto**, con
  `Content-Type: application/json`.
- `202` o `422` → listo (`422` = el gateway no reconoció el evento: no reintentar).
- `404` → token o cuenta inválidos: **no reintentar**, registrar una advertencia (puede ser ruido de internet).
- `5xx` o timeout → reintentar.

**Configuración en el gateway:** `GATEWAY_WEBHOOK_URL=https://<dominio central>/provider-webhooks`.

## 4. Una sola URL de notificaciones (por el túnel)

**Hoy:** `GatewayProvisioning::notificationUrl()` usa el dominio del afiliado. Eso obliga al gateway a salir
a internet hacia cada dominio, y el gateway ya no lo permite (`GATEWAY_NOTIFICATION_HOSTS`).

**Cambio:**

- `notificationUrl()` devuelve **una URL central**, accesible por el túnel, p. ej.
  `https://plataforma.interno/webhooks/signing-gateway` (config `services.signing_gateway.notification_url`).
- El controlador de notificaciones pasa a ser central. Cada aviso trae ahora `tenant` (external_id) y
  `client` (uuid de la credencial):
  1. Buscar el afiliado por `tenant` y, dentro de él, la `GatewayCredential` vigente.
  2. Verificar `X-Gateway-Signature` con el `notification_secret` de **esa** credencial (y la ventana
     de `X-Gateway-Timestamp`) **antes** de procesar nada.
  3. Procesar el evento dentro del contexto del afiliado (`$tenant->run(...)`), como hoy.
- Volver a conectar a los afiliados existentes («Renovar credencial») para que sus credenciales tengan la URL nueva.
- **En el gateway:** `GATEWAY_NOTIFICATION_HOSTS=plataforma.interno`.

## 5. Alta de afiliados sin credenciales huérfanas

En `GatewayProvisioning::connect()`:

- Enviar `Idempotency-Key` en `POST /v1/admin/tenants/{id}/clients`, **estable entre reintentos del mismo
  job**: p. ej. `connect:{tenant}:{$this->job->uuid()}` (pasarla desde `ConnectTenantToGateway`). Si el
  intento anterior emitió y falló al guardar, el gateway revoca esa credencial (`replaced_clients`).
- Después de guardar la nueva, opcional y recomendado: `GET /v1/admin/tenants/{id}/clients` y revocar
  toda credencial activa que no sea la guardada.
- Enviar siempre `allowed_ips` (`GATEWAY_EGRESS_IPS`); en producción, que sea obligatoria.

## 6. Suspender y bloquear afiliados

- En el listener de `SubscriptionChanged`:
  - cancelada, vencida o impaga → `POST /v1/admin/tenants/{id}/suspend` con `reason`;
  - reactivada → `POST /v1/admin/tenants/{id}/resume`.
- Botón de staff **«Bloquear por incidente»** → `POST /v1/admin/tenants/{id}/block`. Revoca todas sus
  credenciales; al reactivarlo hay que usar «Conectar con el gateway» de nuevo.
- Nuevo evento de notificación `session.cancelled` (con `reason`): marcar la solicitud local como
  cancelada y mostrar el motivo al vendedor.
- Con el afiliado suspendido, el gateway responde `403` a todo menos `ping`, listar y ver solicitudes y
  retirar certificados: mostrar «Cuenta suspendida» en lugar de un error genérico.

## 7. Otros ajustes

- **`503` al crear una solicitud** = emisión detenida por el operador (`gateway:halt`): mostrar «La emisión
  está detenida temporalmente, intente más tarde» y no reintentar en bucle.
- **Registro de emisiones:** la sincronización (`GET /v1/admin/issuances?filter[after_sequence]=N`) puede
  usar la credencial de servicio en vez de la de alta.
- **Productos por afiliado:** para vender vigencias específicas o fijar el proveedor, usar
  `PUT /v1/admin/tenants/{id}/products` (la lista simple de `PUT /v1/admin/tenants/{id}` sigue funcionando).
- **Guiar el flujo con `next_action`** y pedir los términos con `GET /v1/terms/current?product=…`.

## 8. Monitor del gateway: pantalla y alertas por correo

El gateway no tiene interfaz: su estado se ve en la plataforma. Seguir la sección «Interfaz y diseño» de
`CLAUDE.md` y copiar el estilo de `/kit`.

### 8.1 Dónde vive y quién lo ve

- Permiso nuevo en `Modules/Gateway/app/Enums/Permission.php` (el módulo aún no tiene enum):
  `HealthView = 'gateway.health.view'`, `label()` «Ver el estado del gateway». Luego `permissions:sync`.
- Destino propio en el sidebar central, grupo «Backoffice», debajo de «Operación»: «Estado del gateway»,
  icono `shield`, `permission: 'gateway.health.view'`.
- Ruta `GET /central/gateway/health` (`central.gateway-health.show`); página
  `Modules/Gateway/resources/js/Pages/Staff/Health.vue` con
  `defineOptions({ layout: { title: 'Estado del gateway' } })`.
- Los correos van a los usuarios centrales con `gateway.health.view`.

### 8.2 Datos y comando `gateway:monitor`

Tablas centrales:

| Tabla | Campos |
|---|---|
| `gateway_health_checks` (estado actual) | `key` (PK), `label`, `status` (`ok`/`warn`/`fail`), `detail`, `hint`, `since`, `failures_in_a_row`, `alerted_at`, `checked_at` |
| `gateway_health_incidents` (historial) | `key`, `label`, `status`, `detail`, `started_at`, `resolved_at` |

`gateway:monitor` (scheduler de la plataforma, cada minuto, `withoutOverlapping`):

1. `GET /v1/platform/health` con la **credencial de servicio**, timeout de 10 s. Respuesta:
   ```json
   { "status": "degraded", "checked_at": "…",
     "checks": [ { "key": "queue:interactive", "label": "Cola interactive", "status": "warn",
                   "detail": "Atrasada: 12 en espera, la más antigua hace 95 s",
                   "hint": "Hay más trabajo que workers: agregue un worker para esta cola." } ] }
   ```
2. Si no responde (timeout, conexión, `5xx`, `401`/`403`), registrar la comprobación sintética
   `gateway` «El emisor de firmas no responde» en `fail`. **Eso también es una caída.**
3. Actualizar `gateway_health_checks`: al cambiar de estado, guardar `since`; al pasar a `warn` o `fail`,
   abrir un incidente; al volver a `ok`, cerrarlo (`resolved_at`).
4. Decidir las alertas (8.4).

- `POST /central/gateway/health/check` («Consultar ahora», throttle 6/min): despacha el monitor y devuelve un toast.
- Prop compartida `gatewayHealth: { status, checked_at }` solo para quien tiene el permiso, diferida y leída
  de la tabla (nunca llama al gateway en cada página).

### 8.3 Pantalla «Estado del gateway»

Una sola vista sin scroll en 1440×900, dos columnas (`xl:col-span-8` + `xl:col-span-4`); en móvil, la
tarjeta de acción primero.

- **Encabezado** `<Heading variant="compact">`: «Revisa si el emisor de firmas está funcionando. Se
  actualiza cada minuto.» A la derecha, `RefreshButton :only="['health']" :seconds="30"` y el botón
  secundario «Consultar ahora».
- **Aviso de una línea** según el estado:

  | Estado | Color | Texto |
  |---|---|---|
  | `ok` | verde | «Todo funciona. Consultado hace 40 s.» |
  | `degraded` | ámbar | «Funciona con 2 avisos.» |
  | `down` | rojo | «Hay 1 falla: la emisión de firmas puede estar afectada.» |
  | `checked_at` de hace más de 3 min | gris | «Sin datos recientes: el monitor de la plataforma no se está ejecutando.» |

  El último caso evita que la pantalla siga en verde si se cae el propio monitor.
- **KPIs** (`StatCard`, 4 columnas): Estado general · Fallas · Avisos · Última consulta.
- **Izquierda**, `Card` con pestañas «Con problemas N · Todas M · Incidentes» (abre en «Con problemas» si
  hay alguno, si no en «Todas»):
  - Tabla (cabecera `bg-ink-50 uppercase text-xs`, filas `py-2.5`): Comprobación (`label`), Estado
    (`Badge` con `healthStatusTone()`: `ok`→`green` «Bien», `warn`→`amber` «Aviso», `fail`→`red` «Falla»),
    Detalle, Desde (`useDateTime`). En las filas que no están bien, el `hint` debajo del detalle con `ui.help`.
  - Grupos con un separador ligero: «Procesos» (scheduler, colas, jobs fallidos, reconciliación),
    «Proveedor» (FirmaSegura y su cupo de consultas), «Avisos y seguridad» (notificaciones, emisión,
    credencial de alta, registro de emisiones). Dentro de cada grupo: fallas, avisos y lo que está bien.
  - «Incidentes»: listado paginado (`Pagination`) con comprobación, estado, inicio, fin y duración;
    filtro «Abiertos · Todos».
- **Derecha**, tarjeta «Qué hacer ahora» (`xl:sticky`, pie `border-t bg-ink-50`): cada problema con su
  `label` y su `hint`, primero las fallas; si todo está bien, «No hay nada que hacer.» Debajo, «Alertas
  por correo: se avisa a N personas con permiso para ver el estado del gateway» y el botón secundario
  «Enviar correo de prueba».
- **Estado vacío** (sin credencial de servicio): aviso ámbar «Para ver el estado del gateway, emparéjalo
  primero» con «Hacerlo ahora →» hacia la ficha del proveedor (Catálogo › Proveedores).
- **Barra superior** (`AppShell`, solo con el permiso): punto verde, ámbar o rojo con `title` y
  `aria-label` («Estado del gateway: con fallas») que enlaza a la pantalla.
- Textos simples, sin jerga: «El emisor de firmas no responde», no «gateway down». Los `label`, `detail` y
  `hint` ya llegan en español: mostrarlos tal cual.
- Solo tokens del tema (`emerald`, `amber`, `red` solo para estados), variante `dark:` en todo, iconos con
  `<Icon>`, verificar a 375 px sin scroll horizontal.

### 8.4 Alertas por correo y campana

Notificación `GatewayHealthAlert`, `via(): ['mail', 'database']`; `toArray()` con `title`, `body` y `url`
absoluta a la pantalla.

- Asunto: `[Gateway] Falla: Cola interactive`, `[Gateway] Aviso: …` o `[Gateway] Resuelto: …`.
- Contenido: `label`, `detail`, `hint`, desde cuándo y el botón «Ver estado del gateway».
- Reglas: `fail` dos consultas seguidas → avisar; `warn` durante 10 minutos → avisar; mientras siga mal,
  un recordatorio por hora (`alerted_at`); al volver a `ok`, «Resuelto» solo si antes se avisó.
- Enviar por una cola distinta de la que llama al gateway, para que el aviso salga aunque esa esté atascada.

### 8.5 Tests

- La pantalla responde `403` sin `gateway.health.view`.
- El monitor (con `Http::fake`) guarda las comprobaciones y abre y cierra incidentes; sin respuesta del
  gateway, marca `down`.
- Alertas con `Notification::fake`: una sola al segundo `fail`, el recordatorio a la hora y «Resuelto» al
  recuperarse.
- El aviso «Sin datos recientes» aparece cuando la última consulta tiene más de 3 minutos.

## 9. Variables nuevas del gateway (producción)

| Variable | Valor |
|---|---|
| `GATEWAY_ALLOWED_SOURCES` | IP de la plataforma en el túnel (p. ej. `10.8.0.2`). Sin ella la API rechaza todo |
| `GATEWAY_NOTIFICATION_HOSTS` | Host central de notificaciones (p. ej. `plataforma.interno`) |
| `GATEWAY_WEBHOOK_URL` | `https://<dominio central>/provider-webhooks` |

Firewall del gateway: entrada **solo** por el túnel desde la plataforma; salida solo a FirmaSegura, a la
plataforma (túnel), DNS, NTP y actualizaciones. Ya no hace falta abrir el 443 a internet.

## Orden sugerido

1. Puntos 3 y 4 (reenvío y URL central), porque cambian la red. Desplegarlos junto con las variables del punto 9.
2. Puntos 1 y 2 (emparejamiento y credencial de servicio).
3. Punto 8 (monitor y alertas): en cuanto exista la credencial de servicio, para ver el gateway desde el primer día.
4. Punto 5 (idempotencia).
5. Punto 6 (suspensión).
6. Punto 7.
