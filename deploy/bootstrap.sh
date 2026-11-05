#!/bin/bash
# Puesta en marcha del Signing Gateway en AlmaLinux 9. Idempotente: si se corta, se vuelve a
# ejecutar y continúa sin repetir ni romper lo hecho (nunca reemplaza llaves ni la base).
#
# Prerrequisito (no lo hace este script): red privada entre el gateway y la plataforma —VNet de
# Proxmox, vRack o túnel—, con la IP privada del gateway ya configurada y la plataforma alcanzable.
#
# Uso, como root:   bash bootstrap.sh
# La configuración queda en /etc/gateway/bootstrap.env y se reutiliza al volver a ejecutarlo.
set -euo pipefail

APP=/var/www/gateway
APP_USER=gateway
CONF_DIR=/etc/gateway
CONF=$CONF_DIR/bootstrap.env
TLS_DIR=$CONF_DIR/tls
ENV_FILE=$APP/.env
PG_BIN=/usr/pgsql-17/bin
PG_DATA=/var/lib/pgsql/17/data
GITHUB_HOST_KEY="github.com ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIOMqqnkVzrm0SdG6UOoqKLsabgH5C9okWi0dh2l9GKJl"

step() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
info() { printf '    %s\n' "$*"; }
fail() { printf '\n\033[1;31mERROR: %s\033[0m\n' "$*" >&2; exit 1; }
pause() { printf '\n\033[1;33m%s\033[0m\n' "$*"; read -rp "    Pulse Enter para continuar... " _; }
as_app() { sudo -u "$APP_USER" -H "$@"; }
artisan() { (cd "$APP" && as_app php artisan "$@"); }
valid_ip() { [[ $1 =~ ^([0-9]{1,3}\.){3}[0-9]{1,3}$ ]]; }

ask() {
    local answer
    read -rp "    $2 [${!1:-$3}]: " answer
    printf -v "$1" '%s' "${answer:-${!1:-$3}}"
}

set_env() {
    if grep -q "^$1=" "$ENV_FILE"; then
        sed -i "s#^$1=.*#$1=$2#" "$ENV_FILE"
    else
        echo "$1=$2" >>"$ENV_FILE"
    fi
}

fcontext() {
    semanage fcontext -a -t "$1" "$2" 2>/dev/null || semanage fcontext -m -t "$1" "$2"
}

# --- 1. Configuración ---------------------------------------------------------------------------
configure() {
    step "Configuración"
    install -d -m 755 "$CONF_DIR"

    if [ -f "$CONF" ]; then
        # shellcheck source=/dev/null
        source "$CONF"
        info "Configuración guardada:"
        sed 's/^/      /' "$CONF"
        read -rp "    ¿Usarla? (S/n): " keep
        [[ ${keep:-s} =~ ^[sS]$ ]] && return
    fi

    ask GATEWAY_PRIVATE_IP "IP privada de ESTE gateway" "10.20.0.1"
    ask PLATFORM_PRIVATE_IP "IP privada de la plataforma (Plesk)" "10.20.0.2"
    ask NOTIFICATION_HOST "Dominio público del SaaS (notificaciones)" "express.com.ec"
    ask PROVIDER_ENVIRONMENT "Ambiente de FirmaSegura (sandbox|production)" "sandbox"
    ask REPO_URL "Repositorio" "git@github.com:edisonfv/signing-gateway.git"
    ask BRANCH "Rama" "main"

    valid_ip "$GATEWAY_PRIVATE_IP" || fail "IP privada del gateway inválida: $GATEWAY_PRIVATE_IP"
    valid_ip "$PLATFORM_PRIVATE_IP" || fail "IP privada de la plataforma inválida: $PLATFORM_PRIVATE_IP"
    [[ $PROVIDER_ENVIRONMENT =~ ^(sandbox|production)$ ]] || fail "Ambiente inválido: $PROVIDER_ENVIRONMENT"

    cat >"$CONF" <<EOF
GATEWAY_PRIVATE_IP=$GATEWAY_PRIVATE_IP
PLATFORM_PRIVATE_IP=$PLATFORM_PRIVATE_IP
NOTIFICATION_HOST=$NOTIFICATION_HOST
PROVIDER_ENVIRONMENT=$PROVIDER_ENVIRONMENT
REPO_URL=$REPO_URL
BRANCH=$BRANCH
EOF
    chmod 600 "$CONF"
}

# --- 2. Comprobaciones previas -----------------------------------------------------------------
prechecks() {
    step "Comprobaciones previas"
    # shellcheck source=/dev/null
    source /etc/os-release
    [[ ${VERSION_ID%%.*} == 9 && ($ID == almalinux || ${ID_LIKE:-} == *rhel*) ]] || fail "Se requiere AlmaLinux 9 (es $PRETTY_NAME)."
    info "Sistema: $PRETTY_NAME"

    mountpoint -q /var/lib/pgsql || fail "/var/lib/pgsql no es una partición propia. Créela al instalar el sistema (ver docs/INSTALACION_SERVIDOR.md)."
    info "Partición de la base: /var/lib/pgsql"

    [[ $(ip -4 -o addr show) == *" ${GATEWAY_PRIVATE_IP}/"* ]] || fail "La IP privada $GATEWAY_PRIVATE_IP no está en ninguna interfaz. Prerrequisito de red: configúrela antes (nmcli)."
    info "IP privada: $GATEWAY_PRIVATE_IP"

    ping -c 2 -W 2 "$PLATFORM_PRIVATE_IP" >/dev/null || fail "La plataforma ($PLATFORM_PRIVATE_IP) no responde por la red privada. Revise la red y la IP privada de Plesk."
    info "La plataforma responde: $PLATFORM_PRIVATE_IP"
}

# --- 3. Paquetes ------------------------------------------------------------------------------------
install_packages() {
    step "Sistema base"
    dnf -y -q install dnf-plugins-core epel-release
    dnf config-manager --set-enabled crb
    dnf -y -q update
    dnf -y -q install git unzip tar chrony dnf-automatic fail2ban firewalld policycoreutils-python-utils openssl bind-utils
    systemctl enable --now chronyd fail2ban firewalld
    sed -i 's/^upgrade_type.*/upgrade_type = security/; s/^apply_updates.*/apply_updates = yes/' /etc/dnf/automatic.conf
    systemctl enable --now dnf-automatic.timer

    step "PHP 8.4 (Remi) y Composer"
    rpm -q remi-release >/dev/null || dnf -y -q install https://rpms.remirepo.net/enterprise/remi-release-9.rpm
    if [[ $(dnf -q module list --enabled php 2>/dev/null) != *remi-8.4* ]]; then
        dnf -y -q module reset php
        dnf -y -q module enable php:remi-8.4
    fi
    dnf -y -q install php-fpm php-cli php-pgsql php-mbstring php-xml php-intl php-bcmath php-zip php-sodium php-opcache php-process composer
    info "$(php -r 'echo "PHP ".PHP_VERSION;') · $(composer --version 2>/dev/null | head -1)"

    step "PostgreSQL 17"
    rpm -q pgdg-redhat-repo >/dev/null || dnf -y -q install https://download.postgresql.org/pub/repos/yum/reporpms/EL-9-x86_64/pgdg-redhat-repo-latest.noarch.rpm
    dnf -y -q module disable postgresql
    dnf -y -q install postgresql17-server
    chown postgres:postgres /var/lib/pgsql
    chmod 700 /var/lib/pgsql
    restorecon -R /var/lib/pgsql
    [ -f "$PG_DATA/PG_VERSION" ] || "$PG_BIN/postgresql-17-setup" initdb
    sed -i -E 's/^(host\s+all\s+all\s+(127\.0\.0\.1\/32|::1\/128)\s+)ident/\1scram-sha-256/' "$PG_DATA/pg_hba.conf"
    systemctl enable --now postgresql-17
    if [ "$(sudo -u postgres psql -tAc 'show shared_buffers')" != "1GB" ]; then
        sudo -u postgres psql -qc "ALTER SYSTEM SET shared_buffers = '1GB';"
        systemctl restart postgresql-17
    fi
    info "PostgreSQL activo en la partición /var/lib/pgsql"

    step "Nginx"
    if [[ $(dnf -q module list --enabled nginx 2>/dev/null) != *1.24* ]]; then
        dnf -y -q module enable nginx:1.24
    fi
    dnf -y -q install nginx
}

# --- 4. Base de datos -------------------------------------------------------------------------------
setup_database() {
    step "Base de datos"
    DB_PASSWORD=""
    if [ -f "$ENV_FILE" ]; then
        DB_PASSWORD=$(grep '^DB_PASSWORD=' "$ENV_FILE" | cut -d= -f2- || true)
    fi
    # Siempre se fija la clave del .env en el rol: así nunca difieren. Si no hay una válida, se genera.
    if [[ ! $DB_PASSWORD =~ ^[A-Za-z0-9]{16,}$ ]]; then
        DB_PASSWORD=$(openssl rand -hex 24)
    fi

    if [ "$(sudo -u postgres psql -tAc "select 1 from pg_roles where rolname = 'gateway'")" = 1 ]; then
        sudo -u postgres psql -qc "ALTER ROLE gateway LOGIN PASSWORD '$DB_PASSWORD';"
    else
        sudo -u postgres psql -qc "CREATE ROLE gateway LOGIN PASSWORD '$DB_PASSWORD';"
    fi
    if [ "$(sudo -u postgres psql -tAc "select 1 from pg_database where datname = 'signing_gateway'")" != 1 ]; then
        sudo -u postgres psql -qc "CREATE DATABASE signing_gateway OWNER gateway;"
    fi
    info "Rol «gateway» y base «signing_gateway» listos"
}

# --- 5. Código --------------------------------------------------------------------------------------
setup_code() {
    step "Usuario de la aplicación y llave de despliegue"
    id "$APP_USER" >/dev/null 2>&1 || useradd -m -s /bin/bash "$APP_USER"
    install -d -o "$APP_USER" -g "$APP_USER" -m 755 "$APP"
    install -d -o "$APP_USER" -g "$APP_USER" -m 700 "/home/$APP_USER/.ssh"
    [ -f "/home/$APP_USER/.ssh/id_ed25519" ] || as_app ssh-keygen -q -t ed25519 -N '' -C "gateway-deploy@$(hostname)" -f "/home/$APP_USER/.ssh/id_ed25519"
    grep -qF "$GITHUB_HOST_KEY" "/home/$APP_USER/.ssh/known_hosts" 2>/dev/null || echo "$GITHUB_HOST_KEY" | as_app tee -a "/home/$APP_USER/.ssh/known_hosts" >/dev/null

    local output
    while true; do
        output=$(as_app ssh -o BatchMode=yes -T git@github.com 2>&1 || true)
        [[ $output == *"successfully authenticated"* ]] && break
        info "GitHub aún no acepta la llave de este servidor. Regístrela en:"
        info "GitHub → $REPO_URL → Settings → Deploy keys → Add deploy key (sin acceso de escritura):"
        echo
        cat "/home/$APP_USER/.ssh/id_ed25519.pub"
        pause "Cuando esté registrada:"
    done
    info "GitHub acepta la llave de despliegue"

    step "Código ($BRANCH)"
    if [ -d "$APP/.git" ]; then
        as_app git -C "$APP" fetch -q origin
        as_app git -C "$APP" checkout -q "$BRANCH"
        as_app git -C "$APP" pull -q --ff-only origin "$BRANCH"
    else
        [ -z "$(ls -A "$APP")" ] || fail "$APP no está vacía y no es un repositorio. Revísela antes de continuar."
        as_app git clone -q -b "$BRANCH" "$REPO_URL" "$APP"
    fi
    (cd "$APP" && as_app composer install -q --no-dev --optimize-autoloader --no-interaction)
    install -m 644 "$APP/deploy/php-fpm/99-gateway.ini" /etc/php.d/99-gateway.ini
    info "Versión: $(as_app git -C "$APP" log -1 --format='%h %s')"
}

# --- 6. .env ---------------------------------------------------------------------------------------
setup_env() {
    step "Configuración de la aplicación (.env)"
    if [ ! -f "$ENV_FILE" ]; then
        as_app tee "$ENV_FILE" >/dev/null <<'EOF'
APP_NAME="FirmaExpress Gateway"
APP_KEY=
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_DAILY_DAYS=30
LOG_LEVEL=warning
LOG_DEPRECATIONS_CHANNEL=null

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=signing_gateway
DB_USERNAME=gateway

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
MAIL_MAILER=log

GATEWAY_VAULT_KEY=
GATEWAY_VAULT_PREVIOUS_KEYS=
GATEWAY_VAULT_PASSWORD_KEY=
GATEWAY_VAULT_PASSWORD_PREVIOUS_KEYS=
GATEWAY_BLIND_INDEX_KEY=

GATEWAY_DEFAULT_PROVIDER=firmasegura
GATEWAY_DEFAULT_PRODUCTS=HEADLESS
GATEWAY_SIGNATURE_TOLERANCE=300
GATEWAY_CLIENT_TOKEN_TTL_DAYS=365
GATEWAY_TENANT_DAILY_CAP=50
GATEWAY_TENANT_HOURLY_CAP=10
GATEWAY_GLOBAL_HOURLY_CAP=200
GATEWAY_PROVIDER_STATUS_HOURLY=900
GATEWAY_APPLICANT_DATA_DAYS=90
GATEWAY_NOTIFICATION_CA=
# Ruta pública de reenvío de avisos en la plataforma, p. ej. https://express.com.ec/provider-webhooks
GATEWAY_WEBHOOK_URL=
EOF
    fi
    chmod 600 "$ENV_FILE"

    # Valores que dependen de esta instalación: se reescriben en cada ejecución.
    set_env APP_ENV production
    set_env APP_DEBUG false
    set_env APP_URL "https://${GATEWAY_PRIVATE_IP}:8443"
    set_env DB_PASSWORD "$DB_PASSWORD"
    set_env QUEUE_CONNECTION database
    set_env CACHE_STORE database
    set_env GATEWAY_PROVIDER_ENVIRONMENT "$PROVIDER_ENVIRONMENT"
    set_env GATEWAY_STRICT_IDENTIFICATION "$([ "$PROVIDER_ENVIRONMENT" = production ] && echo true || echo false)"
    set_env GATEWAY_ALLOWED_SOURCES "$PLATFORM_PRIVATE_IP"
    set_env GATEWAY_NOTIFICATION_HOSTS "$NOTIFICATION_HOST"
    set_env GATEWAY_TLS_PATH "$TLS_DIR"
    info "APP_URL=https://${GATEWAY_PRIVATE_IP}:8443 · proveedor $PROVIDER_ENVIRONMENT · plataforma $PLATFORM_PRIVATE_IP"
}

# --- 7. Certificado, nginx, PHP-FPM y SELinux -------------------------------------------------
setup_web() {
    step "Certificado TLS de la API"
    install -m 755 "$APP/deploy/tls-rotate.sh" /usr/local/sbin/gateway-tls-rotate
    install -d -m 755 "$TLS_DIR"
    fcontext cert_t "$TLS_DIR(/.*)?"
    restorecon -R "$TLS_DIR"
    /usr/local/sbin/gateway-tls-rotate
    info "$(/usr/local/sbin/gateway-tls-rotate --status | head -1)"

    step "Nginx y PHP-FPM"
    install -m 644 "$APP/deploy/nginx/gateway.conf" /etc/nginx/conf.d/gateway.conf
    install -m 644 "$APP/deploy/php-fpm/gateway.conf" /etc/php-fpm.d/gateway.conf
    [ ! -f /etc/php-fpm.d/www.conf ] || mv /etc/php-fpm.d/www.conf /etc/php-fpm.d/www.conf.off
    usermod -aG "$APP_USER" nginx
    nginx -t -q
    php-fpm -t 2>/dev/null

    step "SELinux"
    setsebool -P httpd_can_network_connect=1 httpd_can_network_connect_db=1
    fcontext httpd_sys_rw_content_t "$APP/(storage|bootstrap/cache)(/.*)?"
    restorecon -R "$APP"
    [[ $(semanage port -l | grep -E '^http_port_t\s') == *8443* ]] || semanage port -a -t http_port_t -p tcp 8443

    systemctl enable -q --now php-fpm nginx
    systemctl restart php-fpm
    systemctl reload nginx
    info "API: $(curl -sk -o /dev/null -w '%{http_code}' https://127.0.0.1:8443/up) en https://127.0.0.1:8443/up"
}

# --- 8. Firewall ------------------------------------------------------------------------------------
setup_firewall() {
    step "Firewall: la plataforma por la red privada"
    # La zona se asocia a la IP de la plataforma, no a una interfaz: vale para VNet, vRack o túnel.
    [[ " $(firewall-cmd --permanent --get-zones) " == *" privada "* ]] || firewall-cmd -q --permanent --new-zone=privada
    firewall-cmd -q --permanent --zone=privada --add-source="$PLATFORM_PRIVATE_IP"
    firewall-cmd -q --permanent --zone=privada --add-port=8443/tcp
    firewall-cmd -q --permanent --zone=privada --add-service=ssh
    firewall-cmd -q --reload
    info "Zona «privada»: $(firewall-cmd --zone=privada --list-sources) → 8443/tcp y ssh"
    info "La zona pública no se toca aquí: se cierra al pasar a producción (docs/INSTALACION_SERVIDOR.md)."
}

# --- 9. Instalación de la aplicación ------------------------------------------------------------
install_app() {
    step "Instalación del gateway"
    artisan config:clear -q

    if ! grep -qE '^GATEWAY_VAULT_KEY=.+' "$ENV_FILE"; then
        info "Se generan APP_KEY y las llaves de la bóveda (responda «yes»)."
        artisan gateway:install --no-pair
        echo
        grep -E '^(APP_KEY|GATEWAY_VAULT_KEY|GATEWAY_VAULT_PASSWORD_KEY|GATEWAY_BLIND_INDEX_KEY|DB_PASSWORD)=' "$ENV_FILE" | sed 's/^/      /'
        pause "Copie estas 5 líneas al gestor de contraseñas (NO a chats ni correos). Sin ellas los datos no se recuperan."
        clear
    fi

    info "Migraciones, catálogos, token de FirmaSegura ($PROVIDER_ENVIRONMENT) y verificación."
    artisan gateway:install --no-pair || fail "gateway:install no terminó bien. Corrija lo indicado y vuelva a ejecutar bootstrap.sh."
}

# --- 10. Servicios ----------------------------------------------------------------------------------
setup_services() {
    step "Workers, scheduler y renovación del certificado"
    install -m 644 "$APP"/deploy/systemd/*.service "$APP"/deploy/systemd/*.timer /etc/systemd/system/
    install -m 750 "$APP/deploy/gw" /usr/local/bin/gw
    systemctl daemon-reload
    systemctl enable -q --now gateway-worker-interactive gateway-worker-general gateway-scheduler gateway-tls.timer
    artisan queue:restart -q
    systemctl restart gateway-worker-interactive gateway-worker-general gateway-scheduler
    artisan optimize -q
    gw status
}

summary() {
    step "Listo"
    info "Esperando 70 s a que el scheduler y los workers den señal de vida..."
    sleep 70
    artisan gateway:doctor || true
    cat <<EOF

    Siguientes pasos:
      1. FirmaSegura debe tener autorizada la IP pública de este servidor: $(curl -s --max-time 5 https://ifconfig.me || echo '(no se pudo consultar)')
      2. Emparejar: ejecute «gw art gateway:pair» e ingrese el código en el SaaS antes de 10 minutos.
         URL del gateway para el SaaS: https://${GATEWAY_PRIVATE_IP}:8443 (el certificado viaja con el emparejamiento).
      3. Administración diaria: «gw» (gw status, gw doctor, gw logs, gw deploy, gw tls).
EOF
}

[ "$(id -u)" = 0 ] || fail "Ejecute como root: sudo bash bootstrap.sh"
cd /

configure
prechecks
install_packages
setup_code
setup_database
setup_env
setup_web
setup_firewall
install_app
setup_services
summary
