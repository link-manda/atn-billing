#!/bin/sh

echo "Fixing permissions..."

mkdir -p /tmp
chmod 777 /tmp

chmod -R 775 storage
chmod -R 775 bootstrap/cache

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan migrate --force

php-fpm -D
nginx -g "daemon off;"