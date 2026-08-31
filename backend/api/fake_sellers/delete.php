<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';

require_admin();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    json_fail('Invalid fake seller ID.', 422);
}

$get = $conn->prepare("SELECT id, shop_name, shop_link FROM fake_sellers WHERE id = ? LIMIT 1");
$get->bind_param("i", $id);
$get->execute();
$existing = $get->get_result()->fetch_assoc();
$get->close();

if (!$existing) {
    json_fail('Fake seller not found.', 404);
}

$stmt = $conn->prepare("DELETE FROM fake_sellers WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    json_fail('Failed to delete fake seller: ' . $stmt->error, 500);
}
$stmt->close();

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'FAKE_SELLER_DELETE',
    'entity_type' => 'fake_seller',
    'entity_id'   => $id,
    'meta' => [
        'message' => 'Fake seller deleted',
        'shop_name' => (string)($existing['shop_name'] ?? ''),
        'shop_link' => (string)($existing['shop_link'] ?? ''),
    ],
]);

json_ok([
    'message' => 'Fake seller deleted successfully.',
]);