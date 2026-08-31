<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/_upload.php';

require_admin();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) json_fail('Invalid seller ID.', 422);

$get = $conn->prepare("SELECT id, full_name, seller_code, avatar FROM sellers WHERE id = ? LIMIT 1");
$get->bind_param("i", $id);
$get->execute();
$existing = $get->get_result()->fetch_assoc();
$get->close();

if (!$existing) json_fail('Seller not found.', 404);

$stmt = $conn->prepare("DELETE FROM sellers WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    json_fail('Failed to delete seller: ' . $stmt->error, 500);
}
$stmt->close();

delete_seller_avatar_file($existing['avatar'] ?? '');

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'SELLER_DELETE',
    'entity_type' => 'seller',
    'entity_id'   => $id,
    'meta' => [
        'message' => 'Seller deleted',
        'seller_code' => (string)($existing['seller_code'] ?? ''),
        'full_name' => (string)($existing['full_name'] ?? ''),
    ],
]);

json_ok(['message' => 'Seller deleted successfully.']);