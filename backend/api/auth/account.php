<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('GET');

$currentUser = require_admin();
$userId = (int) $currentUser['id'];

$statement = $conn->prepare(
    'SELECT
        id,
        email_address,
        name,
        role,
        status,
        last_login_at,
        password_changed_at,
        created_at,
        updated_at
     FROM users
     WHERE id = ?
     LIMIT 1'
);

$statement->bind_param('i', $userId);
$statement->execute();

$user = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$user) {
    json_fail('Administrator account not found.', 404);
}

json_ok([
    'user' => [
        'id' => (int) $user['id'],
        'email_address' => (string) $user['email_address'],
        'name' => (string) $user['name'],
        'role' => (string) $user['role'],
        'status' => (string) $user['status'],
        'last_login_at' => $user['last_login_at'],
        'password_changed_at' => $user['password_changed_at'],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at'],
    ],
]);