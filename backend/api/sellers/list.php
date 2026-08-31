<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_admin();

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

$q = trim((string)($_GET['q'] ?? ''));
$position = trim((string)($_GET['position'] ?? ''));
$area_of_distribution = trim((string)($_GET['area_of_distribution'] ?? ''));
$product_type = trim((string)($_GET['product_type'] ?? ''));
$sort = (string)($_GET['sort'] ?? 'full_name');
$dir  = strtolower((string)($_GET['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

$allowedSort = ['id', 'seller_code', 'full_name', 'position', 'area_of_distribution', 'product_type', 'created_at'];
if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';

$where = ['1=1'];
$types = '';
$vals = [];

if ($q !== '') {
    if (mb_strlen($q) > 100) $q = mb_substr($q, 0, 100);
    $like = "%{$q}%";
    $where[] = '(seller_code LIKE ? OR full_name LIKE ?)';
    $types .= 'ss';
    array_push($vals, $like, $like);
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

/** total */
$countSql = "SELECT COUNT(*) AS total FROM sellers {$whereSql}";
$stmt = $conn->prepare($countSql);
if ($types !== '') $stmt->bind_param($types, ...$vals);
$stmt->execute();
$total = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
$stmt->close();

/** rows */
$dataSql = "
    SELECT id, seller_code, full_name, position, area_of_distribution, market_scope,
           facebook_name, facebook_link, shopee_shop_name, shopee_shop_link,
           tiktok_shop_name, tiktok_shop_link, lazada_shop_name, lazada_shop_link,
           physical_store_address, contact_number, product_type, avatar, is_active, created_at
    FROM sellers
    {$whereSql}
    ORDER BY {$sort} {$dir}, id ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($dataSql);
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

/** area dropdown options */
$areas = [];
$areaSql = "
    SELECT DISTINCT area_of_distribution
    FROM sellers
    WHERE area_of_distribution IS NOT NULL
      AND area_of_distribution <> ''
    ORDER BY area_of_distribution ASC
";
$areaStmt = $conn->prepare($areaSql);
$areaStmt->execute();
$areaRes = $areaStmt->get_result();
while ($r = $areaRes->fetch_assoc()) {
    $areas[] = $r['area_of_distribution'];
}
$areaStmt->close();

json_ok([
    'rows' => $rows,
    'areas' => $areas,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)ceil($total / $limit),
]);