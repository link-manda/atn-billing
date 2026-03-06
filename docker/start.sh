#!/bin/sh
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Menunggu database siap (opsional, tapi disarankan)
echo "Waiting for database connection..."
# Bisa menggunakan script wait-for-it jika perlu, tapi kita skip untuk kesederhanaan

# Jalankan migrasi database ke Supabase secara paksa (--force wajib di production)
echo "Running database migrations..."
php artisan migrate --force

# Pastikan cache di-clear dan di-build ulang setelah migrasi (opsional)
# php artisan optimize:clear
# php artisan optimize

# Mulai PHP-FPM di background
echo "Starting PHP-FPM..."
php-fpm -D

# Mulai Nginx di foreground
echo "Starting Nginx..."
nginx -g "daemon off;"