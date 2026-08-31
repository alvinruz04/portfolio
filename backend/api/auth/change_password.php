<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('POST');

$currentUser = require_admin();
require_csrf();

$userId = (int) $currentUser['id'];

$data = read_json_body(8192);

$currentPassword = (string) ($data['current_password'] ?? '');
$newPassword = (string) ($data['new_password'] ?? '');
$confirmPassword = (string) ($data['confirm_password'] ?? '');

/*
|--------------------------------------------------------------------------
| Required fields
|--------------------------------------------------------------------------
*/

if (
    $currentPassword === ''
    || $newPassword === ''
    || $confirmPassword === ''
) {
    json_fail(
        'Please complete all password fields.',
        422
    );
}

/*
|--------------------------------------------------------------------------
| Input size protection
|--------------------------------------------------------------------------
*/

if (strlen($currentPassword) > 1024) {
    json_fail(
        'Current password is invalid.',
        422
    );
}

if (
    strlen($newPassword) > 1024
    || strlen($confirmPassword) > 1024
) {
    json_fail(
        'Password input is too large.',
        422
    );
}

/*
|--------------------------------------------------------------------------
| Confirmation
|--------------------------------------------------------------------------
*/

if (!hash_equals($newPassword, $confirmPassword)) {
    json_fail(
        'New password and confirmation password do not match.',
        422
    );
}

/*
|--------------------------------------------------------------------------
| APESCON password policy
|--------------------------------------------------------------------------
*/

[$passwordValid, $passwordMessage] =
    validate_apescon_password($newPassword);

if (!$passwordValid) {
    json_fail(
        $passwordMessage,
        422
    );
}

/*
|--------------------------------------------------------------------------
| Load current administrator
|--------------------------------------------------------------------------
*/

$statement = $conn->prepare(
    'SELECT
        id,
        password_hash,
        role,
        status
     FROM users
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param(
    'i',
    $userId
);

$statement->execute();

$user = $statement
    ->get_result()
    ->fetch_assoc();

$statement->close();

if (!$user) {
    json_fail(
        'Administrator account not found.',
        404
    );
}

if ((string) $user['status'] !== 'active') {
    json_fail(
        'This administrator account is not active.',
        403
    );
}

if ((string) $user['role'] !== 'admin') {
    json_fail(
        'This account is no longer an administrator.',
        403
    );
}

/*
|--------------------------------------------------------------------------
| Verify current password
|--------------------------------------------------------------------------
*/

if (
    !password_verify(
        $currentPassword,
        (string) $user['password_hash']
    )
) {
    json_fail(
        'Current password is incorrect.',
        422
    );
}

/*
|--------------------------------------------------------------------------
| Prevent current-password reuse
|--------------------------------------------------------------------------
*/

if (
    password_verify(
        $newPassword,
        (string) $user['password_hash']
    )
) {
    json_fail(
        'Your new password must be different from your current password.',
        422
    );
}

/*
|--------------------------------------------------------------------------
| Hash new password
|--------------------------------------------------------------------------
*/

$newHash = password_hash(
    $newPassword,
    PASSWORD_DEFAULT
);

if (!is_string($newHash) || $newHash === '') {
    throw new RuntimeException(
        'Unable to generate the new password hash.'
    );
}

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

$statement = $conn->prepare(
    'UPDATE users
     SET
        password_hash = ?,
        password_changed_at = UTC_TIMESTAMP(),
        updated_by = ?,
        updated_at = UTC_TIMESTAMP()
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param(
    'sii',
    $newHash,
    $userId,
    $userId
);

$statement->execute();
$statement->close();

/*
|--------------------------------------------------------------------------
| Rotate authenticated session ID
|--------------------------------------------------------------------------
|
| The user remains logged in, but the PHP session identifier is replaced.
| Session data such as authentication state and CSRF token is preserved.
|
*/

session_regenerate_id(true);

$_SESSION['last_verified_at'] = time();

/*
|--------------------------------------------------------------------------
| Return actual password-change timestamp
|--------------------------------------------------------------------------
*/

$statement = $conn->prepare(
    'SELECT password_changed_at
     FROM users
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param(
    'i',
    $userId
);

$statement->execute();

$updated = $statement
    ->get_result()
    ->fetch_assoc();

$statement->close();

json_ok([
    'message' => 'Password changed successfully.',
    'password_changed_at' =>
        $updated['password_changed_at'] ?? null,
]);