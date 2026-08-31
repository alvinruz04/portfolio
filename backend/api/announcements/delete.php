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

$get = $conn->prepare("SELECT id, title, image FROM announcements WHERE id = ? LIMIT 1");
$get->bind_param("i", $id);
$get->execute();
$existing = $get->get_result()->fetch_assoc();
$get->close();

if (!$existing) {
    json_fail('Announcement not found.', 404);
}

$stmt = $conn->prepare("DELETE FROM announcements WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    json_fail('Failed to delete announcement: ' . $stmt->error, 500);
}

$stmt->close();

announcement_delete_image((string)$existing['image']);

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'ANNOUNCEMENT_DELETE',
    'entity_type' => 'announcement',
    'entity_id'   => $id,
    'meta' => [
        'message' => 'Announcement deleted',
        'title' => (string)$existing['title'],
        'image' => (string)$existing['image'],
    ],
]);

json_ok([
    'message' => 'Announcement deleted successfully.',
]);