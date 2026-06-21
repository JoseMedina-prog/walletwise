#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "[entrypoint] Creando .env desde .env.example..."
    cp .env.example .env
fi

if [ -z "$(grep -E '^APP_KEY=base64:.+' .env 2>/dev/null)" ]; then
    echo "[entrypoint] Generando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Permisos de storage y bootstrap (idempotente y tolerante a bind-mounts)
chown -R "$(id -u):$(id -g)" storage bootstrap/cache 2>/dev/null || true
find storage bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true

echo "[entrypoint] Limpiando caches de bootstrap..."
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php 2>/dev/null || true
php artisan package:discover --ansi --no-interaction || true

echo "[entrypoint] Limpiando caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

if [ "${APP_ENV:-local}" = "production" ]; then
    echo "[entrypoint] Modo producción: optimizando..."
    php artisan config:cache --force --no-interaction || true
    php artisan route:cache --force --no-interaction || true
    php artisan view:cache --force --no-interaction || true
    php artisan event:cache --force --no-interaction || true
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "[entrypoint] Ejecutando migraciones..."
    php artisan migrate --force --no-interaction || true
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "[entrypoint] Ejecutando seeders..."
    php artisan db:seed --force --no-interaction || true
fi

echo "[entrypoint] storage:link..."
php artisan storage:link || true

echo "[entrypoint] Iniciando servicio..."
exec "$@"