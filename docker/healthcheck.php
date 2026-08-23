<?php

// Container-level health check for the app/queue/scheduler images.
// Verifies: critical env present, PostgreSQL reachable, storage writable.

declare(strict_types=1);

function fail(string $message): never
{
    fwrite(STDERR, "unhealthy: {$message}\n");
    exit(1);
}

foreach (['APP_KEY', 'DB_HOST', 'DB_DATABASE'] as $var) {
    if (getenv($var) === false || getenv($var) === '') {
        fail("environment variable {$var} is not set");
    }
}

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

$logPath = getenv('LARAVEL_STORAGE_PATH') ?: '/var/www/html/storage';
if (! is_writable($logPath.'/logs') && ! @mkdir($logPath.'/logs', 0775, true) && ! is_dir($logPath.'/logs')) {
    fail("{$logPath}/logs is not writable");
}

exit(0);
