<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('POST');

const APESCON_DUMMY_PASSWORD_HASH = '$2y$12$aTnaHAhNKDp5z5D1VGLftOy8WqG3fVu04dzPTKzFaw/hTpTQnx60a';

function recent_failed_attempts(
    mysqli $conn,
    string $column,
    string $hash,
    int $windowMinutes
): array {
    if (!in_array($column, ['identity_hash', 'ip_hash'], true)) {
        throw new InvalidArgumentException('Invalid rate-limit column.');
    }

    $sql = "
        SELECT COUNT(*) AS attempt_count, MIN(attempted_at) AS oldest_attempt
        FROM login_attempts
        WHERE {$column} = ?
          AND was_successful = 0
          AND attempted_at >= (UTC_TIMESTAMP() - INTERVAL {$windowMinutes} MINUTE)
    ";

    $statement = $conn->prepare($sql);
    $statement->bind_param('s', $hash);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return [
        'count' => (int) ($row['attempt_count'] ?? 0),
        'oldest_attempt' => $row['oldest_attempt'] ?? null,
    ];
}

function retry_after_seconds(?string $oldestAttempt, int $windowMinutes): int
{
    if (!$oldestAttempt) {
        return $windowMinutes * 60;
    }

    $expiresAt = strtotime($oldestAttempt . ' UTC') + ($windowMinutes * 60);

    return max(1, $expiresAt - time());
}

function record_login_attempt(
    mysqli $conn,
    string $identityHash,
    string $ipHash,
    bool $successful,
    ?int $userId
): void {
    $successValue = $successful ? 1 : 0;

    $statement = $conn->prepare(
        'INSERT INTO login_attempts
            (identity_hash, ip_hash, user_id, was_successful, attempted_at)
         VALUES (?, ?, ?, ?, UTC_TIMESTAMP())'
    );
    $statement->bind_param(
        'ssii',
        $identityHash,
        $ipHash,
        $userId,
        $successValue
    );
    $statement->execute();
    $statement->close();
}

function clear_identity_failures(mysqli $conn, string $identityHash): void
{
    $statement = $conn->prepare(
        'DELETE FROM login_attempts
         WHERE identity_hash = ?
           AND was_successful = 0'
    );
    $statement->bind_param('s', $identityHash);
    $statement->execute();
    $statement->close();
}

function rate_limit_response(int $retryAfter): void
{
    header('Retry-After: ' . $retryAfter);

    json_fail(
        'Too many sign-in attempts. Please wait before trying again.',
        429,
        ['retry_after' => $retryAfter]
    );
}

$data = read_json_body(4096);

$email = strtolower(trim((string) ($data['email'] ?? '')));
$password = (string) ($data['password'] ?? '');

if (
    $email === ''
    || strlen($email) > 191
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || $password === ''
    || strlen($password) > 1024
) {
    json_fail('Email and password are required.', 400);
}

$windowMinutes = max(1, (int) config('login_rate_limit.window_minutes', 15));
$identityLimit = max(1, (int) config('login_rate_limit.identity_failures', 5));
$ipLimit = max(1, (int) config('login_rate_limit.ip_failures', 20));
$retentionDays = max(1, (int) config('login_rate_limit.retention_days', 7));

$identityHash = secure_hmac('email:' . $email);
$ipHash = secure_hmac('ip:' . client_ip_address());

$cleanupSql = "
    DELETE FROM login_attempts
    WHERE attempted_at < (UTC_TIMESTAMP() - INTERVAL {$retentionDays} DAY)
";
$conn->query($cleanupSql);

$identityRate = recent_failed_attempts($conn, 'identity_hash', $identityHash, $windowMinutes);
$ipRate = recent_failed_attempts($conn, 'ip_hash', $ipHash, $windowMinutes);

if ($identityRate['count'] >= $identityLimit) {
    rate_limit_response(retry_after_seconds($identityRate['oldest_attempt'], $windowMinutes));
}

if ($ipRate['count'] >= $ipLimit) {
    rate_limit_response(retry_after_seconds($ipRate['oldest_attempt'], $windowMinutes));
}

$statement = $conn->prepare(
    'SELECT id, email_address, password_hash, name, role, status
     FROM users
     WHERE email_address = ?
     LIMIT 1'
);
$statement->bind_param('s', $email);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();

$hashToVerify = $user
    ? (string) $user['password_hash']
    : APESCON_DUMMY_PASSWORD_HASH;

$passwordIsValid = password_verify($password, $hashToVerify);
$accountIsValid = $user
    && hash_equals(strtolower((string) $user['email_address']), $email)
    && (string) $user['status'] === 'active'
    && $passwordIsValid;

if (!$accountIsValid) {
    $knownUserId = $user ? (int) $user['id'] : null;

    record_login_attempt($conn, $identityHash, $ipHash, false, $knownUserId);

    $identityRate = recent_failed_attempts($conn, 'identity_hash', $identityHash, $windowMinutes);
    $ipRate = recent_failed_attempts($conn, 'ip_hash', $ipHash, $windowMinutes);

    if ($identityRate['count'] >= $identityLimit) {
        rate_limit_response(retry_after_seconds($identityRate['oldest_attempt'], $windowMinutes));
    }

    if ($ipRate['count'] >= $ipLimit) {
        rate_limit_response(retry_after_seconds($ipRate['oldest_attempt'], $windowMinutes));
    }

    json_fail('Invalid email address or password.', 401);
}

$userId = (int) $user['id'];

$conn->begin_transaction();

try {
    if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);

        $rehashStatement = $conn->prepare(
            'UPDATE users
             SET password_hash = ?, password_changed_at = UTC_TIMESTAMP()
             WHERE id = ?'
        );
        $rehashStatement->bind_param('si', $newHash, $userId);
        $rehashStatement->execute();
        $rehashStatement->close();
    }

    $updateStatement = $conn->prepare(
        'UPDATE users
         SET last_login_at = UTC_TIMESTAMP()
         WHERE id = ?'
    );
    $updateStatement->bind_param('i', $userId);
    $updateStatement->execute();
    $updateStatement->close();

    clear_identity_failures($conn, $identityHash);
    record_login_attempt($conn, $identityHash, $ipHash, true, $userId);

    $conn->commit();
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}

$csrf = start_authenticated_session($user);

json_ok([
    'role' => (string) $user['role'],
    'name' => (string) $user['name'],
    'email_address' => (string) $user['email_address'],
    'csrf' => $csrf,
]);
