<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';
require_admin_download();

$q = trim((string)($_GET['q'] ?? ''));
$position = trim((string)($_GET['position'] ?? ''));
$area_of_distribution = trim((string)($_GET['area_of_distribution'] ?? ''));
$product_type = trim((string)($_GET['product_type'] ?? ''));

$where = ['1=1'];
$types = '';
$vals = [];

if ($q !== '') {
    if (mb_strlen($q) > 100) {
        $q = mb_substr($q, 0, 100);
    }

    $like = "%{$q}%";
    $where[] = '(seller_code LIKE ? OR full_name LIKE ? OR area_of_distribution LIKE ?)';
    $types .= 'sss';
    array_push($vals, $like, $like, $like);
}

if ($position !== '') {
    $where[] = 'position = ?';
    $types .= 's';
    $vals[] = $position;
}

if ($area_of_distribution !== '') {
    $where[] = 'area_of_distribution = ?';
    $types .= 's';
    $vals[] = $area_of_distribution;
}

if ($product_type !== '') {
    $where[] = 'product_type = ?';
    $types .= 's';
    $vals[] = $product_type;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT
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
        is_active,
        created_at
    FROM sellers
    {$whereSql}
    ORDER BY full_name ASC, id ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    json_fail_download('Failed to prepare export query: ' . $conn->error, 500);
}

if ($types !== '') {
    $stmt->bind_param($types, ...$vals);
}

$stmt->execute();
$res = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sellers_report.csv"');
header('X-Content-Type-Options: nosniff');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

fputcsv($out, [
    'ID',
    'Seller Code',
    'Full Name',
    'Position',
    'Area of Distribution',
    'Market Scope',
    'Facebook Name',
    'Facebook Link',
    'Shopee Shop Name',
    'Shopee Shop Link',
    'TikTok Shop Name',
    'TikTok Shop Link',
    'Lazada Shop Name',
    'Lazada Shop Link',
    'Physical Store Address',
    'Contact Number',
    'Product Type',
    'Status',
    'Created At'
]);

while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
        $r['id'],
        $r['seller_code'],
        $r['full_name'],
        $r['position'],
        $r['area_of_distribution'],
        $r['market_scope'],
        $r['facebook_name'],
        $r['facebook_link'],
        $r['shopee_shop_name'],
        $r['shopee_shop_link'],
        $r['tiktok_shop_name'],
        $r['tiktok_shop_link'],
        $r['lazada_shop_name'],
        $r['lazada_shop_link'],
        $r['physical_store_address'],
        $r['contact_number'],
        $r['product_type'],
        ((int)$r['is_active'] === 1 ? 'active' : 'inactive'),
        $r['created_at'],
    ]);
}

fclose($out);
$stmt->close();
exit;