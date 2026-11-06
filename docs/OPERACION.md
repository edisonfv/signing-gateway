# Signing Gateway — Hoja de operación

Una página para el día a día. El detalle de cada paso está en [INSTALACION_SERVIDOR.md](INSTALACION_SERVIDOR.md).

> **Antes de cada comando, mira el prompt:** `root@gateway-prod` es el gateway; `root@mail` es Plesk (SaaS).
> **Nunca** pegues `.env`, llaves, tokens ni contraseñas en chats o correos.

## Mapa

| | Gateway (VM 102) | SaaS en Plesk (VM 100) |
|---|---|---|
| Red privada | `10.20.0.1` | `10.20.0.2` |
| Código | `/var/www/gateway` | `/var/www/vhosts/express.com.ec/httpdocs` |
| Colas y tareas | Servicios del sistema: arrancan solos y se relanzan si caen | Dos *Scheduled Tasks* de Plesk cada minuto |
| Revisar | `gw doctor` | Pantalla «Estado del gateway» |

## Los tres comandos del gateway

| Situación | Comando |
|---|---|
| Servidor nuevo (o repetir una instalación a medias) | `bash /root/bootstrap.sh` |
| Subir una versión nueva | `gw deploy` |
| ¿Está todo bien? | `gw doctor` |

Además: `gw status` (servicios), `gw logs` (en vivo), `gw failed` (jobs fallidos), `gw restart`, `gw tls`
(certificado), `gw env` (configuración sin secretos), `gw art <comando>` (artisan como el usuario `gateway`).

## Tareas de Plesk (SaaS)

En *Websites & Domains → express.com.ec → Scheduled Tasks*, con el usuario del dominio (no root), cada minuto.
Ajustar `8.4` a la versión de PHP del dominio:

```
cd /var/www/vhosts/express.com.ec/httpdocs && /opt/plesk/php/8.4/bin/php artisan schedule:run >> /dev/null 2>&1
cd /var/www/vhosts/express.com.ec/httpdocs && flock -n storage/framework/queue.lock /opt/plesk/php/8.4/bin/php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```

Si el dominio usa el *Scheduler* del Laravel Toolkit, no crear la primera.

## Servidor nuevo, en orden

1. Proxmox: VM con la segunda tarjeta en la VNet `gwnet`. Instalar AlmaLinux 9 (particiones en A3).
   **Quitar la ISO antes de reiniciar.**
2. IP privada: `nmcli con add type ethernet ifname enp6s19 con-name privada ipv4.method manual ipv4.addresses 10.20.0.1/24 ipv6.method disabled && nmcli con up privada && ping -c 2 10.20.0.2`
3. Desde el PC: `scp deploy/bootstrap.sh root@<IP pública>:/root/` y en el servidor `bash /root/bootstrap.sh`.
   Enter en todas las preguntas. **No interrumpir con Ctrl+C**: hay pasos que tardan minutos sin mostrar nada.
   Solo se detiene en la *deploy key* y en el respaldo de llaves (mensaje amarillo).
4. Token de FirmaSegura: `gw art gateway:provider:credentials sandbox` (o `production`). Pegar **solo** el
   token: sin `Bearer`, sin comillas.
5. Emparejar: `gw art gateway:pair` y, en la SaaS, URL `https://10.20.0.1:8443/api` + el código (10 minutos).
6. Avisos de FirmaSegura (cuando la SaaS tenga la ruta de reenvío): `GATEWAY_WEBHOOK_URL=https://express.com.ec/provider-webhooks` en `.env`, luego `gw art optimize && gw restart`.
7. `gw doctor` → «Todo en orden».

## Si algo falla

| Síntoma | Causa habitual | Qué hacer |
|---|---|---|
| SaaS: «URL del gateway no está permitida» | Falta `GATEWAY_ALLOWED_HOSTS=10.20.0.1` en el `.env` de la SaaS | Agregarla y `php artisan optimize:clear` |
| SaaS: «No se pudo conectar con el gateway» | URL sin `https`, `:8443` o `/api`; o SaaS sin el certificado | Desde Plesk: `curl -sk -o /dev/null -w '%{http_code}\n' https://10.20.0.1:8443/up` → `200` |
| SaaS: «Bearer token is malformed» | Token de FirmaSegura mal pegado | Repetir el paso 4 y `gw restart` |
| Un pedido no avanza | Cola detenida | `gw status`, `gw failed`; en Plesk, que existan las dos tareas |
| `gw doctor` con FALLO | Lo dice la columna «Detalle» | Seguir el detalle; si no es claro, `gw logs` |
| El gateway responde `403` | La llamada no sale desde `10.20.0.2` | Revisar la IP privada de Plesk |

## Reglas que no se rompen

- Las llaves (`APP_KEY`, `GATEWAY_*_KEY`) y `DB_PASSWORD` viven en el gestor de contraseñas. Sin ellas los
  datos cifrados no se recuperan. **Nunca** se regeneran en producción.
- Composer y artisan nunca como root: siempre `gw art …` o `sudo -u gateway`.
- En Plesk no se instala nada que Plesk no gestione.
- El cierre de accesos (SSH y zona pública, sección E) es el último paso antes de producción.
