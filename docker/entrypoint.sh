#!/bin/sh
set -eu

cd /var/www/html

log() { printf '\033[36m[entrypoint]\033[0m %s\n' "$1"; }

is_production() {
    [ "${APP_ENV:-local}" = "production" ] || [ "${APP_ENV:-local}" = "prod" ]
}

wait_for_db() {
    i=0
    until php -r '
        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s",
                getenv("DB_HOST") ?: "postgres",
                getenv("DB_PORT") ?: "5432",
                getenv("DB_DATABASE") ?: "promptforge"
            );
            new PDO($dsn, getenv("DB_USERNAME") ?: "promptforge", getenv("DB_PASSWORD") ?: "");
        } catch (Throwable) {
            exit(1);
        }
    ' >/dev/null 2>&1; do
        i=$((i + 1))
        if [ "$i" -ge 60 ]; then
            log "Database unreachable after ${i}s, aborting."
            exit 1
        fi
        sleep 1
    done
    log "Database is up."
}

ensure_directories() {
    mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache
    [ "$(id -u)" = "0" ] && chown -R www-data:www-data storage bootstrap/cache || true
}

ensure_app_key() {
    if [ -n "${APP_KEY:-}" ]; then
        return 0
    fi

    if is_production; then
        printf '%s\n' "APP_KEY must be provided in production (pass it via environment or env_file)." >&2
        exit 1
    fi

    # Persist to a bind-mounted .env so the key survives restarts;
    # fall back to an ephemeral key for the lifetime of the container.
    if [ ! -f .env ] && touch .env 2>/dev/null; then
        :
    fi
    if [ -f .env ] && [ -w .env ]; then
        php artisan key:generate --force >/dev/null
    else
        export APP_KEY="$(php artisan key:generate --show)"
    fi
    log "Generated application key."
}

run_optimization() {
    if is_production; then
        log "Caching config, routes, views & events..."
        php artisan optimize --no-interaction
    else
        php artisan config:clear >/dev/null 2>&1 || true
        php artisan route:clear >/dev/null 2>&1 || true
        php artisan view:clear >/dev/null 2>&1 || true
    fi
}

# --- boot sequence ---------------------------------------------------------

if [ "$(id -u)" = "0" ] && [ ! -f vendor/autoload.php ] && command -v composer >/dev/null 2>&1; then
    log "vendor/ missing, running composer install..."
    composer install --prefer-dist --no-interaction --no-progress
fi

ensure_directories
ensure_app_key

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    wait_for_db
    log "Running migrations..."
    php artisan migrate --force --no-interaction
fi

php artisan storage:link >/dev/null 2>&1 || true

run_optimization

# --- hand over to CMD ------------------------------------------------------

if [ "$(id -u)" = "0" ]; then
    if [ "${SKIP_PRIVILEGE_DROP:-false}" = "true" ] || [ "$1" = "php-fpm" ]; then
        # php-fpm master drops privileges to www-data itself
        exec "$@"
    fi
    if command -v su-exec >/dev/null 2>&1; then
        exec su-exec www-data "$@"
    fi
fi

exec "$@"
