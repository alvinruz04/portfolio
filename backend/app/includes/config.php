<?php
declare(strict_types=1);

$configPath = dirname(__DIR__, 2) . '/config/app.php';

if (!is_file($configPath)) {
    throw new RuntimeException(
        'Missing backend/config/app.php. Copy backend/config/app.example.php and configure it.'
    );
}

$appConfig = require $configPath;

if (!is_array($appConfig)) {
    throw new RuntimeException('backend/config/app.php must return an array.');
}

$GLOBALS['apescon_config'] = $appConfig;

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['apescon_config'] ?? [];

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}
