<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$name = trim((string)($data['name'] ?? ''));
$email = strtolower(trim((string)($data['email'] ?? '')));
$password = (string)($data['password'] ?? '');
$confirmPassword = (string)($data['confirm_password'] ?? '');

// Basic validation
if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
    json_fail('All fields are required.', 422);
}

if (mb_strlen($name) > 150) {
    $name = mb_substr($name, 0, 150);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_fail('Please enter a valid email address.', 422);
}

if ($password !== $confirmPassword) {
    json_fail('Password and confirm password do not match.', 422);
}

// Strong password validation from _bootstrap.php
[$ok, $msg] = validate_strong_password($password);
if (!$ok) {
    json_fail($msg, 422);
}

// Check if email already exists
$check = $conn->prepare("
    SELECT id
    FROM users
    WHERE email_address = ?
    LIMIT 1
");
$check->bind_param("s", $email);
$check->execute();
$exists = $check->get_result()->fetch_assoc();
$check->close();

if ($exists) {
    json_fail('Email is already registered.', 409);
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO users (
        email_address,
        password_hash,
        name,
        role,
        status,
        user_points,
        created_by,
        updated_by
    ) VALUES (?, ?, ?, 'user', 'active', 0, NULL, NULL)
");
$stmt->bind_param("sss", $email, $passwordHash, $name);

if (!$stmt->execute()) {
    $stmt->close();
    json_fail('Registration failed. Please try again.', 500);
}
$stmt->close();

json_ok([
    'message' => 'Account created successfully.',
]);