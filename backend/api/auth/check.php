<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_method('GET');

$user = session_user($conn);

if (!$user) {
    json_ok([
        'authenticated' => false,
    ]);
}

json_ok([
    'authenticated' => true,
    'user_id' => $user['id'],
    'role' => $user['role'],
    'name' => $user['name'],
    'email_address' => $user['email_address'],
]);
