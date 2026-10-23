// Signing Gateway — firma HMAC de cada petición.
//
// Pegar como "Script personalizado" en el pre-procesador a nivel de PROYECTO de Apidog
// (Configuración del proyecto → Pre/Post procesadores) para que aplique a todos los endpoints.
//
// Variables del entorno (usar "valor local" para que no se sincronicen a la nube):
//   token          Token de acceso de `php artisan gateway:client:create`
//   signingSecret  Secreto de firma de la misma credencial
//   apiBasePath    (opcional) Prefijo de la API cuando la URL base no lo incluye. Por defecto: /api
//
// Cadena firmada (una línea por elemento):
//   MÉTODO
//   /ruta?query            (query decodificada, en el orden enviado)
//   X-Timestamp            (epoch en segundos)
//   X-Nonce
//   sha256 hex del cuerpo  (cadena vacía en GET)

const CryptoJS = require('crypto-js');

const token = pm.environment.get('token');
const secret = pm.environment.get('signingSecret');

if (!token || !secret) {
    throw new Error('Configure las variables de entorno "token" y "signingSecret".');
}

const fullUrl = pm.variables.replaceIn(pm.request.url.toString()).split('#')[0];
const withoutOrigin = fullUrl.replace(/^[a-z][a-z0-9+.-]*:\/\/[^/]+/i, '');
const separator = withoutOrigin.indexOf('?');
let path = separator === -1 ? withoutOrigin : withoutOrigin.slice(0, separator);
const query = separator === -1 ? '' : decodeURIComponent(withoutOrigin.slice(separator + 1));

const apiBasePath = pm.environment.get('apiBasePath') || '/api';
if (!path.startsWith('/')) {
    path = '/' + path;
}
if (!path.startsWith(apiBasePath + '/')) {
    path = apiBasePath + path;
}

const body = pm.request.body && pm.request.body.mode === 'raw'
    ? pm.variables.replaceIn(pm.request.body.raw || '')
    : '';

const timestamp = Math.floor(Date.now() / 1000).toString();
const nonce = CryptoJS.lib.WordArray.random(16).toString();

const canonical = [
    pm.request.method.toUpperCase(),
    path + (query ? '?' + query : ''),
    timestamp,
    nonce,
    CryptoJS.SHA256(body).toString(),
].join('\n');

const signature = CryptoJS.HmacSHA256(canonical, secret).toString();

pm.request.headers.upsert({ key: 'Authorization', value: 'Bearer ' + token });
pm.request.headers.upsert({ key: 'Accept', value: 'application/json' });
pm.request.headers.upsert({ key: 'X-Timestamp', value: timestamp });
pm.request.headers.upsert({ key: 'X-Nonce', value: nonce });
pm.request.headers.upsert({ key: 'X-Signature', value: signature });
