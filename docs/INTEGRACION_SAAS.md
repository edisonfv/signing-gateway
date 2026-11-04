# Integración del SaaS con el Signing Gateway

Guía para implementar en el SaaS (backend y frontend) la emisión de firmas electrónicas a través del
Signing Gateway. Está escrita para que un desarrollador o un agente de IA la siga sin conocer el
código del gateway.

> Fuente de verdad del contrato: la especificación OpenAPI del gateway (`docs/openapi.json` del
> repositorio del gateway, o `/docs/api` en local). Si esta guía y el OpenAPI difieren, manda el OpenAPI.

---

## 1. Qué es el gateway y qué NO debe hacer el SaaS

- El gateway es el **único** componente que habla con FirmaSeguraEC (y con futuros proveedores).
  Guarda el token del proveedor, aplica topes de emisión, valida datos y documentos, y custodia
  temporalmente los certificados.
- El SaaS **nunca** llama a FirmaSegura directamente ni conoce su token.
- El **navegador del titular nunca** habla con el gateway ni recibe credenciales del SaaS. Todo pasa
  por el **backend del SaaS**. La única excepción son las credenciales AWS temporales de la prueba de
  vida (sección 7), que el backend del SaaS entrega a su propio frontend.

```
Navegador del titular ──► Backend del SaaS ──(token + firma HMAC)──► Gateway ──► FirmaSegura
        ▲                        ▲                                      │
        └── credenciales AWS ────┘◄──── notificaciones firmadas ────────┘
            (solo prueba de vida)
```

---

## 2. Configuración por cliente (admin del SaaS)

Cada cliente del SaaS (tenant) tiene **su propia credencial**, limitada a su tenant. Lo normal es que
la plataforma la emita sola con su credencial de alta (abajo); el operador puede hacerlo a mano con
`php artisan gateway:client:create`. `gateway:client:revoke` revoca una credencial al instante.

**Red:** la API solo responde a las IPs de `GATEWAY_ALLOWED_SOURCES` (la IP de la plataforma en el
túnel). En producción es obligatoria: sin ella responde `403` a todo.

### Instalación y emparejamiento

En el servidor del gateway:

```bash
php artisan gateway:install   # llaves, migraciones, catálogos, token del proveedor, verificación y código
php artisan gateway:pair      # solo si hace falta otro código (vence en 10 minutos, un solo uso)
php artisan gateway:doctor    # verifica todo; ejecutarlo tras instalar o mudar de servidor
```

En la plataforma, la ficha del proveedor pide **URL del gateway** y **código de emparejamiento**.
«Emparejar» llama a `POST /v1/pair` `{code}` (sin token ni firma; solo desde `GATEWAY_ALLOWED_SOURCES`,
5 intentos por minuto) y recibe **una sola vez** la credencial de alta de la plataforma
(`{client, token, signing_secret, token_expires_at}`), limitada a la IP que emparejó. Ningún secreto se
copia a mano. Emparejar de nuevo revoca las credenciales anteriores de la plataforma: es la forma de
rotarlas o de mudar el gateway de servidor.

La plataforma **no es un afiliado**: sus credenciales no tienen tenant y solo llegan a `/v1/admin` y
`/v1/platform` (`GET /v1/ping` responde `platform: true`).

### Alta automática desde el backoffice del SaaS

Con la **credencial de alta** (ability `clients.manage`, obtenida al emparejar), el SaaS da de alta y
renueva afiliados sin el operador:

| Petición | Qué hace |
|---|---|
| `GET /v1/admin/products` | Catálogo completo del gateway: cada producto con la oferta del proveedor (flujo, vigencias, documentos) y las cuentas que lo atienden (`provider_accounts`). |
| `PUT /v1/admin/tenants/{external_id}` | Registra o actualiza el afiliado (`name`, `products` opcional como lista simple; conserva las condiciones ya configuradas). Idempotente. |
| `GET /v1/admin/tenants/{external_id}/products` | Condiciones del afiliado (`settings`) y la oferta efectiva que verá en `GET /v1/products` (`available`). |
| `PUT /v1/admin/tenants/{external_id}/products` | Reemplaza los productos del afiliado: `[{product, periods?, preferred_provider_account_id?}]`. Lo que no se envía queda desactivado. |
| `POST /v1/admin/tenants/{external_id}/clients` | Emite su credencial con `delivery_public_key` y `notification_url` (HTTPS, host en `GATEWAY_NOTIFICATION_HOSTS`); devuelve token y secretos **una sola vez** y `token_expires_at`. Con `Idempotency-Key`, un reintento revoca la credencial del intento anterior (`replaced_clients`). |
| `GET /v1/admin/tenants/{external_id}/clients` | Credenciales del afiliado (sin secretos): para detectar y revocar las que la plataforma no tiene guardadas. |
| `DELETE /v1/admin/clients/{client}` | Revoca una credencial (al renovar: primero se guarda la nueva). |
| `POST /v1/admin/tenants/{external_id}/suspend` | Suspensión comercial inmediata: no crea ni avanza solicitudes; las abiertas no emitidas se cancelan en el proveedor. Aún consulta y retira lo ya emitido. |
| `POST /v1/admin/tenants/{external_id}/block` | Incidente: como suspender, y revoca todas sus credenciales; no puede hacer nada. |
| `POST /v1/admin/tenants/{external_id}/resume` | Reactiva (tras un bloqueo hay que emitir credenciales nuevas). |
| `POST /v1/admin/platform-credential` | Emite la **credencial de servicio** de la plataforma (ability `platform.service`), con las IPs de la de alta. |
| `POST /v1/admin/provisioning-credential` | **Renueva la credencial de alta** sin volver a emparejar: emite una nueva con las mismas IPs; la anterior sigue viva hasta que la plataforma la revoca con `DELETE /v1/admin/clients/{client}`. |
| `GET /v1/admin/issuances` | Registro de emisiones: un certificado emitido por fila, en orden de `sequence` (filtros `after_sequence`, `tenant`, `product`, `provider_account_id`, `issued_from`, `issued_to`). |
| `GET /v1/admin/issuances/summary` | Cantidades por afiliado, cuenta de proveedor, producto y vigencia, con los mismos filtros. |

### Credencial de servicio y reenvío de avisos del proveedor

El operador solo genera el código de emparejamiento. Con la credencial de alta la plataforma emite
(y renueva) su credencial de servicio, de uso frecuente y bajo privilegio:

| Petición (ability `platform.service`) | Qué hace |
|---|---|
| `GET /v1/platform/products` | Catálogo completo del gateway (igual a `GET /v1/admin/products`). |
| `GET /v1/platform/health` | Estado de salud para el monitor: `status` (`ok`, `degraded`, `down`) y `checks[]` con `key`, `label`, `status`, `detail` y `hint`. |
| `POST /v1/platform/provider-events/{provider}/{account}/{token}` | Reenvío de un aviso del proveedor. |

**El gateway no se expone a internet.** El proveedor avisa a la ruta pública de la plataforma que
el gateway le da como `callbackUrl` (`GATEWAY_WEBHOOK_URL`, p. ej.
`https://app.midominio.com/provider-webhooks/{provider}/{account}/{token}`). La plataforma:

1. Recibe el POST sin sesión ni CSRF, limita el tamaño (64 KB) y la frecuencia, y responde `202` al instante.
2. **No interpreta el cuerpo** ni valida `{token}` (solo lo conoce el gateway).
3. En un job, reenvía el **cuerpo exacto** a `POST /v1/platform/provider-events/{provider}/{account}/{token}`
   (los mismos segmentos), firmado con la credencial de servicio. Reintenta si el gateway no responde.

El gateway valida el token y, si coincide, adelanta la reconciliación de esa sesión. El aviso nunca
cambia un estado por sí mismo; si se pierde, la reconciliación cada 5 minutos lo cubre.

### Registro de emisiones (para facturar y conciliar)

El gateway registra cada certificado que emite el proveedor, lo que el proveedor cobra: solicitud,
afiliado, credencial, cuenta del proveedor, producto, vigencia, referencia del proveedor, número de serie
y fecha. Sin precios ni datos personales; los precios de costo y de venta viven en la plataforma.

- **Sincronizar:** guardar el último `sequence` recibido y pedir `filter[after_sequence]=<ese valor>`.
  El correlativo no tiene huecos: si falta un número, algo está mal. La notificación `certificate.issued`
  avisa en el momento; la consulta es la fuente de verdad si una notificación se pierde.
- **Cuadrar con el proveedor:** `summary` con `filter[issued_from]`/`filter[issued_to]` del mes da las
  cantidades por cuenta y vigencia que deben coincidir con su factura.
- **Integridad:** el registro solo admite inserciones (un trigger de la base rechaza cambios y borrados)
  y cada fila sella con su `hash` el de la anterior. `php artisan gateway:ledger:verify` lo comprueba
  todos los días.

Las credenciales emitidas así son de cliente normal: nunca reciben `clients.manage`. Cada uso queda
en la auditoría con la credencial de alta como actor.

Condiciones por producto del afiliado:

- `periods`: vigencias que puede vender (subconjunto de las que ofrece el proveedor). Omitido o `null` = todas.
- `preferred_provider_account_id`: cuenta de proveedor que lo atiende. Si está inactiva, el gateway usa la
  cuenta de mayor prioridad. Omitido o `null` = por prioridad.
- Un afiliado sin productos configurados usa `GATEWAY_DEFAULT_PRODUCTS`.

```json
PUT /v1/admin/tenants/acme/products
{ "products": [
  { "product": "HEADLESS", "periods": ["ONE_YEAR", "TWO_YEARS"] },
  { "product": "REGULAR" }
] }
```

El admin del SaaS debe permitir configurar por cliente:

| Campo | Ejemplo | Cómo se guarda en el SaaS |
|---|---|---|
| URL del gateway | `https://signing-gateway.interno/api` | Texto. En producción es una URL interna (VPN/WireGuard) |
| Token de acceso | `01a16…\|Xk29…` | **Cifrado** (p. ej. cast `encrypted` de Laravel). Nunca en logs |
| Secreto de firma | 64 caracteres | **Cifrado** |
| Secreto de notificaciones | 64 caracteres | **Cifrado** |
| Llave privada de entrega X25519 | generada por el SaaS (sección 9) | **Cifrada**, con una llave distinta de la del resto de datos si es posible |

El SaaS le entrega al operador del gateway, para que los registre en la credencial:

- la **llave pública** de entrega (base64, 32 bytes);
- la **URL de notificaciones** del SaaS (HTTPS), donde el gateway enviará los eventos;
- las **IPs** desde las que saldrá el SaaS (el gateway puede restringir por IP).

Agregar en el admin un botón **"Probar conexión"** que llame a `GET /v1/ping` (sección 3) y muestre el
tenant, las abilities y el vencimiento del token.

---

## 3. Autenticación de cada petición al gateway

Todas las rutas exigen **las tres cosas**:

1. `Authorization: Bearer <token de acceso>`
2. Cabeceras de firma:
   - `X-Timestamp`: epoch en **segundos** (UTC). Ventana aceptada: ±300 s → sincronizar la hora (NTP).
   - `X-Nonce`: valor aleatorio único por petición, 16 a 64 caracteres (p. ej. 32 hex). No reutilizar.
   - `X-Signature`: HMAC-SHA256 en **hex minúsculas** con el **secreto de firma** sobre la cadena canónica.
3. `Accept: application/json` (y `Content-Type: application/json` si hay cuerpo).

**Cadena canónica** (líneas unidas con `\n`, sin `\n` final):

```
MÉTODO                       ← en mayúsculas: GET, POST
/api/v1/ruta?query           ← ruta COMPLETA desde /api, con la query DECODIFICADA y en el orden enviado
X-Timestamp
X-Nonce
sha256_hex(cuerpo_crudo)     ← del cuerpo exacto que se envía; para GET sin cuerpo, sha256 de "" 
```

Ejemplo de cadena para `GET /api/v1/provinces?filter[name]=santo`:

```
GET
/api/v1/provinces?filter[name]=santo
1761200000
b1f0c3e9a7d24c6f9e2a5b8c7d1e0f3a
e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
```

Reglas importantes:
- Firmar **exactamente** los bytes que se envían como cuerpo (serializar el JSON una vez y enviar ese mismo string).
- La ruta firmada incluye el prefijo `/api`.
- Respuestas: `401` token o firma inválidos (timestamp fuera de ventana, nonce repetido); `403` credencial
  revocada, tenant suspendido, IP no permitida o token sin la ability; `429` límite de 120 peticiones/minuto.

### Cliente de referencia (Laravel)

```php
namespace App\Services\SigningGateway;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GatewayClient
{
    public function __construct(
        private string $baseUrl,          // https://…/api
        #[\SensitiveParameter] private string $token,
        #[\SensitiveParameter] private string $signingSecret,
    ) {}

    public function get(string $path, array $query = []): Response
    {
        return $this->send('GET', $path, $query);
    }

    public function post(string $path, ?array $data = null, array $headers = []): Response
    {
        return $this->send('POST', $path, [], $data, $headers);
    }

    private function send(string $method, string $path, array $query = [], ?array $data = null, array $headers = []): Response
    {
        $queryString = $query ? '?'.http_build_query($query) : '';
        $body = $data === null ? '' : json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $timestamp = (string) now()->getTimestamp();
        $nonce = Str::random(32);
        $signedPath = parse_url($this->baseUrl, PHP_URL_PATH).$path.rawurldecode($queryString); // /api/v1/…

        $canonical = implode("\n", [$method, $signedPath, $timestamp, $nonce, hash('sha256', $body)]);

        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout(30)
            ->withHeaders([
                'X-Timestamp' => $timestamp,
                'X-Nonce' => $nonce,
                'X-Signature' => hash_hmac('sha256', $canonical, $this->signingSecret),
                ...$headers,
            ])
            ->withBody($body, 'application/json')
            ->send($method, rtrim($this->baseUrl, '/').$path.$queryString);
    }
}
```

---

## 4. Elegir el flujo según las condiciones comerciales

`GET /v1/products` devuelve los productos que el tenant tiene contratados y sus requisitos. El frontend
del SaaS **solo debe ofrecer lo que viene en esta respuesta** y construir el formulario con ella.

| `product` | Nombre | Validación de identidad | Entrega del certificado |
|---|---|---|---|
| `HEADLESS` | Flujo por API | **Prueba de vida** con cámara (sección 7) | Al SaaS, **sellado** (sección 9) |
| `REGULAR` | Firmas Regulares | **Documentos** que revisan operadores del proveedor | El proveedor lo envía **por correo al titular** |

Los requisitos los declara **el proveedor que atiende al tenant**, no el gateway: si mañana otro
proveedor atiende el producto, el SaaS recibe sus requisitos aquí sin cambiar código.

Campos útiles de cada producto: `flow` (`LIVENESS_API` o `DOCUMENT_REVIEW`), `delivery_mode`,
`application_types[].value` y `required_documents`, `periods` (solo las vigencias contratadas),
`requires_liveness`, `requires_birth_date`, `allows_holder_password`, `documents[]` (tipos MIME y tamaño
máximo) y `guides` (textos guía para selfie, cédula y la plantilla del video para mayores de 65).

Si se envía un producto no contratado, el gateway responde `422` en `product`; una vigencia no contratada,
`422` en `period`.

---

## 5. Catálogos

- `GET /v1/provinces` → `[{id, name, cities_count}]`
- `GET /v1/provinces/{id}/cities` → `[{id, name, province_id}]` (filtros: `filter[name]=…`, `sort=name`)
- `GET /v1/catalogs/application-types` y `GET /v1/catalogs/periods` → `[{value, label}]`

Se envía el **`city_id`** (UUID) de la ciudad; el gateway resuelve los nombres que espera el proveedor.
Cachear estos catálogos en el SaaS (cambian muy poco).

---

## 6. Crear la solicitud

1. `GET /v1/terms/current?product=HEADLESS` (o `REGULAR`; por defecto `HEADLESS`) →
   `{id, version, content, content_sha256}`: los términos del proveedor que atenderá ese producto. **Mostrar `content` al titular**
   y exigir que acepte (checklist de certificación). Guardar en el SaaS la evidencia de aceptación.
2. `POST /v1/signature-sessions` con la cabecera **`Idempotency-Key`** (UUID generado por el SaaS y
   guardado con la operación; si hay timeout, reintentar con **la misma** clave devuelve la misma solicitud).

```json
{
  "product": "HEADLESS",
  "application_type": "NATURAL_PERSON",
  "period": "ONE_YEAR",
  "password": "Opcional, no recomendado (ver abajo)",
  "applicant": {
    "identification_number": "1710034065",
    "finger_code": "E4333V2422",
    "email": "titular@correo.com",
    "cellphone": "+593998765432",
    "address": "Av. Amazonas N12-34",
    "city_id": "uuid-de-la-ciudad",
    "person_ruc": "1710034065001",
    "birth_date": "1990-05-10"
  },
  "company": {
    "ruc": "1790012345001",
    "name": "EMPRESA S.A.",
    "position": "GERENTE GENERAL",
    "appointment_expires_at": "2028-12-31"
  },
  "documents": {
    "identification_front": "<base64>",
    "identification_back": "<base64>",
    "identification_selfie": "<base64>",
    "company_ruc_pdf": "<base64>",
    "representative_appointment_pdf": "<base64>",
    "appointment_acceptance_pdf": "<base64>",
    "company_constitution_pdf": "<base64>",
    "authorization_video": "<base64>"
  },
  "terms": {
    "id": "id de GET /v1/terms/current",
    "version": "2.0.9",
    "accepted_at": "2026-10-23T10:15:00-05:00",
    "applicant_ip": "IP pública del navegador del titular",
    "user_agent": "User-Agent del navegador del titular"
  }
}
```

Reglas por producto:

| Campo | HEADLESS | REGULAR |
|---|---|---|
| `application_type` | `NATURAL_PERSON`, `NATURAL_PERSON_RUC` | además `LEGAL_REPRESENTATIVE` |
| `applicant.person_ruc` | solo si `NATURAL_PERSON_RUC` (cédula + `001`) | igual |
| `applicant.birth_date` | no se envía | **obligatorio** (`Y-m-d`). Menores de 18 → rechazo |
| `password` | opcional (ver abajo) | prohibido |
| `company` | prohibido | solo y obligatorio si `LEGAL_REPRESENTATIVE` |
| `documents` | prohibido | obligatorio según `required_documents`; `authorization_video` si tiene 65 años o más |

- Celular: formato exacto `+5939XXXXXXXX`. Cédula: 10 dígitos (en producción se valida el dígito verificador).
- Documentos: base64 **sin** prefijo `data:…;base64,`. Imágenes JPG/PNG ≤ 5 MB, PDF ≤ 5 MB, video MP4 ≤ 20 MB.
  El gateway inspecciona el contenido real; la extensión no importa.
- **Contraseña del certificado (`password`, solo HEADLESS).** Es **opcional**. Si no se envía, FirmaSegura
  genera una aleatoria de 16 caracteres, que llega con el p12 en la entrega sellada (sección 9). **Es la
  opción recomendada:** nadie la elige ni la conoce antes de la emisión.
  Si el titular prefiere elegirla, el SaaS puede enviarla con estas condiciones:
  - **Política:** de 8 a 64 caracteres, **solo letras sin tilde (A-Z, a-z) y números**, con al menos una
    mayúscula, una minúscula y un número. FirmaSegura rechaza con `WEAK_PASSWORD` contraseñas con símbolos
    (`Abcd2009**`) aunque su documentación no lo diga; por eso el gateway no los acepta.
    Si no la cumple, la respuesta es `422` en `password`.
  - **Riesgo que hay que explicarle al titular:** una contraseña elegida suele ser más corta y predecible,
    y se reutiliza en otros servicios. Quien obtenga el archivo .p12 y la adivine **puede firmar en su nombre**.
    Además, la contraseña pasa por el SaaS y por el gateway antes de la emisión.
  - **Custodia:** tratarla como secreto (no guardarla, no registrarla en logs, `#[\SensitiveParameter]`).
    El gateway la guarda cifrada con la llave de contraseñas de la bóveda solo hasta registrarla en el
    proveedor, y la borra al registrarla o al cerrarse la solicitud. Nunca aparece en las respuestas.
- Respuesta: `202` (creada) o `200` (ya existía con ese `Idempotency-Key`), con el recurso de la sección 8.
  Errores de validación: `422` con `errors` por campo (mostrarlos en el formulario).

---

## 7. Flujo por API (HEADLESS): prueba de vida en el frontend

FirmaSegura no ofrece pantallas en este flujo: el **frontend del SaaS** debe implementar la captura.
Hay una implementación de referencia en el gateway: `resources/js/dev/liveness.jsx`.

**Secuencia:**

1. Tras crear la solicitud, esperar la notificación `session.awaiting_liveness`
   (estado `AWAITING_APPLICANT`). Si el Registro Civil rechaza → `session.rejected`.
2. Cuando el titular está frente a la cámara: el backend llama `POST /v1/signature-sessions/{id}/liveness-session` (`202`).
3. Al llegar `session.liveness_ready` (o consultando `GET /v1/signature-sessions/{id}` hasta que `step = LIVENESS_SESSION`),
   el backend llama **una sola vez** `GET …/liveness-credentials`:
   ```json
   { "data": { "liveness_session_id": "…", "region": "us-east-1", "expires_at": "…",
               "credentials": { "access_key_id": "…", "secret_access_key": "…", "session_token": "…" } } }
   ```
   Una segunda llamada da `404`. Entregar al navegador **solo** estos datos (no guardarlos ni registrarlos en logs).
4. El frontend monta el componente de AWS Amplify:
   ```bash
   npm install @aws-amplify/ui-react-liveness @aws-amplify/ui-react aws-amplify react react-dom
   ```
   ```jsx
   import { FaceLivenessDetectorCore } from '@aws-amplify/ui-react-liveness';
   import '@aws-amplify/ui-react/styles.css';

   <FaceLivenessDetectorCore
     sessionId={data.liveness_session_id}
     region={data.region}
     config={{ credentialProvider: async () => ({
       accessKeyId: data.credentials.access_key_id,
       secretAccessKey: data.credentials.secret_access_key,
       sessionToken: data.credentials.session_token,
       expiration: new Date(data.expires_at),
     }) }}
     displayText={textosEnEspañol}
     onAnalysisComplete={avisarAlBackend}
     onError={(e) => /* la sesión es de un solo uso: pedir una nueva */ null}
   />
   ```
   - El componente es **solo React**. Si el SaaS usa Vue, Livewire o Blade, montar una "isla" de React solo en esa pantalla.
   - Requiere HTTPS y permiso de cámara. Mostrar la advertencia de fotosensibilidad y los textos guía.
5. `onAnalysisComplete` → el backend llama `POST …/liveness-result` con `{"liveness_session_id": "…"}` (`202`).
6. Resultado por notificación:
   - `certificate.ready` → retirar el certificado (sección 9).
   - `session.liveness_failed` → estado `FAILED_RETRYABLE`: ofrecer **reintentar** (volver al paso 2).
     `remaining_liveness_attempts` puede venir vacío; decide el estado, no el contador.
   - `session.rejected` → intentos agotados: cerrar y, si corresponde, crear una solicitud nueva.

---

## 8. Seguimiento del estado

Recurso devuelto por todos los endpoints de solicitudes:

```json
{ "data": {
  "id": "uuid", "product": "HEADLESS", "flow": "LIVENESS_API", "delivery_mode": "CLIENT_SEALED",
  "status": "AWAITING_APPLICANT", "step": "CIVIL_REGISTRY",
  "next_action": { "type": "START_LIVENESS", "method": "POST", "path": "/v1/signature-sessions/uuid/liveness-session" },
  "application_type": "NATURAL_PERSON", "period": "ONE_YEAR",
  "provider_status": "IN_PROGRESS", "provider_review": null, "remaining_liveness_attempts": null,
  "error": { "message": "…", "provider_status": "…", "codes": ["A01"] },
  "certificate_available": false,
  "created_at": "…", "issued_at": null, "delivered_at": null, "closed_at": null
} }
```

**`provider_review` (solo REGULAR, `null` en HEADLESS):** en qué está la revisión del proveedor mientras
`status` sigue en `VALIDATING`. Cuando cambia, el gateway envía `session.reconciled` aunque el estado no cambie.

```json
"provider_review": {
  "stage": "MANUALLY_VALIDATING",
  "operator_assigned_at": null,
  "observations": [
    { "type": "WARNING", "message": "… otra solicitud reciente del aplicante …9053 …", "created_at": "2026-11-03T19:10:37+00:00" }
  ],
  "updated_at": "2026-11-03T19:10:46+00:00"
}
```

- `stage`: `PENDING`, `AUTOMATICALLY_VALIDATING`, `MANUALLY_VALIDATING` (un operador del proveedor revisa los documentos), `ERROR`, `REFUSED`, `COMPLETED`.
- `operator_assigned_at`: cuándo un operador tomó la solicitud (`operatorAssignments` del proveedor); `null` mientras espera en la fila. El proveedor no informa quién es.
- `observations`: notas del proveedor para el operador (p. ej. una solicitud duplicada). No detienen el trámite; los errores que sí lo hacen llegan en `error.codes`. Los números de cédula o RUC llegan tapados (`…9053`).
- `updated_at`: última vez que el proveedor cambió la solicitud. Las fechas van en UTC.

| `status` | Significado | Acción del SaaS |
|---|---|---|
| `SUBMITTING` / `SUBMITTED` | Enviándose / registrada en el proveedor | Esperar |
| `AWAITING_APPLICANT` | Espera la prueba de vida (HEADLESS) | Mostrar la pantalla de cámara |
| `VALIDATING` | Validando (biometría u operadores) | Esperar |
| `FAILED_RETRYABLE` | Error corregible | HEADLESS: nueva prueba de vida. REGULAR: corrección (sección 10) |
| `ISSUED` | Emitido (REGULAR: el titular recibió el correo de descarga) | Informar al titular |
| `CERTIFICATE_STORED` | HEADLESS: certificado listo en el gateway | Retirarlo (sección 9) |
| `DELIVERED` | Entregado (HEADLESS: al SaaS; REGULAR: el titular lo descargó) | Fin |
| `REJECTED` / `EXPIRED` / `CANCELLED` / `FAILED` / `REVOKED` | Cerrada | Mostrar `error.message`; `FAILED` requiere soporte |

**`next_action` dice qué hacer sin conocer el proveedor.** Conviene que el SaaS se guíe por este campo
y no por `product`/`status`: así un proveedor nuevo no obliga a cambiar la lógica.

| `next_action.type` | Qué hace el SaaS |
|---|---|
| `START_LIVENESS` | Pedir la sesión de prueba de vida (`POST …/liveness-session`) |
| `FACE_LIVENESS` | Retirar las credenciales (`GET …/liveness-credentials`), correr el SDK y enviar el resultado |
| `CORRECT_DATA` | Reenviar lo indicado en `error.codes` (`POST …/corrections`) |
| `RETRIEVE_CERTIFICATE` | Retirar el certificado sellado (`GET …/certificate`) |
| `WAIT` | Esperar la notificación |
| `null` | Nada más que hacer (cerrada, o entregada por el proveedor al titular) |

- `GET /v1/signature-sessions` lista todas las solicitudes del tenant (`filter[status]=…`, `filter[product]=…`, `sort=-created_at`, paginado).
- `POST /v1/signature-sessions/{id}/refresh` consulta al proveedor **bajo demanda**. Usar solo cuando el
  usuario lo pide: comparte con la reconciliación automática un cupo de 900 consultas por hora (`429` si se agota).
- El gateway se entera de los cambios por el webhook de FirmaSegura (Flujo por API) y, como respaldo, reconcilia cada 5 minutos. **No hacer polling en bucle:** usar las notificaciones.
- `POST /v1/signature-sessions/{id}/cancel`: solo HEADLESS y antes de emitir. REGULAR no se puede cancelar.

---

## 9. Entrega del certificado (HEADLESS)

**Generar el par de llaves** (una vez por credencial, en el SaaS):

```php
$keyPair = sodium_crypto_box_keypair();
$publicKey = base64_encode(sodium_crypto_box_publickey($keyPair));   // → entregar al operador del gateway
$privateKey = base64_encode($keyPair);                              // → guardar CIFRADO en el SaaS
```

**Retirar y abrir** al recibir `certificate.ready`:

```php
$response = $gateway->get("/v1/signature-sessions/{$id}/certificate");   // UNA sola vez; luego 410
$opened = sodium_crypto_box_seal_open(base64_decode($response->json('data.sealed')), base64_decode($privateKey));
$certificate = json_decode($opened, true);
// ['certificate_base64' => …, 'password' => …, 'serial_number' => …, 'sha256' => …]

hash('sha256', base64_decode($certificate['certificate_base64'])) === $certificate['sha256'] || throw new RuntimeException('p12 alterado');
```

- La entrega es **de un solo uso** y el gateway borra el p12 de su bóveda. Guardar el resultado **antes**
  de confirmar nada al usuario (transacción). Si el SaaS pierde el p12 tras retirarlo, no hay forma de recuperarlo.
- **Custodia:** p12 y contraseña cifrados y en campos separados (idealmente con llaves distintas). Nunca en logs.
- REGULAR (`delivery_mode = HOLDER`): el proveedor envía el certificado por correo; `GET …/certificate` responde `409`.

---

## 10. Firmas Regulares (REGULAR)

1. Formulario según `GET /v1/products`: documentos requeridos por tipo de solicitante, `birth_date`, datos de
   la empresa para representante legal, y **video de autorización desde los 65 años** con la plantilla de
   `guides.elderly_video.template`. Reemplazar `[FECHA]`, `[NOMBRES_APELLIDOS]`, `[CEDULA]` y `[CORREO]` y
   pedir al titular que la lea en el video.
2. Crear la solicitud (sección 6). El gateway la registra en el proveedor (`session.submitted`).
3. Operadores del proveedor validan (horas o días). Los cambios llegan como `session.reconciled`.
4. Si queda en `FAILED_RETRYABLE`, revisar `error.codes`:
   - `D02` (código dactilar), `A01` (anverso), `A02` (reverso), `A03` (selfie): el SaaS puede corregir con
     `POST /v1/signature-sessions/{id}/corrections`
     `{"finger_code": "…", "documents": {"identification_front": "<base64>"}}` → vuelve a `VALIDATING`.
   - Otros códigos los corrigen los operadores del proveedor.
5. `ISSUED`: el titular recibió el correo para descargar. `DELIVERED`: ya lo descargó.

---

## 11. Notificaciones del gateway hacia el SaaS

El gateway hace `POST` a la URL de notificaciones de la credencial:

```json
{ "id": "uuid-de-la-notificación", "tenant": "external_id del afiliado", "client": "uuid de la credencial",
  "event": "certificate.ready", "occurred_at": "2026-10-23T10:20:00+00:00",
  "data": { "signature_session_id": "uuid", "status": "CERTIFICATE_STORED", "step": "ISSUANCE", "remaining_liveness_attempts": null } }
```

Cabeceras: `X-Gateway-Timestamp` (epoch s) y `X-Gateway-Signature` = HMAC-SHA256 hex con el **secreto de
notificaciones** sobre `"{timestamp}\n{cuerpo_crudo}"`.

**El endpoint del SaaS debe:**
1. Verificar la firma sobre el **cuerpo crudo**, con `hash_equals`. Si es inválida, responder `401`.
2. Rechazar timestamps con más de 5 minutos de diferencia.
3. **Deduplicar por `id`**: el gateway reintenta (hasta 8 veces, con backoff de hasta 1 hora) si no recibe un `2xx`.
4. Responder `2xx` rápido y procesar en una cola del SaaS.
5. Tratar la notificación como aviso: si hace falta, confirmar con `GET /v1/signature-sessions/{id}`.

```php
public function __invoke(Request $request)
{
    $timestamp = (string) $request->header('X-Gateway-Timestamp');
    $expected = hash_hmac('sha256', $timestamp."\n".$request->getContent(), $notificationSecret);

    abort_unless(hash_equals($expected, (string) $request->header('X-Gateway-Signature')), 401);
    abort_if(abs(time() - (int) $timestamp) > 300, 401);

    ProcessGatewayEvent::dispatch($request->json()->all());   // deduplicar por id dentro del job

    return response()->noContent();
}
```

| Evento | Cuándo |
|---|---|
| `session.submitted` | Registrada en el proveedor |
| `session.awaiting_liveness` | Registro Civil OK; esperar prueba de vida (HEADLESS) |
| `session.liveness_ready` | Credenciales de prueba de vida listas para retirar |
| `session.liveness_passed` | Biometría OK; se está emitiendo |
| `session.liveness_failed` | Biometría no coincidió; puede reintentar |
| `certificate.issued` | El proveedor emitió el certificado (cualquier producto): quedó en el registro de emisiones con su `sequence`. Una sola vez por solicitud |
| `certificate.ready` | p12 listo para retirar (HEADLESS) |
| `session.rejected` | Rechazo definitivo (Registro Civil, intentos agotados, datos) |
| `session.cancelled` | El gateway la canceló: afiliado suspendido o bloqueado, credencial revocada o emisión detenida (`reason`) |
| `session.failed` | Fallo de emisión: requiere revisión manual (soporte) |
| `session.reconciled` | Estado actualizado por la reconciliación (REGULAR sobre todo) |
| `session.needs_review` | El proveedor emitió pero el certificado no llegó al gateway: incidente |

---

## 12. Consideraciones técnicas para el SaaS

**Seguridad**
- Secretos (token, secreto de firma, secreto de notificaciones, llave privada) **cifrados** en la base del SaaS.
  Nunca en el frontend, en logs ni en mensajes de error.
- Las llamadas al gateway salen **solo desde el backend**. Respetar la red privada (WireGuard/IP permitida) en producción.
- Usar `#[\SensitiveParameter]` en funciones que reciban secretos, p12 o contraseñas.
- Rotación: el operador emite una credencial nueva, el SaaS la configura, se prueba con `/ping` y se revoca la anterior.
  Los tokens vencen (12 meses por defecto); alertar en el admin con `token_expires_at` de `/ping`.

**Datos personales (LOPDP)**
- Cédula, código dactilar, fotos, video y biometría son datos sensibles. No conservarlos en el SaaS más de lo necesario.
- Guardar la evidencia de aceptación de términos (versión, `content_sha256`, fecha, IP, user-agent).

**Robustez**
- Idempotencia: generar el `Idempotency-Key` antes de llamar y reutilizarlo en reintentos.
- Timeouts del cliente HTTP: 30 s (60 s para crear solicitudes REGULAR con video). Reintentar solo `429`, `5xx`
  y errores de red, con backoff; nunca reintentar `4xx` de validación.
- `429` en `/refresh` = cupo de consultas agotado: mostrar "intente más tarde", no reintentar en bucle.
- Máquina de estados propia en el SaaS que refleje `status`; idempotente ante notificaciones repetidas o fuera de orden.

**Experiencia del titular**
- Mostrar términos y condiciones antes de crear la solicitud (obligatorio).
- Textos guía para selfie, cédula y video (vienen en `GET /v1/products`).
- Prueba de vida: buena luz, rostro centrado, sin lentes oscuros ni gorra; advertencia de fotosensibilidad;
  botón de reintento si `FAILED_RETRYABLE`.
- Menores de edad: el gateway (REGULAR) o el Registro Civil (HEADLESS) los rechazan; validarlo también en el formulario.

---

## 13. Checklist de implementación

**Backend**
- [ ] Configuración por cliente en el admin (sección 2), cifrada, con "Probar conexión" (`/ping`)
- [ ] `GatewayClient` con firma HMAC (sección 3) y pruebas contra `/ping`
- [ ] Catálogos y productos cacheados (secciones 4 y 5)
- [ ] Crear solicitud con `Idempotency-Key` y evidencia de términos (sección 6)
- [ ] Endpoint de notificaciones con verificación de firma, deduplicación y cola (sección 11)
- [ ] HEADLESS: sesión y credenciales de prueba de vida, envío del resultado (sección 7)
- [ ] HEADLESS: retiro y apertura del certificado sellado, custodia cifrada (sección 9)
- [ ] REGULAR: carga y validación de documentos, video para 65+, correcciones (sección 10)
- [ ] Máquina de estados de la solicitud en el SaaS (sección 8)

**Frontend**
- [ ] Selector de flujo según `GET /v1/products`
- [ ] Formulario dinámico por producto y tipo de solicitante, con validaciones de formato
- [ ] Pantalla de términos y condiciones
- [ ] Pantalla de prueba de vida con `FaceLivenessDetectorCore` (HEADLESS)
- [ ] Carga de documentos y video con vista previa y textos guía (REGULAR)
- [ ] Pantalla de estado de la solicitud y de reintento o corrección

**Pruebas**
- [ ] Contra el sandbox del gateway con las personas de prueba de FirmaSegura
- [ ] Firma HMAC inválida → 401; nonce repetido → 401; notificación con firma inválida rechazada
- [ ] Reintento con el mismo `Idempotency-Key` no duplica solicitudes
