<?php
require_once __DIR__ . '/../_bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

// Inputs
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(20, max(5, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

$q = trim((string)($_GET['q'] ?? ''));
$marketScope = trim((string)($_GET['market_scope'] ?? ''));
$areaOfDistribution = trim((string)($_GET['area_of_distribution'] ?? ''));
$productType = trim((string)($_GET['product_type'] ?? ''));

$where = ["is_active = 1"];
$types = "";
$vals = [];

// Search by seller name or seller code
if ($q !== '') {
    $where[] = "(full_name LIKE ? OR seller_code LIKE ?)";
    $like = "%{$q}%";
    $types .= "ss";
    $vals[] = $like;
    $vals[] = $like;
}

// Filter: market scope
if ($marketScope !== '') {
    $where[] = "market_scope = ?";
    $types .= "s";
    $vals[] = $marketScope;
}

// Filter: area of distribution
if ($areaOfDistribution !== '') {
    $where[] = "area_of_distribution = ?";
    $types .= "s";
    $vals[] = $areaOfDistribution;
}

// Filter: product type
if ($productType !== '') {
    $where[] = "product_type = ?";
    $types .= "s";
    $vals[] = $productType;
}

$whereSql = "WHERE " . implode(" AND ", $where);

// Total count
$countSql = "SELECT COUNT(*) AS total FROM sellers $whereSql";
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
$total = 0;
$resTotal = $stmt->get_result();
if ($rowTotal = $resTotal->fetch_assoc()) {
    $total = (int)$rowTotal['total'];
}
$stmt->close();

// Main data
$sql = "SELECT 
            id,
            seller_code,
            full_name,
            position,
            contact_number,
            area_of_distribution
        FROM sellers
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

// Dropdown options
$marketScopes = [];
$areaOptions = [];
$productOptions = [];

// Market Scope options
$res1 = $conn->query("
    SELECT DISTINCT market_scope
    FROM sellers
    WHERE is_active = 1 AND market_scope IS NOT NULL AND market_scope <> ''
    ORDER BY market_scope ASC
");
if ($res1) {
    while ($r = $res1->fetch_assoc()) {
        $marketScopes[] = $r['market_scope'];
    }
    $res1->free();
}

// Area of Distribution options
$res2 = $conn->query("
    SELECT DISTINCT area_of_distribution
    FROM sellers
    WHERE is_active = 1 AND area_of_distribution IS NOT NULL AND area_of_distribution <> ''
    ORDER BY area_of_distribution ASC
");
if ($res2) {
    while ($r = $res2->fetch_assoc()) {
        $areaOptions[] = $r['area_of_distribution'];
    }
    $res2->free();
}

// Product options
$res3 = $conn->query("
    SELECT DISTINCT product_type
    FROM sellers
    WHERE is_active = 1 AND product_type IS NOT NULL AND product_type <> ''
    ORDER BY product_type ASC
");
if ($res3) {
    while ($r = $res3->fetch_assoc()) {
        $productOptions[] = $r['product_type'];
    }
    $res3->free();
}

echo json_encode([
    'success' => true,
    'rows' => $rows,
    'page' => $page,
    'total' => $total,
    'totalPages' => max(1, (int)ceil($total / $limit)),
    'marketScopes' => $marketScopes,
    'areaOptions' => $areaOptions,
    'productOptions' => $productOptions,
]);