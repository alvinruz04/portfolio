<?php
session_start();

// Dynamic CORS headers
$allowed_origins = [
    "http://localhost:5173",
    "https://balancestudio.ph"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");
}

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

header('Content-Type: application/json');

require_once '../db.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Query client by email
$stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check and verify password
if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['clients'] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
        ];

        $_SESSION['client_id'] = $row['id'];
        $_SESSION['client_logged_in'] = true;

        echo json_encode([
            'success' => true,
            'clients' => $_SESSION['clients'],
        ]);
        exit;
    }
}

// Invalid credentials
echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
