<?php
require_once __DIR__ . '/../_bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

// Inputs
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(20, max(6, (int)($_GET['limit'] ?? 9)));
$offset = ($page - 1) * $limit;

$q = trim((string)($_GET['q'] ?? ''));
$platform = trim((string)($_GET['platform'] ?? ''));
$productType = trim((string)($_GET['product_type'] ?? ''));

$where = ["is_active = 1"];
$types = "";
$vals = [];

// Search by shop name only
if ($q !== '') {
    $where[] = "shop_name LIKE ?";
    $like = "%{$q}%";
    $types .= "s";
    $vals[] = $like;
}

// Filter by platform
if ($platform !== '') {
    $where[] = "platform = ?";
    $types .= "s";
    $vals[] = $platform;
}

// Filter by product type
if ($productType !== '') {
    $where[] = "product_type = ?";
    $types .= "s";
    $vals[] = $productType;
}

$whereSql = "WHERE " . implode(" AND ", $where);

// Count
$countSql = "SELECT COUNT(*) AS total FROM fake_sellers $whereSql";
$stmt = $conn->prepare($countSql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare count query.'
    ]);
    exit;
}

if ($types !== '') {
    $stmt->bind_param($types, ...$vals);
}

$stmt->execute();
$resTotal = $stmt->get_result();
$total = 0;

if ($row = $resTotal->fetch_assoc()) {
    $total = (int)$row['total'];
}
$stmt->close();

// Main list
$sql = "SELECT
            id,
            shop_name,
            shop_link,
            platform,
            product_type,
            created_at
        FROM fake_sellers
        $whereSql
        ORDER BY created_at DESC, id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare list query.'
    ]);
    exit;
}

$types2 = $types . "ii";
$vals2 = $vals;
$vals2[] = $limit;
$vals2[] = $offset;

$stmt->bind_param($types2, ...$vals2);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
$stmt->close();

// Dropdowns
$platformOptions = [];
$productOptions = [];

$res1 = $conn->query("
    SELECT DISTINCT platform
    FROM fake_sellers
    WHERE is_active = 1 AND platform IS NOT NULL AND platform <> ''
    ORDER BY platform ASC
");
if ($res1) {
    while ($r = $res1->fetch_assoc()) {
        $platformOptions[] = $r['platform'];
    }
    $res1->free();
}

$res2 = $conn->query("
    SELECT DISTINCT product_type
    FROM fake_sellers
    WHERE is_active = 1 AND product_type IS NOT NULL AND product_type <> ''
    ORDER BY product_type ASC
");
if ($res2) {
    while ($r = $res2->fetch_assoc()) {
        $productOptions[] = $r['product_type'];
    }
    $res2->free();
}

echo json_encode([
    'success' => true,
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => max(1, (int)ceil($total / $limit)),
    'platformOptions' => $platformOptions,
    'productOptions' => $productOptions,
]);