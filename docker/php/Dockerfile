# =============================================================================
# PRODUCTION IMAGE — multi-stage build for Laravel 13 (PHP 8.3) + PostgreSQL
# =============================================================================
# Build:   docker build -f docker/php/Dockerfile -t prompt-forge:latest .
# Result:  ~208MB php-fpm-alpine image containing application code, production
#          composer dependencies, compiled frontend assets, tuned OPcache and
#          a hardened php-fpm pool — but NO composer, NO dev deps, NO tests.
#
# Layer-cache strategy (why the stages are ordered like this):
#   Stage "vendor" re-runs ONLY when composer.lock changes.
#   Stage "assets" re-runs ONLY when package-lock.json / resources change.
#   Final stage copies their outputs, so everyday code edits rebuild just the
#   last few cheap layers.
#
# The same image powers every prod container (fpm app, queue worker,
# scheduler) — only the startup command differs (see docker-compose.prod.yml).
# =============================================================================

# syntax=docker/dockerfile:1  → modern BuildKit syntax (better default output)

# ---------- Stage 1: PHP dependencies (cached until lock files change) --------
FROM composer:2 AS vendor
# Composer's own image ships PHP + composer already matched to each other.

WORKDIR /app

# Copy manifests FIRST so Docker can cache this layer independently of source.
COPY composer.json composer.lock ./

# --no-dev          exclude require-dev packages (phpunit, pint...) from prod
# --prefer-dist     download zips instead of cloning repos => faster, smaller
# --no-autoloader   skip autoload generation: classes don't exist yet (app/
#                   is copied in the FINAL stage), the authoritative classmap
#                   must be built there
# --no-scripts      skip post-autoload-dump (would call artisan, absent here)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-autoloader \
    --no-scripts

# ---------- Stage 2: Frontend assets (cached until package files change) ------
FROM node:22-alpine AS assets
# Node 22 LTS satisfies Vite 8's engine requirement (>= 20.19 / 22.12).

WORKDIR /app

# Glob tolerates a missing lockfile; commit one for reproducible builds.
COPY package*.json .npmrc ./

# npm ci would be stricter/faster once a lockfile exists; npm install works
# with or without it and respects the lockfile when present.
RUN npm install --no-audit --no-fund

COPY vite.config.js ./
# css/js inputs (+ blade views: tiny, and keeps the COPY simple)
COPY resources ./resources

# Emits public/build/* with content-hashed filenames (e.g. app-BvRk9kiK.js)
# which nginx later serves with immutable year-long cache headers.
RUN npm run build

# ---------- Stage 3: Production runtime ---------------------------------------
FROM php:8.3-fpm-alpine AS app
# fpm-alpine: smallest official PHP-FPM base; matches composer.json (^8.3).

LABEL org.opencontainers.image.description="prompt-forge production image (php-fpm)"

# su-exec: ~20KB replacement for gosu — lets the entrypoint start as root
# (to fix volume permissions) then drop to www-data before running CLI commands.
# tzdata: correct timestamps if TZ env var is set.
# NOTE: composer is deliberately NOT shipped — vendor/ arrives fully built.
RUN apk add --no-cache su-exec tzdata

# install-php-extensions compiles/configures extensions in one line, picking
# correct configure flags per version — far less error-prone than raw
# docker-php-ext-install calls.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# mbstring  required by Laravel (string handling, UTF-8)
# pdo_pgsql PostgreSQL driver (config/database.php default here)
# bcmath    precise math used by various packages
# pcntl     POSIX signals so queue workers handle SIGTERM/SIGUSR2 gracefully
# opcache   bytecode cache — biggest single PHP perf win (tuned via ini below)
RUN install-php-extensions mbstring pdo_pgsql bcmath pcntl opcache

WORKDIR /var/www/html

# --- assemble the application -------------------------------------------------
# Application source (context filtered by .dockerignore: no tests, no secrets,
# no local storage state). Copied AFTER vendor/assets so those layers stay
# cached during normal development.
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor

# Built assets are world-readable static files owned by root: they must never
# be modified at runtime, and root ownership enforces exactly that.
COPY --from=assets --chown=root:root /app/public/build ./public/build

# Configuration baked into the image:
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
# --optimize                build a classmap => no PSR-4 filesystem lookups
# --classmap-authoritative  ONLY the classmap is consulted => even fewer checks;
#                           safe in prod because all classes are known at build time
# --no-dev                  regenerate rules without dev packages
# mkdir/chown: storage skeleton must exist & be writable by www-data because
# named volumes mounted over /storage in compose inherit these permissions.

# ENTRYPOINT runs on every container start (migrations, caches, perms);
# CMD is the long-running process, overridden per service in compose.
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
