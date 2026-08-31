<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';
require_admin_download();

$q            = trim((string)($_GET['q'] ?? ''));
$platform     = trim((string)($_GET['platform'] ?? ''));
$product_type = trim((string)($_GET['product_type'] ?? ''));
$is_active    = trim((string)($_GET['is_active'] ?? ''));

$where = ['1=1'];
$types = '';
$vals = [];

if ($q !== '') {
    if (mb_strlen($q) > 100) {
        $q = mb_substr($q, 0, 100);
    }

    $like = "%{$q}%";
    $where[] = '(shop_name LIKE ? OR shop_link LIKE ?)';
    $types .= 'ss';
    array_push($vals, $like, $like);
}

if ($platform !== '') {
    $where[] = 'platform = ?';
    $types .= 's';
    $vals[] = $platform;
}

if ($product_type !== '') {
    $where[] = 'product_type = ?';
    $types .= 's';
    $vals[] = $product_type;
}

if ($is_active !== '' && ($is_active === '0' || $is_active === '1')) {
    $where[] = 'is_active = ?';
    $types .= 'i';
    $vals[] = (int)$is_active;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT
        id,
        shop_name,
        shop_link,
        platform,
        product_type,
        is_active,
        created_at
    FROM fake_sellers
    {$whereSql}
    ORDER BY created_at DESC
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
header('Content-Disposition: attachment; filename="fake_sellers_report.csv"');
header('X-Content-Type-Options: nosniff');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

fputcsv($out, [
    'ID',
    'Shop Name',
    'Shop Link',
    'Platform',
    'Product Type',
    'Status',
    'Created At',
]);

while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
        $r['id'],
        $r['shop_name'],
        $r['shop_link'],
        $r['platform'],
        $r['product_type'],
        ((int)$r['is_active'] === 1 ? 'active' : 'inactive'),
        $r['created_at'],
    ]);
}

fclose($out);
$stmt->close();
exit;