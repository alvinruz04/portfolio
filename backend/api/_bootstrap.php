<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'");

ini_set('display_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set('UTC');

set_exception_handler(static function (Throwable $exception): void {
    error_log(sprintf(
        '[APESCON API] %s in %s:%d',
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    ));

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode(
        ['success' => false, 'message' => 'An unexpected server error occurred.'],
        JSON_UNESCAPED_SLASHES
    );
    exit;
});

set_error_handler(static function (
    int $severity,
    string $message,
    string $file,
    int $line
): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once __DIR__ . '/../app/includes/config.php';
require_once __DIR__ . '/../app/includes/cors.php';
require_once __DIR__ . '/../app/includes/password_policy.php';

$appKey = (string) config('app_key', '');

if (strlen($appKey) < 32 || str_contains($appKey, 'CHANGE_THIS')) {
    throw new RuntimeException('APP key is missing or insecure.');
}

$isHttps = (bool) config('force_https', false)
    || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
    || ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443');

if ((string) config('env', 'production') === 'production' && !$isHttps) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'HTTPS is required.',
    ]);
    exit;
}

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
ini_set('session.cookie_httponly', '1');

session_name(
    (string) config('env', 'production') === 'production' && $isHttps
        ? '__Host-apescon_session'
        : 'apescon_session'
);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => (string) config('session.same_site', 'Lax'),
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../app/includes/db.php';

function json_ok(array $data = [], int $code = 200): void
{
    http_response_code($code);

    echo json_encode(
        array_merge(['success' => true], $data),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    exit;
}

function json_fail(string $message, int $code = 400, array $extra = []): void
{
    http_response_code($code);

    echo json_encode(
        array_merge(['success' => false, 'message' => $message], $extra),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    exit;
}

function require_method(string ...$allowedMethods): void
{
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $allowed = array_map('strtoupper', $allowedMethods);

    if (!in_array($method, $allowed, true)) {
        header('Allow: ' . implode(', ', $allowed));
        json_fail('Method not allowed.', 405);
    }
}

function read_json_body(int $maximumBytes = 8192): array
{
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

    if ($contentLength > $maximumBytes) {
        json_fail('Request body is too large.', 413);
    }

    $raw = file_get_contents('php://input');

    if ($raw === false || strlen($raw) > $maximumBytes) {
        json_fail('Request body is too large.', 413);
    }

    try {
        $data = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        json_fail('Invalid JSON request.', 400);
    }

    if (!is_array($data)) {
        json_fail('Invalid JSON request.', 400);
    }

    return $data;
}

function is_unsafe_http_method(): bool
{
    return in_array(
        strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
        ['POST', 'PUT', 'PATCH', 'DELETE'],
        true
    );
}

if (is_unsafe_http_method() && !apescon_is_trusted_origin()) {
    json_fail('Invalid request origin.', 403);
}

function client_ip_address(): string
{
    $ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

function secure_hmac(string $value): string
{
    return hash_hmac('sha256', $value, (string) config('app_key'));
}

function expire_session_cookie(): void
{
    if (!ini_get('session.use_cookies')) {
        return;
    }

    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'] ?: '/',
        'domain' => $params['domain'] ?? '',
        'secure' => (bool) ($params['secure'] ?? false),
        'httponly' => (bool) ($params['httponly'] ?? true),
        'samesite' => (string) ($params['samesite'] ?? 'Lax'),
    ]);
}

function destroy_current_session(): void
{
    $_SESSION = [];
    expire_session_cookie();

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function session_user(mysqli $conn): ?array
{
    if (empty($_SESSION['auth']) || empty($_SESSION['user_id'])) {
        return null;
    }

    $now = time();
    $startedAt = (int) ($_SESSION['started_at'] ?? 0);
    $lastActivityAt = (int) ($_SESSION['last_activity_at'] ?? 0);

    $idleTimeout = (int) config(
        'session.idle_timeout_seconds',
        1800
    );

    $absoluteTimeout = (int) config(
        'session.absolute_timeout_seconds',
        28800
    );

    if (
        $startedAt <= 0
        || $lastActivityAt <= 0
        || ($now - $lastActivityAt) > $idleTimeout
        || ($now - $startedAt) > $absoluteTimeout
    ) {
        destroy_current_session();

        return null;
    }

    /*
     * Update activity time.
     *
     * Do not bind the authenticated session to the browser
     * User-Agent. Responsive/device emulation, browser updates,
     * privacy features and legitimate browser changes may alter
     * the User-Agent during an otherwise valid session.
     */
    $_SESSION['last_activity_at'] = $now;

    $lastVerifiedAt = (int) (
        $_SESSION['last_verified_at'] ?? 0
    );

    $recheckSeconds = (int) config(
        'session.database_recheck_seconds',
        300
    );

    if (($now - $lastVerifiedAt) >= $recheckSeconds) {
        $userId = (int) $_SESSION['user_id'];

        $statement = $conn->prepare(
            'SELECT
                id,
                email_address,
                name,
                role,
                status
             FROM users
             WHERE id = ?
             LIMIT 1'
        );

        $statement->bind_param('i', $userId);
        $statement->execute();

        $user = $statement
            ->get_result()
            ->fetch_assoc();

        $statement->close();

        if (
            !$user
            || (string) $user['status'] !== 'active'
        ) {
            destroy_current_session();

            return null;
        }

        $_SESSION['email_address'] =
            (string) $user['email_address'];

        $_SESSION['name'] =
            (string) $user['name'];

        $_SESSION['role'] =
            (string) $user['role'];

        $_SESSION['last_verified_at'] = $now;
    }

    return [
        'id' => (int) $_SESSION['user_id'],

        'email_address' =>
            (string) (
                $_SESSION['email_address'] ?? ''
            ),

        'name' =>
            (string) (
                $_SESSION['name'] ?? ''
            ),

        'role' =>
            (string) (
                $_SESSION['role'] ?? ''
            ),
    ];
}

function require_auth(): array
{
    global $conn;

    $user = session_user($conn);

    if (!$user) {
        json_fail('Unauthorized.', 401);
    }

    return $user;
}

function require_roles(array $roles): array
{
    $user = require_auth();

    if (!in_array($user['role'], $roles, true)) {
        json_fail('Forbidden.', 403);
    }

    return $user;
}

function require_admin(): array
{
    return require_roles(['admin']);
}

function require_user(): array
{
    return require_roles(['user']);
}

function current_user_id(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function start_authenticated_session(array $user): string
{
    $_SESSION = [];
    session_regenerate_id(true);

    $now = time();

    $_SESSION['auth'] = true;
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['email_address'] = (string) $user['email_address'];
    $_SESSION['name'] = (string) $user['name'];
    $_SESSION['role'] = (string) $user['role'];
    $_SESSION['started_at'] = $now;
    $_SESSION['last_activity_at'] = $now;
    $_SESSION['last_verified_at'] = $now;
    $_SESSION['csrf'] = bin2hex(random_bytes(32));

    return (string) $_SESSION['csrf'];
}

function ensure_csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function require_csrf(): void
{
    require_auth();

    $providedToken = trim((string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));

    if ($providedToken === '') {
        json_fail('Missing CSRF token.', 419);
    }

    $sessionToken = ensure_csrf_token();

    if (!hash_equals($sessionToken, $providedToken)) {
        json_fail('Invalid CSRF token.', 419);
    }
}
