<?php

declare(strict_types=1);

function luxe_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    luxe_load_env();

    $dsn = getenv("LUXE_DB_DSN") ?: "pgsql:host=127.0.0.1;port=5432;dbname=luxe_ecommerce";
    $user = getenv("LUXE_DB_USER") ?: "postgres";
    $password = getenv("LUXE_DB_PASS") ?: "";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function luxe_load_env(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $loaded = true;
    $path = dirname(__DIR__) . "/.env";
    if (!is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === "" || str_starts_with($line, "#") || !str_contains($line, "=")) {
            continue;
        }

        [$key, $value] = array_map("trim", explode("=", $line, 2));
        if ($key === "" || getenv($key) !== false) {
            continue;
        }

        $value = trim($value, "\"'");
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}
