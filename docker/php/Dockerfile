# syntax=docker/dockerfile:1

# ---------- Stage 1: PHP dependencies (cached until lock files change) ----------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-autoloader \
    --no-scripts

# ---------- Stage 2: Frontend assets (cached until package files change) ----------
FROM node:22-alpine AS assets

WORKDIR /app

# Glob tolerates a missing lockfile; commit one for reproducible builds
COPY package*.json .npmrc ./

RUN npm install --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources

RUN npm run build

# ---------- Stage 3: Production runtime ----------
FROM php:8.3-fpm-alpine AS app

LABEL org.opencontainers.image.description="prompt-forge production image (php-fpm)"

# Runtime helpers only; composer is intentionally NOT shipped to prod
RUN apk add --no-cache su-exec tzdata

# PHP extensions required by Laravel + PostgreSQL
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mbstring pdo_pgsql bcmath pcntl opcache

WORKDIR /var/www/html

# Application source (context is filtered by .dockerignore)
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=root:root /app/public/build ./public/build
COPY --chown=www-data:www-data docker/php/prod.ini $PHP_INI_DIR/conf.d/99-app.ini
COPY --chown=www-data:www-data docker/php/opcache.prod.ini $PHP_INI_DIR/conf.d/99-opcache.ini
COPY --chown=www-data:www-data docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY --chown=root:root docker/entrypoint.sh /usr/local/bin/entrypoint
COPY --chown=www-data:www-data docker/healthcheck.php /opt/healthcheck.php

# Borrow composer for the optimized autoloader, then drop it again.
# post-autoload-dump also runs package discovery (bootstrap/cache/packages.php).
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && rm /usr/local/bin/composer \
    && chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/{cache/data,sessions,testing,views} \
              storage/app/public storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
