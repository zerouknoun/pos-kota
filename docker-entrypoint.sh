#!/bin/bash
set -e

echo "=== POS Kota - Railway Startup ==="

# Generate APP_KEY jika belum di-set
if [ -z "$APP_KEY" ]; then
    echo ">>> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Jalankan database migrations
echo ">>> Running migrations..."
php artisan migrate --force

# Cache config, routes, views untuk performa production
echo ">>> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Buat symlink storage (abaikan error jika sudah ada)
echo ">>> Creating storage symlink..."
php artisan storage:link || true

echo "=== Startup selesai! Menjalankan Apache... ==="

exec "$@"
