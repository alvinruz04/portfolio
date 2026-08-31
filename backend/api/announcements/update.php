<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/_image.php';

require_admin();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    json_fail('Invalid announcement ID.', 422);
}

$get = $conn->prepare("SELECT * FROM announcements WHERE id = ? LIMIT 1");
$get->bind_param("i", $id);
$get->execute();
$existing = $get->get_result()->fetch_assoc();
$get->close();

if (!$existing) {
    json_fail('Announcement not found.', 404);
}

$title = trim((string)($_POST['title'] ?? ''));
$is_active = isset($_POST['is_active']) && (string)$_POST['is_active'] === '0' ? 0 : 1;

if ($title === '') {
    json_fail('Announcement title is required.', 422);
}

if (mb_strlen($title) > 150) {
    $title = mb_substr($title, 0, 150);
}

$image = (string)$existing['image'];
$imageOriginalName = (string)($existing['image_original_name'] ?? '');
$newUploadedImage = null;

$hasNewImage = isset($_FILES['image']) && (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

if ($hasNewImage) {
    try {
        $newUploadedImage = announcement_store_image($_FILES['image']);
        $image = $newUploadedImage['filename'];
        $imageOriginalName = $newUploadedImage['original_name'];
    } catch (Throwable $e) {
        json_fail($e->getMessage(), 422);
    }
}

$stmt = $conn->prepare("
    UPDATE announcements
    SET
        title = ?,
        image = ?,
        image_original_name = ?,
        is_active = ?
    WHERE id = ?
");

if (!$stmt) {
    if ($newUploadedImage) {
        announcement_delete_image($newUploadedImage['filename']);
    }
    json_fail('Failed to prepare update query: ' . $conn->error, 500);
}

$stmt->bind_param(
    "sssii",
    $title,
    $image,
    $imageOriginalName,
    $is_active,
    $id
);

if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();

    if ($newUploadedImage) {
        announcement_delete_image($newUploadedImage['filename']);
    }

    json_fail('Failed to update announcement: ' . $err, 500);
}

$stmt->close();

if ($newUploadedImage && !empty($existing['image'])) {
    announcement_delete_image((string)$existing['image']);
}

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'ANNOUNCEMENT_UPDATE',
    'entity_type' => 'announcement',
    'entity_id'   => $id,
    'meta' => [
        'message' => 'Announcement updated',
        'old_title' => (string)$existing['title'],
        'new_title' => $title,
        'old_image' => (string)$existing['image'],
        'new_image' => $image,
        'is_active' => $is_active,
    ],
]);

json_ok([
    'message' => 'Announcement updated successfully.',
]);