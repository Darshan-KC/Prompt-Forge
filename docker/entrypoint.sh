#!/bin/sh
# =============================================================================
# CONTAINER ENTRYPOINT — shared by local & production images
# =============================================================================
# Runs BEFORE the container's main command (CMD from the Dockerfile, or the
# `command:` override in compose) and prepares the runtime environment:
#
#   1. composer install          (only if vendor/ is missing — dev bind mounts)
#   2. storage skeleton + perms  (volumes can shadow baked-in directories)
#   3. APP_KEY handling          (generate locally, hard-fail in production)
#   4. .env.container generation (local only, see below)
#   5. wait for postgres         (when RUN_MIGRATIONS=true)
#   6. php artisan migrate       (same flag)
#   7. storage:link              (idempotent public/storage symlink)
#   8. caches                    (production: build them / local: clear them)
#   9. exec CMD                  (replacing PID 1 => signals reach the server)
#
# Design notes:
#   - POSIX sh (not bash): alpine images ship no bash.
#   - set -eu : abort on any error or unset variable — a half-initialized
#     container is worse than a crashed one that restart policies retry.
#   - Runs as root in production (php-fpm needs root to drop privileges for
#     workers); runs as your host user locally, so every root-only step is
#     guarded by an id -u check.
# =============================================================================

set -eu

# Every artisan command must run from the application root.
cd /var/www/html

log() { printf '\033[36m[entrypoint]\033[0m %s\n' "$1"; }

is_production() {
    [ "${APP_ENV:-local}" = "production" ] || [ "${APP_ENV:-local}" = "prod" ]
}

# -----------------------------------------------------------------------------
# wait_for_db — block until PostgreSQL accepts connections
# -----------------------------------------------------------------------------
# Containers start in parallel; postgres needs a few seconds to initialize.
# Rather than crashing the app on first boot, poll with a raw PDO connection
# (no framework boot needed => fast, no log noise) until it succeeds.
# Gives up after 60s: by then it's a real problem, not a startup race.
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
            exit(1);   // not up yet -> retry
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

# -----------------------------------------------------------------------------
# ensure_directories — guarantee Laravel's writable directory skeleton
# -----------------------------------------------------------------------------
# Laravel auto-creates these at runtime, but only when running as the right
# user; pre-creating avoids permission errors when volumes are mounted over
# /storage (a fresh named volume starts EMPTY, hiding whatever the image had).
ensure_directories() {
    mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache
    # chown only when running as root (production). The `|| true` keeps the
    # script alive under set -e when running unprivileged (local dev).
    [ "$(id -u)" = "0" ] && chown -R www-data:www-data storage bootstrap/cache || true
}

# -----------------------------------------------------------------------------
# ensure_app_key — the one secret the app cannot boot without
# -----------------------------------------------------------------------------
# Production: NEVER auto-generate. A silently regenerated key would encrypt
# sessions/cookies with a key nobody knows => all users logged out, encrypted
# data unreadable. Failing loudly forces explicit provisioning.
#
# Local: generate on first boot. Prefer persisting into a bind-mounted .env so
# the key survives container rebuilds; if .env isn't writable, fall back to an
# ephemeral key valid for this container run only (sessions reset per boot —
# acceptable for development).
ensure_app_key() {
    if [ -n "${APP_KEY:-}" ]; then
        return 0                      # already provided via environment/env_file
    fi

    if is_production; then
        printf '%s\n' "APP_KEY must be provided in production (pass it via environment or env_file)." >&2
        exit 1
    fi

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

# -----------------------------------------------------------------------------
# build_container_env_file — local-only bridge around `artisan serve`
# -----------------------------------------------------------------------------
# WHY THIS EXISTS:
#   `php artisan serve` intentionally DROPS all inherited environment variables
#   from its request workers (keeping only a tiny allowlist like APP_ENV), so
#   that editing .env takes effect without restarting. Side effect: compose
#   `environment:` can never reach HTTP requests — workers would fall back to
#   the host-oriented .env (DB host 127.0.0.1!) inside the container.
#
# SOLUTION:
#   Generate `.env.container` with container-correct values (DB host
#   "postgres", mailpit SMTP...) and load it via `serve --env=container`.
#   docker-compose.yml sets APP_ENV=container to select exactly this file.
build_container_env_file() {
    is_production && return 0         # prod injects env directly; skip entirely

    # Key resolution order: real env -> existing .env -> freshly generated.
    # (A stable key matters even locally: losing it logs out every session.)
    local_key="${APP_KEY:-}"
    if [ -z "$local_key" ] && [ -f .env ]; then
        local_key="$(grep -E '^APP_KEY=' .env | head -1 | cut -d= -f2-)"
    fi
    if [ -z "$local_key" ]; then
        local_key="$(php artisan key:generate --show)"   # prints WITHOUT writing
    fi
    export APP_KEY="$local_key"

    app_display_name="${APP_NAME:-Laravel}"

    cat > .env.container <<EOF
APP_NAME=$app_display_name
# Must stay "container": the serve process loads this file via APP_ENV
APP_ENV=container
APP_KEY=$local_key
APP_DEBUG=true
APP_URL=http://localhost:${APP_HOST_PORT:-8000}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=${DB_DATABASE:-promptforge}
DB_USERNAME=${DB_USERNAME:-promptforge}
DB_PASSWORD=${DB_PASSWORD:-secret}

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-mailpit}
MAIL_PORT=${MAIL_PORT:-1025}
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="$app_display_name"

VITE_APP_NAME="$app_display_name"
EOF
    # Make the file the single source of truth for THIS process tree too:
    # subsequent artisan calls (migrate...) and the exec'd serve command all
    # inherit identical values whether they read env vars or the file.
    set -a                            # auto-export everything defined below
    . ./.env.container
    set +a
    log "Wrote and loaded .env.container."
}

# -----------------------------------------------------------------------------
# run_optimization — environment-appropriate cache strategy
# -----------------------------------------------------------------------------
# Production: build ALL framework caches once at boot. With OPcache
# (validate_timestamps=0) and immutable images, cached config/routes/views/
# events stay valid for the container's entire lifetime. Boot cost is paid
# once; every request afterwards skips re-parsing config & routes.
#
# Local: CLEAR any caches instead. Stale route/config caches are the classic
# "works in prod, broken locally" trap while code changes constantly.
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

# =============================================================================
# BOOT SEQUENCE
# =============================================================================

# Safety net for local bind-mount workflows where the developer hasn't run
# composer yet: vendor/ is missing AND composer exists in this image.
# (Production images always contain vendor/, so this never triggers there.)
if [ "$(id -u)" = "0" ] && [ ! -f vendor/autoload.php ] && command -v composer >/dev/null 2>&1; then
    log "vendor/ missing, running composer install..."
    composer install --prefer-dist --no-interaction --no-progress
fi

ensure_directories
ensure_app_key
build_container_env_file

# Migrations opt-in via env so queue/scheduler containers DON'T race the app
# over the schema — only one designated service migrates (see compose files).
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    wait_for_db
    log "Running migrations..."
    php artisan migrate --force --no-interaction
fi

# public/storage symlink for serving uploads. Idempotent; failure is non-
# fatal (e.g. read-only fs in odd setups) hence the `|| true`.
php artisan storage:link >/dev/null 2>&1 || true

run_optimization

# =============================================================================
# HAND OVER TO CMD
# =============================================================================
# `exec` REPLACES the shell with the target process => it becomes PID 1 and
# receives SIGTERM/SIGINT directly (essential for graceful shutdown of fpm,
# queue workers...).
if [ "$(id -u)" = "0" ]; then
    # Two legitimate reasons to keep root:
    # 1) SKIP_PRIVILEGE_DROP=true — jobs that need root (assets-init copy)
    # 2) php-fpm — its master MUST start as root to spawn www-data workers;
    #    it performs its own privilege drop afterwards.
    if [ "${SKIP_PRIVILEGE_DROP:-false}" = "true" ] || [ "$1" = "php-fpm" ]; then
        exec "$@"
    fi
    # Everything else (artisan serve, queue:work, schedule:work) runs
    # unprivileged: su-exec switches user THEN exec's the command.
    if command -v su-exec >/dev/null 2>&1; then
        exec su-exec www-data "$@"
    fi
fi

exec "$@"   # already non-root (local dev runs as host UID) -> just exec
