<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/_image.php';

require_admin();
require_csrf();

$title = trim((string)($_POST['title'] ?? ''));
$is_active = isset($_POST['is_active']) && (string)$_POST['is_active'] === '0' ? 0 : 1;

if ($title === '') {
    json_fail('Announcement title is required.', 422);
}

if (mb_strlen($title) > 150) {
    $title = mb_substr($title, 0, 150);
}

if (!isset($_FILES['image'])) {
    json_fail('Announcement image is required.', 422);
}

try {
    $upload = announcement_store_image($_FILES['image']);
} catch (Throwable $e) {
    json_fail($e->getMessage(), 422);
}

$stmt = $conn->prepare("
    INSERT INTO announcements (
        title,
        image,
        image_original_name,
        is_active
    ) VALUES (?, ?, ?, ?)
");

if (!$stmt) {
    announcement_delete_image($upload['filename']);
    json_fail('Failed to prepare create query: ' . $conn->error, 500);
}

$stmt->bind_param(
    "sssi",
    $title,
    $upload['filename'],
    $upload['original_name'],
    $is_active
);

if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    announcement_delete_image($upload['filename']);
    json_fail('Failed to create announcement: ' . $err, 500);
}

$newId = (int)$conn->insert_id;
$stmt->close();

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'ANNOUNCEMENT_CREATE',
    'entity_type' => 'announcement',
    'entity_id'   => $newId,
    'meta' => [
        'message' => 'Announcement created',
        'title' => $title,
        'image' => $upload['filename'],
        'is_active' => $is_active,
    ],
]);

json_ok([
    'id' => $newId,
    'image' => $upload['filename'],
    'message' => 'Announcement created successfully.',
]);