#!/usr/bin/env bash
#
# ngrok.sh — Start ngrok for WalletWise and auto-configure the app URL.
#
# Usage:
#   ./ngrok.sh                # Start ngrok on port 8000 and update .env
#   ./ngrok.sh 3000           # Custom port
#   ./ngrok.sh --stop        # Kill the running ngrok process
#   ./ngrok.sh --restore     # Restore APP_URL to http://localhost in .env
#
# Requirements:
#   - ngrok installed and authenticated (ngrok config add-authtoken <token>)
#   - Laravel app running via: php artisan serve --host=127.0.0.1 --port=<port>
#     (the script can also start it for you if not running)
#

set -euo pipefail

# ----- Configuration ---------------------------------------------------------
PORT="${1:-8000}"
ENV_FILE=".env"
NGROK_API="http://127.0.0.1:4040/api/tunnels"
NGROK_BIN="$(command -v ngrok || true)"

# ----- Helpers ---------------------------------------------------------------
print_banner() {
    echo ""
    echo "============================================================"
    echo "  WalletWise + ngrok"
    echo "============================================================"
    echo ""
}

require_ngrok() {
    if [[ -z "$NGROK_BIN" ]]; then
        echo "[!] ngrok no está instalado."
        echo "    Instálalo desde https://ngrok.com/download"
        echo "    Luego autentica: ngrok config add-authtoken <token>"
        exit 1
    fi
}

update_env() {
    local url="$1"
    if [[ ! -f "$ENV_FILE" ]]; then
        echo "[!] No se encontró $ENV_FILE. Ejecuta este script desde la raíz del proyecto Laravel."
        exit 1
    fi

    # Backup once
    [[ -f "${ENV_FILE}.bak" ]] || cp "$ENV_FILE" "${ENV_FILE}.bak"

    # Replace or append APP_URL
    if grep -qE "^APP_URL=" "$ENV_FILE"; then
        sed -i "s|^APP_URL=.*|APP_URL=${url}|" "$ENV_FILE"
    else
        echo "APP_URL=${url}" >> "$ENV_FILE"
    fi

    # Add SESSION_DOMAIN and SANCTUM_STATEFUL_DOMAINS only if missing
    if ! grep -qE "^SESSION_SECURE_COOKIE=" "$ENV_FILE"; then
        echo "SESSION_SECURE_COOKIE=true" >> "$ENV_FILE"
    else
        sed -i "s|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=true|" "$ENV_FILE"
    fi

    if ! grep -qE "^SESSION_SAME_SITE=" "$ENV_FILE"; then
        echo "SESSION_SAME_SITE=none" >> "$ENV_FILE"
    else
        sed -i "s|^SESSION_SAME_SITE=.*|SESSION_SAME_SITE=none|" "$ENV_FILE"
    fi

    echo "[OK] .env actualizado con APP_URL=${url}"
}

clear_cache() {
    php artisan config:clear   >/dev/null 2>&1 || true
    php artisan route:clear    >/dev/null 2>&1 || true
    php artisan view:clear     >/dev/null 2>&1 || true
    php artisan cache:clear    >/dev/null 2>&1 || true
    echo "[OK] Caché de Laravel limpiada"
}

is_laravel_running() {
    curl -fsS -o /dev/null "http://127.0.0.1:${PORT}/up" 2>/dev/null
}

start_laravel() {
    echo "[i] Iniciando Laravel en puerto ${PORT}..."
    nohup php artisan serve --host=127.0.0.1 --port="${PORT}" \
        > storage/logs/serve.log 2>&1 &
    sleep 2
    if is_laravel_running; then
        echo "[OK] Laravel corriendo en http://127.0.0.1:${PORT}"
    else
        echo "[!] Laravel no respondió. Revisa storage/logs/serve.log"
        exit 1
    fi
}

start_ngrok() {
    echo "[i] Iniciando ngrok en puerto ${PORT}..."
    nohup "$NGROK_BIN" http "${PORT}" --log=stdout --log-level=warn \
        > storage/logs/ngrok.log 2>&1 &
    sleep 4

    # Fetch the public URL from ngrok's local API
    local url
    for _ in {1..10}; do
        url="$(curl -fsS "$NGROK_API" 2>/dev/null \
            | grep -oE '"public_url":"https://[^"]+' \
            | head -n 1 \
            | sed 's/"public_url":"//')"
        [[ -n "$url" ]] && break
        sleep 1
    done

    if [[ -z "$url" ]]; then
        echo "[!] No se pudo obtener la URL pública de ngrok."
        echo "    Revisa storage/logs/ngrok.log"
        exit 1
    fi

    echo "$url"
}

stop_ngrok() {
    pkill -f "ngrok http" 2>/dev/null || true
    echo "[OK] ngrok detenido"
}

restore_env() {
    if [[ -f "${ENV_FILE}.bak" ]]; then
        cp "${ENV_FILE}.bak" "$ENV_FILE"
        echo "[OK] .env restaurado desde backup"
        clear_cache
    else
        echo "[i] No hay backup. Solo reseteando APP_URL..."
        sed -i "s|^APP_URL=.*|APP_URL=http://localhost|" "$ENV_FILE"
        clear_cache
    fi
}

# ----- Main ------------------------------------------------------------------
print_banner
require_ngrok

case "${1:-}" in
    --stop)
        stop_ngrok
        exit 0
        ;;
    --restore)
        restore_env
        exit 0
        ;;
esac

# Normal flow
if ! is_laravel_running; then
    start_laravel
else
    echo "[OK] Laravel ya está corriendo en el puerto ${PORT}"
fi

URL="$(start_ngrok)"
update_env "$URL"
clear_cache

echo ""
echo "============================================================"
echo "  App expuesta en: ${URL}"
echo "  Panel ngrok:    http://127.0.0.1:4040"
echo "============================================================"
echo ""
echo "Para detener:  ./ngrok.sh --stop"
echo "Para restaurar APP_URL local:  ./ngrok.sh --restore"
echo ""
