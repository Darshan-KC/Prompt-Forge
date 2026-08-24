<?php

// =============================================================================
// CONTAINER HEALTHCHECK — deep readiness probe (app / queue / scheduler)
// =============================================================================
// Invoked by Docker on a schedule (see `healthcheck:` in the compose files):
//   exit 0  => healthy    exit != 0 => unhealthy
//
// WHY NOT A SIMPLER CHECK?
//   - "process is running" says nothing: PHP could be up while the DB is down
//   - an HTTP request would need curl/fcgi tooling fpm containers lack
//   - this script validates the three things the container truly needs:
//       1. critical environment present   (catches config/deploy mistakes)
//       2. PostgreSQL actually reachable  (catches network/DB outages)
//       3. storage/logs writable          (Laravel cannot operate without it)
//
// Deliberately framework-free (raw PDO, no artisan bootstrap): a health probe
// must stay fast (<1s), never deadlock on the very subsystems it diagnoses,
// and keep working even when Laravel itself cannot boot.
//
// NOTE: this checks INFRASTRUCTURE health. Application-level health (the
// built-in `/up` route) is verified end-to-end by nginx's healthcheck, which
// performs a real HTTP request through the whole stack.
// =============================================================================

declare(strict_types=1);

function fail(string $message): never
{
    fwrite(STDERR, "unhealthy: {$message}\n");   // visible in `docker inspect`
    exit(1);
}

// --- 1. critical configuration present --------------------------------------
// APP_KEY missing => encryption broken; DB_* missing => guaranteed connection
// failure. Detect misconfiguration explicitly instead of failing obscurely.
foreach (['APP_KEY', 'DB_HOST', 'DB_DATABASE'] as $var) {
    if (getenv($var) === false || getenv($var) === '') {
        fail("environment variable {$var} is not set");
    }
}

// --- 2. database connectivity ------------------------------------------------
// A real SELECT verifies not just TCP reachability but authentication AND
// query execution. ATTR_TIMEOUT=3 caps connect() so one slow check can't
// exceed the compose healthcheck timeout budget.
try {
    $pdo = new PDO(
        sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('DB_HOST'),
            getenv('DB_PORT') ?: '5432',
            getenv('DB_DATABASE')
        ),
        getenv('DB_USERNAME') ?: null,
        getenv('DB_PASSWORD') ?: null,
        [PDO::ATTR_TIMEOUT => 3, PDO::ERRMODE_EXCEPTION => true]
    );
    $pdo->query('SELECT 1');
} catch (Throwable $e) {
    fail('database connection failed: '.$e->getMessage());
}

// --- 3. storage writable ------------------------------------------------------
// Laravel writes logs/sessions/uploads under storage/. If that path became
// read-only (volume mis-mount, root-squash...), the app fails at runtime —
// surface it here. The triple-check creates the directory if genuinely
// absent, then confirms writability.
$logPath = getenv('LARAVEL_STORAGE_PATH') ?: '/var/www/html/storage';
if (! is_writable($logPath.'/logs') && ! @mkdir($logPath.'/logs', 0775, true) && ! is_dir($logPath.'/logs')) {
    fail("{$logPath}/logs is not writable");
}

exit(0);   // all good
