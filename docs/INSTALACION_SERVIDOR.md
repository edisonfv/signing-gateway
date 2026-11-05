# Signing Gateway — Instalación del servidor

Montar un gateway son tres partes: **(A) prerrequisitos** (red privada y sistema operativo), **(B) un
script** (`deploy/bootstrap.sh`) y **(C) emparejar** con la plataforma. Fecha: 2026-11-05.

> **Nunca** pegues el `.env`, las llaves ni tokens en chats, tickets o correos. Para revisar la
> configuración sin secretos: `gw env`.

---

## 0. Datos de la instalación

| Elemento | Valor |
|---|---|
| Host Proxmox | `ns1010631` (OVH) |
| VM Plesk (SaaS) | **100** — pública `135.148.59.194`, privada `10.20.0.2`, interfaz privada `enp6s19` |
| VM Gateway | **102** — AlmaLinux 9, 2 vCPU / 4 GB / 100 GB, UEFI; privada `10.20.0.1` |
| Red privada | VNet `gwnet` (zona SDN `privgw`), `10.20.0.0/24`; solo VMs 100 y 102 |
| API del gateway | `https://10.20.0.1:8443` (solo para `10.20.0.2`) |
| Notificaciones | `https://express.com.ec/webhooks/signing-gateway` (dominio público del SaaS, sin redirección) |
| DNS de `express.com.ec` | Plesk (`ns1`/`ns2` → `135.148.59.194`) |

| Flujo | Camino | Protección |
|---|---|---|
| SaaS → gateway | Red privada → `https://10.20.0.1:8443` | Firewall (solo `10.20.0.2`), `GATEWAY_ALLOWED_SOURCES`, TLS con certificado entregado al emparejar, token + HMAC |
| Gateway → SaaS | HTTPS público → `express.com.ec` | Let's Encrypt de Plesk, `GATEWAY_NOTIFICATION_HOSTS`, firma `X-Gateway-Signature` |
| FirmaSegura → SaaS → gateway | `/provider-webhooks` (público) → red privada | La plataforma reenvía sin interpretar |
| Gateway → FirmaSegura | Salida desde la IP pública del gateway | IP registrada en FirmaSegura |

---

## A. Prerrequisitos (una vez, a mano)

### A1. Red privada en Proxmox (VNet)

No usar `enp5s0f1` (segunda tarjeta física; en OVH suele ser la del vRack). La VNet no lleva puerto físico.

1. **Datacenter → SDN → Zones → Add → Simple**: ID `privgw`, Nodes `ns1010631`, IPAM `pve`, Automatic DHCP **sin marcar**.
2. **Datacenter → SDN → VNets → Create**: Name `gwnet`, Zone `privgw`, **Isolate Ports sin marcar**, VLAN aware sin marcar. **Sin subnet.**
3. **Datacenter → SDN → Apply**.
4. **Conectar las VMs.** En **VM 102** y **VM 100**: **Hardware → Add → Network Device**
   - Bridge: `gwnet`
   - Model: VirtIO
   - Firewall: ✓

Si un día las VMs están en servidores distintos, la red privada se hace con vRack (o un túnel): el resto
de esta guía no cambia, solo las IPs.

### A2. IP privada en Plesk (VM 100)

Solo configuración de red del sistema operativo; nada que Plesk no gestione. La interfaz de `gwnet` es
`enp6s19` (la pública, `enp6s18`, no se toca):

```bash
nmcli con add type ethernet ifname enp6s19 con-name privada ipv4.method manual ipv4.addresses 10.20.0.2/24 ipv6.method disabled
nmcli con up privada
```

Si `enp6s19` ya tenía una conexión automática (`nmcli -f NAME,DEVICE con show`), borrarla antes con
`nmcli con delete "<nombre>"`. Luego en Plesk: **Tools & Settings → IP Addresses → Reread IP**, sin asignar
`10.20.0.2` a ningún dominio. En Plesk **no** se crea ninguna zona de firewall.

### A3. AlmaLinux 9 en el gateway (VM 102)

Instalador: **Installation Destination** → el disco → **Custom** → **Done** → esquema **LVM**:

| Mount point | Tamaño | Para qué |
|---|---|---|
| `/boot/efi` (UEFI) o `biosboot` (SeaBIOS) | 600 MiB / 1 MiB | Arranque |
| `/boot` | 1 GiB | Kernel |
| `/` | 25 GiB | Sistema |
| `/var` | 12 GiB | Caché de dnf y servicios |
| `/var/log` | 8 GiB | Logs |
| `/var/lib/pgsql` | 20 GiB | PostgreSQL (**obligatoria**: el script la exige) |
| `/var/www` | 10 GiB | El gateway |
| `/tmp` | 4 GiB | Subidas temporales |
| `swap` | 4 GiB | Respaldo de la RAM |

- Volume Group → **Modify** → Size policy **As large as possible** (deja ~16 GiB libres para crecer). Si
  quedó espacio fuera de LVM: `dnf -y install cloud-utils-growpart && growpart /dev/sda 3 && pvresize /dev/sda3`.
- Con UEFI, en Proxmox la VM debe tener **EFI Disk** (Hardware).
- Software: **Minimal Install** · KDUMP: desactivado · Zona horaria: **UTC** con NTP · Usuario administrador.
- Red: la IP pública fija (la que se registra en FirmaSegura) y hostname `gateway-prod`.

Después de instalar, la IP privada en la interfaz de `gwnet` (la que `nmcli device status` muestra
`disconnected`; suele ser `enp6s19`):

```bash
nmcli con add type ethernet ifname enp6s19 con-name privada ipv4.method manual ipv4.addresses 10.20.0.1/24 ipv6.method disabled
nmcli con up privada
ping -c 2 10.20.0.2
```

✅ El prerrequisito está cumplido cuando el gateway responde el ping a la plataforma y viceversa.

---

## B. Instalación con el script

### B1. Llevar el script al servidor

El repositorio es privado: copiarlo desde el PC (con el repositorio clonado) por SSH a la IP pública del gateway:

```powershell
scp deploy/bootstrap.sh root@<IP pública del gateway>:/root/
```

### B2. Ejecutarlo

```bash
bash /root/bootstrap.sh
```

Pregunta (con valores por defecto; quedan guardados en `/etc/gateway/bootstrap.env`):

| Pregunta | Valor |
|---|---|
| IP privada de este gateway | `10.20.0.1` |
| IP privada de la plataforma | `10.20.0.2` |
| Dominio público del SaaS | `express.com.ec` |
| Ambiente de FirmaSegura | `sandbox` (o `production`) |
| Repositorio / rama | `git@github.com:edisonfv/signing-gateway.git` / `main` |

Se detiene solo donde hace falta una persona:

1. **Llave de despliegue:** muestra una línea `ssh-ed25519 …` → registrarla en GitHub (**Settings → Deploy
   keys → Add**, sin acceso de escritura) y pulsar Enter.
2. **Llaves de la bóveda:** responde `yes` y muestra 5 líneas (`APP_KEY`, `GATEWAY_*_KEY`, `DB_PASSWORD`)
   → copiarlas al **gestor de contraseñas** y pulsar Enter (la pantalla se limpia).
3. **Token de FirmaSegura** del ambiente elegido.

Lo que hace, en orden: comprueba el prerrequisito (AlmaLinux 9, partición `/var/lib/pgsql`, IP privada,
plataforma alcanzable) → sistema base y actualizaciones automáticas de seguridad → PHP 8.4 (Remi) y
Composer → PostgreSQL 17 en su partición → usuario `gateway`, llave de despliegue y código → base de datos
con clave generada → `.env` → certificado TLS → Nginx y PHP-FPM → SELinux (enforcing) → firewall (zona
`privada` = IP de la plataforma) → `gateway:install` → workers, scheduler, renovación del certificado y `gw`
→ `gateway:doctor`.

**Es idempotente:** si se corta o algo falla, se corrige y se vuelve a ejecutar; no repite lo hecho ni
reemplaza llaves, base o certificado.

### B3. Comprobar

```bash
gw status     # todo «active»
gw doctor     # avisos esperables: Plataforma (sin emparejar) y GATEWAY_WEBHOOK_URL
```

Desde Plesk (`root@mail`): `curl -sk https://10.20.0.1:8443/up` → `200`.

---

## C. Emparejar con la plataforma

1. Requisitos del lado del SaaS: `docs/INSTRUCCIONES_PLATAFORMA.md` (puntos 0, 1 y 1.1).
2. En el gateway: `gw art gateway:pair` → código de 8 caracteres (vence en 10 minutos).
3. En el SaaS, ficha del proveedor: URL `https://10.20.0.1:8443` + el código → **Emparejar**. El SaaS
   recibe los certificados en los que confiar: **no se copia ningún archivo**.
4. `gw doctor` → «Plataforma: emparejada».
5. Cuando el SaaS reenvíe los avisos del proveedor: en `.env`,
   `GATEWAY_WEBHOOK_URL=https://express.com.ec/provider-webhooks` y `gw art optimize`.

### Compra de prueba (sandbox)

Comprar una firma desde el SaaS con las personas de prueba de FirmaSegura y seguirla con `gw logs` y
`gw doctor` hasta `COMPLETED`, con la notificación recibida en el SaaS. Repetir los casos que FirmaSegura
pide para certificar (ARQUITECTURA.md §14.1).

---

## D. Paso a producción

1. **Certificación con FirmaSegura:** prueba funcional, enviarles la IP pública del gateway
   (`curl -s https://ifconfig.me`) y recibir el **token de producción** (al gestor de contraseñas).
2. **Bases limpias** (sin datos de prueba ni emisiones de sandbox en el registro de emisiones):
   ```bash
   systemctl stop gateway-worker-interactive gateway-worker-general gateway-scheduler
   sudo -u postgres psql -c "DROP DATABASE signing_gateway;" -c "CREATE DATABASE signing_gateway OWNER gateway;"
   ```
   En el SaaS, borrar solo lo de prueba del gateway (afiliados, solicitudes y credenciales de prueba),
   con respaldo previo.
3. **Producción:** `bash /root/bootstrap.sh` → «¿Usarla?» **n** → ambiente `production`. Conserva las
   llaves, pide el token de producción, migra y deja `GATEWAY_STRICT_IDENTIFICATION=true`.
4. Emparejar de nuevo (parte C) y volver a conectar a los afiliados.
5. Primera firma real de bajo riesgo; revisar `gw doctor`.

## E. Cierre de accesos (último paso)

- **SSH solo desde Plesk por la red privada** (la IP de administración es dinámica). En el PC,
  `~/.ssh/config` (la llave pública del PC en `/root/.ssh/authorized_keys` de Plesk y del gateway):
  ```
  Host plesk
      HostName 135.148.59.194
      User root

  Host gateway
      HostName 10.20.0.1
      User root
      ProxyJump plesk
  ```
  Cuando `ssh gateway` funcione, cerrar la zona pública (con la consola de Proxmox abierta):
  ```bash
  firewall-cmd --permanent --zone=public --remove-service=ssh --remove-service=cockpit --remove-service=dhcpv6-client
  firewall-cmd --reload
  systemctl disable --now cockpit.socket
  ```
  Emergencia: consola de Proxmox (VM 102 → Console).
- Panel de Proxmox (8006) restringido y con 2FA.
- **Respaldo programado** de las VMs 100 y 102 (Datacenter → Backup), guardado **fuera del servidor
  físico** y cifrado (incluye el `.env`). Probar una restauración.
- Llaves, clave de la base y token de producción solo en el gestor de contraseñas.
- Monitor del gateway en la plataforma con alertas por correo (INSTRUCCIONES_PLATAFORMA.md §8).
- Opcional: salida restringida a FirmaSegura, `express.com.ec`, GitHub, DNS, NTP y repositorios.

---

## Administración diaria: `gw`

| Comando | Hace |
|---|---|
| `gw status` | Estado de PostgreSQL, PHP-FPM, Nginx, workers, scheduler y timer del certificado |
| `gw doctor` | `gateway:doctor` |
| `gw logs` | Log del día en vivo (Ctrl+C para salir) |
| `gw art <comando>` | `php artisan <comando>` como el usuario `gateway` (p. ej. `gw art gateway:pair`) |
| `gw restart` | Reinicia workers y scheduler, recarga PHP-FPM |
| `gw failed` | Jobs fallidos |
| `gw tls` | Certificados (vigente y siguiente) y si la plataforma ya descargó el siguiente |
| `gw deploy` | Publica la última versión de la rama (`git pull`, Composer, migraciones, caché, workers) y corre `gateway:doctor` |
| `gw env` | El `.env` **sin** llaves ni contraseñas |

Cambios en la infraestructura (`deploy/`: nginx, servicios, script del certificado): `bash /root/bootstrap.sh`
de nuevo (conviene copiar antes la versión nueva del script).

## Certificado de la API

Lo administra el gateway solo (`/etc/gateway/tls`, timer `gateway-tls` diario): 30 días antes de vencer
crea el siguiente, la plataforma lo descarga en su sincronización diaria y recién entonces el gateway
cambia a él. Si algo falla, el monitor de la plataforma y `gw doctor` lo muestran («Certificado TLS de la
API»). Si cambia la IP privada: actualizar `/etc/gateway/bootstrap.env` y `gateway-tls-rotate --rotate`.
