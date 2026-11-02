# Cambios en la plataforma (signature-saas) para el gateway endurecido

Tareas para `signature-saas` tras el endurecimiento del gateway (2026-11-02). Referencia completa de la
API: `docs/INTEGRACION_SAAS.md`. Los nombres de clases son los actuales de `Modules/Gateway` y
`Modules/Catalog`.

**Objetivo:** el gateway solo habla con la plataforma (por el túnel) y con FirmaSegura. Nada de él queda
en internet. El operador configura una sola credencial; todo lo demás lo hace la plataforma.

---

## 1. Ficha del proveedor: solo URL y credencial de alta

**Hoy:** la pantalla pide la credencial de catálogo y la de alta.
**Cambio:** pedir solo **URL del microservicio** y **credencial de alta** (token + secreto de firma).

- Al guardar la credencial de alta, emitir la **credencial de servicio**:
  `POST /v1/admin/platform-credential` (con la de alta) → `{client, token, signing_secret, token_expires_at}`.
  Guardarla cifrada en `Provider::credentials['service']`. Probarla con `GET /v1/ping`.
- Quitar los campos de la credencial de catálogo. Migración de datos: si existe `credentials['catalog']`,
  emitir la de servicio y luego revocar la de catálogo con `DELETE /v1/admin/clients/{client}`.
- Renovación: en `gateway:renew-credentials`, si la de servicio vence en menos de
  `gateway.renew_days_before` días, emitir una nueva, guardarla, probarla y revocar la anterior
  (el mismo patrón que `GatewayProvisioning::connect`).

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

## 8. Variables nuevas del gateway (producción)

| Variable | Valor |
|---|---|
| `GATEWAY_ALLOWED_SOURCES` | IP de la plataforma en el túnel (p. ej. `10.8.0.2`). Sin ella la API rechaza todo |
| `GATEWAY_NOTIFICATION_HOSTS` | Host central de notificaciones (p. ej. `plataforma.interno`) |
| `GATEWAY_WEBHOOK_URL` | `https://<dominio central>/provider-webhooks` |

Firewall del gateway: entrada **solo** por el túnel desde la plataforma; salida solo a FirmaSegura, a la
plataforma (túnel), DNS, NTP y actualizaciones. Ya no hace falta abrir el 443 a internet.

## Orden sugerido

1. Puntos 3 y 4 (reenvío y URL central), porque cambian la red. Desplegarlos junto con las variables del punto 8.
2. Puntos 1 y 2 (credencial de servicio).
3. Punto 5 (idempotencia).
4. Punto 6 (suspensión).
5. Punto 7.
