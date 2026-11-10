# ==========================================
# 1. Frontend Build Stage
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

# Install npm dependencies
COPY package.json package-lock.json* ./
RUN npm ci || npm install

# Copy source assets & config needed for asset compilation
COPY vite.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# ==========================================
# 2. Production Application
# ==========================================
FROM serversideup/php:8.4-fpm-nginx-alpine

WORKDIR /var/www/html

# Switch to root to prepare structure and ownership
USER root

COPY --chown=www-data:www-data . /var/www/html

# Copy compiled frontend assets from frontend stage
COPY --chown=www-data:www-data --from=frontend /app/public/build /var/www/html/public/build

USER www-data
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
USER root

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

EXPOSE 8080
