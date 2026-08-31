<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| APESCON private application configuration
|--------------------------------------------------------------------------
| Copy this file to backend/config/app.php and update the values.
| Keep app.php outside the public web root and never commit it to Git.
*/

return [
    'env' => 'local', // local | production

    // Generate with:
    // php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
    'app_key' => 'e6678babb20845e624bcc28880c149206ba9ee7c234cf0e0a58f6bfd8e9c861d',

    // Set true only when HTTPS terminates before PHP and PHP cannot detect it.
    'force_https' => false,

    // Exact origins only. Do not use "*".
    'allowed_origins' => [
        'http://localhost:5173',
        // 'https://apescon.example.com',
        // 'https://www.apescon.example.com',
    ],

    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'apescon_db',
        'username' => 'root',
        'password' => '$2y$10$oA88vSQssVt1NxnUIDR93uERm7i/i2w7SqnxKHCcFevdwGDIGuv3G',
    ],

    'session' => [
        'idle_timeout_seconds' => 1800,   // 30 minutes
        'absolute_timeout_seconds' => 28800, // 8 hours
        'database_recheck_seconds' => 300,   // Re-check status/role every 5 minutes
        'same_site' => 'Lax',
    ],

    'login_rate_limit' => [
        'window_minutes' => 15,
        'identity_failures' => 5,
        'ip_failures' => 20,
        'retention_days' => 7,
    ],
];
