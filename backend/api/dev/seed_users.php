<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$users = [
    [
        'email_address' => 'admin@youglowbabemain.ph',
        'name' => 'Main Admin',
        'password' => 'Admin@123',
        'role' => 'admin',
        'status' => 'active',
        'user_points' => 0,
        'created_by' => null,
        'updated_by' => null,
    ],
    [
        'email_address' => 'anna@example.com',
        'name' => 'Anna Dela Cruz',
        'password' => 'User@123',
        'role' => 'user',
        'status' => 'active',
        'user_points' => 120,
        'created_by' => null,
        'updated_by' => null,
    ],
    [
        'email_address' => 'bea@example.com',
        'name' => 'Bea Santos',
        'password' => 'User@123',
        'role' => 'user',
        'status' => 'active',
        'user_points' => 85,
        'created_by' => null,
        'updated_by' => null,
    ],
    [
        'email_address' => 'carla@example.com',
        'name' => 'Carla Reyes',
        'password' => 'User@123',
        'role' => 'user',
        'status' => 'active',
        'user_points' => 240,
        'created_by' => null,
        'updated_by' => null,
    ],
    [
        'email_address' => 'diane@example.com',
        'name' => 'Diane Gomez',
        'password' => 'User@123',
        'role' => 'user',
        'status' => 'active',
        'user_points' => 35,
        'created_by' => null,
        'updated_by' => null,
    ],
];

$checkStmt = $conn->prepare("SELECT id FROM users WHERE email_address = ? LIMIT 1");
$insertStmt = $conn->prepare("
    INSERT INTO users (
        email_address,
        password_hash,
        name,
        role,
        status,
        user_points,
        created_by,
        updated_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$inserted = 0;
$skipped = 0;

foreach ($users as $u) {
    $checkStmt->bind_param("s", $u['email_address']);
    $checkStmt->execute();
    $existing = $checkStmt->get_result()->fetch_assoc();

    if ($existing) {
        $skipped++;
        continue;
    }

    $hash = password_hash($u['password'], PASSWORD_DEFAULT);

    $insertStmt->bind_param(
        "sssssiii",
        $u['email_address'],
        $hash,
        $u['name'],
        $u['role'],
        $u['status'],
        $u['user_points'],
        $u['created_by'],
        $u['updated_by']
    );

    $insertStmt->execute();
    $inserted++;
}

json_ok([
    'message' => 'User seeding completed.',
    'inserted' => $inserted,
    'skipped' => $skipped,
]);