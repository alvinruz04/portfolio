<?php
// Dynamic CORS headers
$allowed_origins = [
    "http://localhost:5173",
    "https://balancestudio.ph"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Credentials: true");
}

header("Content-Type: application/json");

session_start();
require_once '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$adminId = (int) $_SESSION['admin_id'];
$oldPassword = trim($data['oldPassword'] ?? '');
$newPassword = trim($data['newPassword'] ?? '');
$confirmPassword = trim($data['confirmPassword'] ?? '');

$errors = [];

// Validation
if (!$oldPassword) $errors['oldPassword'] = 'Old password is required.';
if (!$newPassword || strlen($newPassword) < 6) {
    $errors['newPassword'] = 'New password must be at least 6 characters.';
}
if ($newPassword !== $confirmPassword) {
    $errors['confirmPassword'] = 'Passwords do not match.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Fetch current hashed password
$stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$stmt->bind_result($currentHash);
$stmt->fetch();
$stmt->close();

if (!password_verify($oldPassword, $currentHash)) {
    echo json_encode(['success' => false, 'errors' => ['oldPassword' => 'Old password is incorrect.']]);
    exit;
}

// Update with new password
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
$update->bind_param("si", $newHash, $adminId);

if ($update->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
}
