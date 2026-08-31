<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/_upload.php';

require_admin();
require_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    json_fail('Invalid seller ID.', 422);
}

$get = $conn->prepare("SELECT * FROM sellers WHERE id = ? LIMIT 1");
$get->bind_param("i", $id);
$get->execute();
$existing = $get->get_result()->fetch_assoc();
$get->close();

if (!$existing) {
    json_fail('Seller not found.', 404);
}

$seller_code            = trim((string)($_POST['seller_code'] ?? ''));
$full_name              = trim((string)($_POST['full_name'] ?? ''));
$position               = trim((string)($_POST['position'] ?? ''));
$area_of_distribution   = trim((string)($_POST['area_of_distribution'] ?? ''));
$market_scope           = trim((string)($_POST['market_scope'] ?? 'Local'));
$facebook_name          = trim((string)($_POST['facebook_name'] ?? ''));
$facebook_link          = normalize_optional_link($_POST['facebook_link'] ?? '');
$shopee_shop_name       = trim((string)($_POST['shopee_shop_name'] ?? ''));
$shopee_shop_link       = normalize_optional_link($_POST['shopee_shop_link'] ?? '');
$tiktok_shop_name       = trim((string)($_POST['tiktok_shop_name'] ?? ''));
$tiktok_shop_link       = normalize_optional_link($_POST['tiktok_shop_link'] ?? '');
$lazada_shop_name       = trim((string)($_POST['lazada_shop_name'] ?? ''));
$lazada_shop_link       = normalize_optional_link($_POST['lazada_shop_link'] ?? '');
$physical_store_address = trim((string)($_POST['physical_store_address'] ?? ''));
$contact_number         = trim((string)($_POST['contact_number'] ?? ''));
$product_type           = trim((string)($_POST['product_type'] ?? 'All Products'));
$is_active              = isset($_POST['is_active']) ? (int)!!$_POST['is_active'] : 1;

/** REQUIRED */
if ($seller_code === '') json_fail('Seller code is required.', 422);
if ($full_name === '') json_fail('Full name is required.', 422);
if ($position === '') json_fail('Position is required.', 422);
if ($area_of_distribution === '') json_fail('Area of distribution is required.', 422);
if ($contact_number === '') json_fail('Contact number is required.', 422);

/** LENGTH LIMITS */
if (mb_strlen($seller_code) > 20) $seller_code = mb_substr($seller_code, 0, 20);
if (mb_strlen($full_name) > 150) $full_name = mb_substr($full_name, 0, 150);
if (mb_strlen($area_of_distribution) > 255) $area_of_distribution = mb_substr($area_of_distribution, 0, 255);
if (mb_strlen($facebook_name) > 150) $facebook_name = mb_substr($facebook_name, 0, 150);
if (mb_strlen($shopee_shop_name) > 150) $shopee_shop_name = mb_substr($shopee_shop_name, 0, 150);
if (mb_strlen($tiktok_shop_name) > 150) $tiktok_shop_name = mb_substr($tiktok_shop_name, 0, 150);
if (mb_strlen($lazada_shop_name) > 150) $lazada_shop_name = mb_substr($lazada_shop_name, 0, 150);
if (mb_strlen($physical_store_address) > 255) $physical_store_address = mb_substr($physical_store_address, 0, 255);
if (mb_strlen($contact_number) > 20) $contact_number = mb_substr($contact_number, 0, 20);

/** ENUM VALIDATION */
$allowed_positions = ['Depot','Regional Distributor','Provincial Distributor','City Distributor','Reseller'];
if (!in_array($position, $allowed_positions, true)) {
    json_fail('Invalid position selected.', 422);
}

$allowed_market_scope = ['Local', 'International'];
if (!in_array($market_scope, $allowed_market_scope, true)) {
    json_fail('Invalid market scope selected.', 422);
}

$allowed_product_types = ['Beauty White Caps & Skin', 'Shepu', 'All Products'];
if (!in_array($product_type, $allowed_product_types, true)) {
    json_fail('Invalid product type selected.', 422);
}

/** UNIQUE seller_code except self */
$chk = $conn->prepare("SELECT id FROM sellers WHERE seller_code = ? AND id <> ? LIMIT 1");
$chk->bind_param("si", $seller_code, $id);
$chk->execute();

if ($chk->get_result()->num_rows > 0) {
    $chk->close();
    json_fail('Seller code already exists.', 409);
}
$chk->close();

/** AVATAR */
$avatar = (string)($existing['avatar'] ?? '');

if (isset($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    try {
        $newAvatar = save_seller_avatar($_FILES['avatar']);
        delete_seller_avatar_file($avatar);
        $avatar = $newAvatar;
    } catch (Throwable $e) {
        json_fail($e->getMessage(), 422);
    }
}

$stmt = $conn->prepare("
    UPDATE sellers SET
        seller_code = ?,
        full_name = ?,
        position = ?,
        area_of_distribution = ?,
        market_scope = ?,
        facebook_name = ?,
        facebook_link = ?,
        shopee_shop_name = ?,
        shopee_shop_link = ?,
        tiktok_shop_name = ?,
        tiktok_shop_link = ?,
        lazada_shop_name = ?,
        lazada_shop_link = ?,
        physical_store_address = ?,
        contact_number = ?,
        product_type = ?,
        avatar = ?,
        is_active = ?
    WHERE id = ?
");

if (!$stmt) {
    json_fail('Failed to prepare update query: ' . $conn->error, 500);
}

$stmt->bind_param(
    "sssssssssssssssssii",
    $seller_code,
    $full_name,
    $position,
    $area_of_distribution,
    $market_scope,
    $facebook_name,
    $facebook_link,
    $shopee_shop_name,
    $shopee_shop_link,
    $tiktok_shop_name,
    $tiktok_shop_link,
    $lazada_shop_name,
    $lazada_shop_link,
    $physical_store_address,
    $contact_number,
    $product_type,
    $avatar,
    $is_active,
    $id
);

if (!$stmt->execute()) {
    json_fail('Failed to update seller: ' . $stmt->error, 500);
}
$stmt->close();

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => (int)($_SESSION['user_id'] ?? 0),
    'action'      => 'SELLER_UPDATE',
    'entity_type' => 'seller',
    'entity_id'   => $id,
    'meta' => [
        'message' => 'Seller updated',
        'seller_code' => $seller_code,
        'old_full_name' => (string)($existing['full_name'] ?? ''),
        'new_full_name' => $full_name,
        'old_avatar' => (string)($existing['avatar'] ?? ''),
        'new_avatar' => $avatar,
    ],
]);

json_ok([
    'message' => 'Seller updated successfully.',
]);