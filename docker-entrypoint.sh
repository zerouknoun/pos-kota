#!/bin/bash
set -e

echo "=== POS Kota - Railway Startup ==="

# Buat .env dari .env.example jika belum ada
# Railway inject env vars sebagai OS environment — akan override nilai .env ini
if [ ! -f /var/www/html/.env ]; then
    echo ">>> Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate APP_KEY dan tulis ke .env
echo ">>> Generating APP_KEY..."
php artisan key:generate --force

# Jalankan database migrations
echo ">>> Running migrations..."
php artisan migrate --force --seed 2>/dev/null || php artisan migrate --force

# Cache config, routes, views untuk performa production
echo ">>> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Buat symlink storage (abaikan error jika sudah ada)
echo ">>> Creating storage symlink..."
php artisan storage:link || true

# Fix permissions setelah semua selesai
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Startup selesai! Menjalankan Apache... ==="

exec "$@"
