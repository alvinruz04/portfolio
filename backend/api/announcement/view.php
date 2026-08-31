<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid announcement ID.',
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        image,
        image_original_name,
        created_at,
        updated_at
    FROM announcements
    WHERE id = ? AND is_active = 1
    LIMIT 1
");

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare announcement view query.',
    ]);
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode([
        'success' => false,
        'message' => 'Announcement not found.',
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $row,
]);