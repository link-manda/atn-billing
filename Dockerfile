# 1. Build dependencies with Composer
FROM composer:2.7 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# PERBAIKAN: Tambahkan --no-scripts agar Composer tidak mencoba menjalankan 'artisan' yang belum dicopy
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs --no-scripts

# 2. Build assets with Node
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js postcss.config.js* tailwind.config.* ./
RUN npm ci
COPY resources/ ./resources/
COPY public/ ./public/

# Batasi memori Node.js agar tidak melebihi RAM Free Tier (512MB)
ENV NODE_OPTIONS="--max-old-space-size=400"
RUN npm run build

# 3. Final image
FROM php:8.2-fpm-alpine

# Install system dependencies & PostgreSQL driver untuk Supabase
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    freetype-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql gd zip bcmath

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Setup working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy built vendor and frontend assets
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/

# Set proper permissions untuk storage, cache, dan direktori temporary
RUN mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/testing \
    && mkdir -p /var/www/html/storage/framework/views \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod 777 /tmp

# Menyimpan lokasi temporary dir agar PHP menggunakannya dengan benar
ENV SYS_TEMP_DIR=/tmp

# Copy and setup start script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Optimize Laravel for Production
# Kita tambahkan package:discover di sini karena sebelumnya kita skip (--no-scripts) di tahap composer
RUN php artisan package:discover --ansi \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]