<?php

/**
 * Upserts KEY=VALUE lines read from STDIN into the .env next to `artisan`.
 *
 * Used by the deploy workflow to copy secrets that live only in GitHub
 * Actions (e.g. the Telegram bot token) into the server's .env, so they are
 * never committed. Empty values are ignored, so an unset GitHub secret never
 * blanks out a value that was set on the server by hand.
 *
 *   printf 'TELEGRAM_CHAT_ID=123\n' | php scripts/set-env.php
 */

$path = dirname(__DIR__) . '/.env';
$env = is_file($path) ? (string) file_get_contents($path) : '';
$updated = [];

foreach (preg_split('/\R/', (string) stream_get_contents(STDIN)) as $line) {
    if (! preg_match('/^([A-Z][A-Z0-9_]*)=(.*)$/', trim($line), $match)) {
        continue;
    }

    [, $key, $value] = $match;

    if ($value === '') {
        continue;
    }

    // Plain token-like values stay bare; anything else is single-quoted
    // (literal in phpdotenv: no interpolation, no escapes).
    $entry = preg_match('/^[A-Za-z0-9_:\-.@\/+]+$/', $value) || str_contains($value, "'")
        ? "{$key}={$value}"
        : "{$key}='{$value}'";

    $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
    $env = preg_match($pattern, $env)
        ? preg_replace_callback($pattern, fn () => $entry, $env, 1)
        : rtrim($env, "\n") . "\n{$entry}\n";

    $updated[] = $key;
}

if ($updated !== [] && file_put_contents($path, $env) === false) {
    fwrite(STDERR, "Could not write {$path}\n");
    exit(1);
}

echo 'set-env: ' . ($updated === [] ? 'nothing to update' : 'updated ' . implode(', ', $updated)) . PHP_EOL;
