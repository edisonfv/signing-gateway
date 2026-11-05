#!/bin/bash
# Genera y rota el certificado TLS de la API interna del Signing Gateway.
# bootstrap.sh lo instala como /usr/local/sbin/gateway-tls-rotate (root) y gateway-tls.timer lo
# ejecuta a diario.
#
# Ciclo, sin copiar archivos entre servidores:
#   1. Sin current.crt: lo crea (la plataforma lo recibe al emparejar).
#   2. 30 días antes de que venza: crea next.crt. La plataforma lo descarga en su sincronización
#      diaria de GET /v1/platform/tls y desde ese momento confía en los dos.
#   3. Cuando la plataforma ya lo descargó (huella en el archivo de acuse), cambia a next.crt y
#      recarga nginx. Si nunca lo descarga, cambia igual el último día para no quedar vencido.
#
# Uso: gateway-tls-rotate [--status | --rotate]
#   --status  muestra los certificados y si la plataforma ya tiene el siguiente
#   --rotate  crea el siguiente ahora (p. ej. al cambiar la IP privada); el cambio sigue el ciclo
set -euo pipefail

CONF=/etc/gateway/bootstrap.env
DIR=/etc/gateway/tls
ACK=/var/www/gateway/storage/app/private/tls-next-ack
VALID_DAYS=365
RENEW_DAYS=30

# shellcheck source=/dev/null
source "$CONF"

fingerprint() {
    openssl x509 -in "$1" -noout -fingerprint -sha256 | cut -d= -f2 | tr -d : | tr 'A-F' 'a-f'
}

expires_within_days() {
    ! openssl x509 -in "$1" -noout -checkend $(($2 * 86400)) >/dev/null
}

generate() {
    openssl req -x509 -nodes -days "$VALID_DAYS" -newkey ec -pkeyopt ec_paramgen_curve:prime256v1 \
        -keyout "$DIR/$1.key.tmp" -out "$DIR/$1.crt.tmp" -subj "/CN=signing-gateway" \
        -addext "subjectAltName=IP:${GATEWAY_PRIVATE_IP}" 2>/dev/null
    chmod 600 "$DIR/$1.key.tmp"
    chmod 644 "$DIR/$1.crt.tmp"
    mv "$DIR/$1.key.tmp" "$DIR/$1.key"
    mv "$DIR/$1.crt.tmp" "$DIR/$1.crt"
    restorecon -q "$DIR/$1.key" "$DIR/$1.crt" 2>/dev/null || true
}

reload_nginx() {
    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
    fi
}

next_acknowledged() {
    [ -f "$ACK" ] && [ "$(tr -d '[:space:]' <"$ACK")" = "$(fingerprint "$DIR/next.crt")" ]
}

install -d -m 755 "$DIR"

case "${1:-}" in
    --status)
        for name in current next; do
            if [ -f "$DIR/$name.crt" ]; then
                echo "$name: vence $(openssl x509 -in "$DIR/$name.crt" -noout -enddate | cut -d= -f2) · $(fingerprint "$DIR/$name.crt")"
            else
                echo "$name: no existe"
            fi
        done
        if [ -f "$DIR/next.crt" ]; then
            next_acknowledged && echo "La plataforma ya descargó el siguiente." || echo "La plataforma aún no descarga el siguiente (GET /v1/platform/tls)."
        fi
        exit 0
        ;;
    --rotate)
        generate next
        echo "Siguiente creado. Se usará cuando la plataforma lo descargue de GET /v1/platform/tls."
        exit 0
        ;;
esac

if [ ! -f "$DIR/current.crt" ]; then
    generate current
    reload_nginx
    echo "Certificado creado (vence en $VALID_DAYS días)."
    exit 0
fi

if [ ! -f "$DIR/next.crt" ] && expires_within_days "$DIR/current.crt" "$RENEW_DAYS"; then
    generate next
    echo "Siguiente creado: la plataforma lo descargará en su sincronización diaria."
fi

if [ -f "$DIR/next.crt" ] && { next_acknowledged || expires_within_days "$DIR/current.crt" 1; }; then
    mv "$DIR/next.key" "$DIR/current.key"
    mv "$DIR/next.crt" "$DIR/current.crt"
    rm -f "$ACK"
    reload_nginx
    echo "Cambiado al certificado siguiente."
fi
