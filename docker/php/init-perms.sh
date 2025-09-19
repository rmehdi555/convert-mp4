#!/bin/sh
set -e

cd /var/www/html

# Ensure required directories exist
mkdir -p storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  bootstrap/cache

# Relaxed, dev-friendly ownership and permissions
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwX,o+rx storage bootstrap/cache || true

# Clear stale caches (ignore errors if artisan not bootstrapped yet)
php artisan view:clear || true
php artisan cache:clear || true
php artisan config:clear || true

exec php-fpm


