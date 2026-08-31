<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('POST');

$currentUser = require_admin();
require_csrf();

$userId = (int) $currentUser['id'];

$data = read_json_body(4096);

$name = trim((string) ($data['name'] ?? ''));

/*
|--------------------------------------------------------------------------
| Normalize display name
|--------------------------------------------------------------------------
*/
$normalizedName = preg_replace('/\s+/u', ' ', $name);

if (is_string($normalizedName)) {
    $name = trim($normalizedName);
}

/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/
if ($name === '') {
    json_fail('Display name is required.', 422);
}

$nameLength = mb_strlen($name);

if ($nameLength < 2) {
    json_fail('Display name must contain at least 2 characters.', 422);
}

if ($nameLength > 150) {
    json_fail('Display name must not exceed 150 characters.', 422);
}

/*
 * Reject control characters while still allowing normal names,
 * punctuation, hyphens, apostrophes and Unicode characters.
 */
if (preg_match('/[\x00-\x1F\x7F]/u', $name)) {
    json_fail('Display name contains invalid characters.', 422);
}

/*
|--------------------------------------------------------------------------
| Confirm account still exists
|--------------------------------------------------------------------------
*/
$statement = $conn->prepare(
    'SELECT id, name, email_address, role, status
     FROM users
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param('i', $userId);
$statement->execute();

$existing = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$existing) {
    json_fail('Administrator account not found.', 404);
}

if ((string) $existing['status'] !== 'active') {
    json_fail('This administrator account is not active.', 403);
}

if ((string) $existing['role'] !== 'admin') {
    json_fail('This account is no longer an administrator.', 403);
}

/*
|--------------------------------------------------------------------------
| No-op update
|--------------------------------------------------------------------------
*/
if (hash_equals((string) $existing['name'], $name)) {
    /*
     * Keep the authenticated session synchronized even when
     * the database does not need an update.
     */
    $_SESSION['name'] = $name;
    $_SESSION['last_verified_at'] = time();

    json_ok([
        'message' => 'No profile changes were necessary.',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email_address' => (string) $existing['email_address'],
            'role' => (string) $existing['role'],
            'status' => (string) $existing['status'],
        ],
    ]);
}

/*
|--------------------------------------------------------------------------
| Update ONLY the editable field
|--------------------------------------------------------------------------
|
| Important:
| Email, role, status and ID are never read from the request body.
| Therefore the browser cannot modify those fields through this endpoint.
|
*/
$statement = $conn->prepare(
    'UPDATE users
     SET
        name = ?,
        updated_by = ?,
        updated_at = UTC_TIMESTAMP()
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param(
    'sii',
    $name,
    $userId,
    $userId
);

$statement->execute();
$statement->close();

/*
|--------------------------------------------------------------------------
| Keep current PHP session in sync
|--------------------------------------------------------------------------
*/
$_SESSION['name'] = $name;
$_SESSION['last_verified_at'] = time();

json_ok([
    'message' => 'Account name updated successfully.',
    'user' => [
        'id' => $userId,
        'name' => $name,
        'email_address' => (string) $existing['email_address'],
        'role' => (string) $existing['role'],
        'status' => (string) $existing['status'],
    ],
]);