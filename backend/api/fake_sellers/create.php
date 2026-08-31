<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';

require_admin();
require_csrf();

$shop_name    = trim((string)($_POST['shop_name'] ?? ''));
$shop_link    = trim((string)($_POST['shop_link'] ?? ''));
$platform     = trim((string)($_POST['platform'] ?? ''));
$product_type = trim((string)($_POST['product_type'] ?? ''));
$is_active    = isset($_POST['is_active']) ? (int)!!$_POST['is_active'] : 1;

if ($shop_name === '') {
    json_fail('Shop name is required.', 422);
}
if ($shop_link === '') {
    json_fail('Shop link is required.', 422);
}
if ($platform === '') {
    json_fail('Platform is required.', 422);
}
if ($product_type === '') {
    json_fail('Product type is required.', 422);
}

if (mb_strlen($shop_name) > 150) $shop_name = mb_substr($shop_name, 0, 150);
if (mb_strlen($shop_link) > 500) $shop_link = mb_substr($shop_link, 0, 500);

if (!preg_match('#^https?://#i', $shop_link)) {
    $shop_link = 'https://' . $shop_link;
}

$allowed_platforms = ['TikTok', 'Shopee', 'Lazada'];
if (!in_array($platform, $allowed_platforms, true)) {
    json_fail('Invalid platform selected.', 422);
}

$allowed_product_types = [
    'Beauty White Capsule',
    'Beauty White Soap',
    'Beauty White Sunscreen',
    'Shepu Appu',
    'All Products',
];
if (!in_array($product_type, $allowed_product_types, true)) {
    json_fail('Invalid product type selected.', 422);
}

/** Optional duplicate check */
$chk = $conn->prepare("
    SELECT id
    FROM fake_sellers
    WHERE shop_name = ? AND shop_link = ?
    LIMIT 1
");
$chk->bind_param("ss", $shop_name, $shop_link);
$chk->execute();

if ($chk->get_result()->num_rows > 0) {
    $chk->close();
    json_fail('This fake seller already exists.', 409);
}
$chk->close();

$stmt = $conn->prepare("
    INSERT INTO fake_sellers (
        shop_name,
        shop_link,
        platform,
        product_type,
        is_active
    ) VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    json_fail('Failed to prepare create query: ' . $conn->error, 500);
}

$stmt->bind_param(
    "ssssi",
    $shop_name,
    $shop_link,
    $platform,
    $product_type,
    $is_active
);

if (!$stmt->execute()) {
    json_fail('Failed to create fake seller: ' . $stmt->error, 500);
}

$newId = (int)$conn->insert_id;
$stmt->close();

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'FAKE_SELLER_CREATE',
    'entity_type' => 'fake_seller',
    'entity_id'   => $newId,
    'meta' => [
        'message' => 'Fake seller created',
        'shop_name' => $shop_name,
        'shop_link' => $shop_link,
        'platform' => $platform,
        'product_type' => $product_type,
        'is_active' => $is_active,
    ],
]);

json_ok([
    'id' => $newId,
    'message' => 'Fake seller created successfully.',
]);