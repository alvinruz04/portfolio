<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_admin();

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

$q            = trim((string)($_GET['q'] ?? ''));
$platform     = trim((string)($_GET['platform'] ?? ''));
$product_type = trim((string)($_GET['product_type'] ?? ''));
$is_active    = trim((string)($_GET['is_active'] ?? ''));
$sort         = (string)($_GET['sort'] ?? 'shop_name');
$dir          = strtolower((string)($_GET['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

$allowedSort = ['id', 'shop_name', 'platform', 'product_type', 'is_active', 'created_at'];
if (!in_array($sort, $allowedSort, true)) {
    $sort = 'shop_name';
}

$where = ['1=1'];
$types = '';
$vals = [];

if ($q !== '') {
    if (mb_strlen($q) > 100) $q = mb_substr($q, 0, 100);
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

/** total */
$countSql = "SELECT COUNT(*) AS total FROM fake_sellers {$whereSql}";
$stmt = $conn->prepare($countSql);
if (!$stmt) {
    json_fail('Failed to prepare count query: ' . $conn->error, 500);
}
if ($types !== '') {
    $stmt->bind_param($types, ...$vals);
}
$stmt->execute();
$total = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

/** rows */
$dataSql = "
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
    ORDER BY {$sort} {$dir}
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($dataSql);
if (!$stmt) {
    json_fail('Failed to prepare list query: ' . $conn->error, 500);
}

$types2 = $types . 'ii';
$vals2 = $vals;
$vals2[] = $limit;
$vals2[] = $offset;

$stmt->bind_param($types2, ...$vals2);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

json_ok([
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)ceil($total / $limit),
]);