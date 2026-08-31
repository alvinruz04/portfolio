<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

$userId = (int)($_SESSION['user_id'] ?? 0);
$role = (string)($_SESSION['role'] ?? '');

if ($userId <= 0 || $role !== 'user') {
    json_fail('Unauthorized.', 401);
}

$userStmt = $conn->prepare("
    SELECT id, name, email_address, user_points
    FROM users
    WHERE id = ?
    LIMIT 1
");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$user) {
    json_fail('User not found.', 404);
}

$stmt = $conn->prepare("
    SELECT
        id,
        status,
        points_awarded,
        rejection_reason,
        customer_note,
        original_name,
        file_size,
        created_at,
        reviewed_at,
        proof_deleted_at
    FROM purchase_proofs
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 50
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
$pending = null;

while ($row = $res->fetch_assoc()) {
    $row['id'] = (int)$row['id'];
    $row['points_awarded'] = (int)$row['points_awarded'];
    $row['file_size'] = (int)$row['file_size'];

    if ($row['status'] === 'pending' && $pending === null) {
        $pending = $row;
    }

    $rows[] = $row;
}

$stmt->close();

json_ok([
    'user_points' => (int)$user['user_points'],
    'hasPending' => $pending !== null,
    'pending' => $pending,
    'rows' => $rows,
]);