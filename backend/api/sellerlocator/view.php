<?php
require_once __DIR__ . '/../_bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid seller ID.'
    ]);
    exit;
}

$sql = "SELECT
            id,
            seller_code,
            full_name,
            position,
            area_of_distribution,
            market_scope,
            facebook_name,
            facebook_link,
            shopee_shop_name,
            shopee_shop_link,
            tiktok_shop_name,
            tiktok_shop_link,
            lazada_shop_name,
            lazada_shop_link,
            physical_store_address,
            contact_number,
            product_type,
            avatar
        FROM sellers
        WHERE id = ? AND is_active = 1
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare query.'
    ]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();
$stmt->close();

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Seller not found.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $data
]);