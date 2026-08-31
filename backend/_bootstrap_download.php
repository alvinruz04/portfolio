<?php
declare(strict_types=1);

// No default JSON header here (download endpoints set their own headers)

// Convert errors to plain text (download-safe)
ini_set('display_errors', '0');
error_reporting(E_ALL);

set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Server exception: " . $e->getMessage();
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "PHP error: $message in $file:$line";
    exit;
});

// Session cookie policy (same as main bootstrap)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',   // change to 'None' if cross-site
    'secure' => $isHttps,
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/includes/cors.php';
require_once __DIR__ . '/app/includes/db.php';

// Minimal auth helpers (use same rules as your API)
function json_fail_download(string $message, int $code = 400): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

function require_auth_download(): void
{
    if (empty($_SESSION['auth']) || empty($_SESSION['user_id'])) {
        json_fail_download('Unauthorized.', 401);
    }
}

function require_superadmin_download(): void
{
    require_auth_download();
    if (($_SESSION['role'] ?? null) !== 'superadmin') {
        json_fail_download('Forbidden (superadmin only).', 403);
    }
}

function require_admin_download(): void
{
    require_auth_download();
    if (($_SESSION['role'] ?? null) !== 'admin') {
        json_fail_download('Forbidden (admin only).', 403);
    }
}

