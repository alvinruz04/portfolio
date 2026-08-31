<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_admin();

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
if ($page > 100000) $page = 100000;
$offset = ($page - 1) * $limit;

$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$sort = (string)($_GET['sort'] ?? 'created_at');
$dir  = strtolower((string)($_GET['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';

$allowedSort = ['id', 'name', 'active', 'created_at'];
if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';

$branch_id = current_admin_branch_id($conn);
if ($branch_id <= 0) json_fail('Admin branch is not set.', 403);

$where = ["p.branch_id = ?"];
$bindTypes = "i";
$bindValues = [$branch_id];

if ($q !== '') {
    if (mb_strlen($q) > 150) $q = mb_substr($q, 0, 150);
    $where[] = "(p.name LIKE ?)";
    $like = "%{$q}%";
    $bindTypes .= "s";
    $bindValues[] = $like;
}

if ($status !== '') {
    if ($status === 'active') $where[] = "p.active = 1";
    elseif ($status === 'inactive') $where[] = "p.active = 0";
}

$whereSql = "WHERE " . implode(" AND ", $where);

/** Total packages */
$countSql = "SELECT COUNT(*) AS total FROM packages p {$whereSql}";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param($bindTypes, ...$bindValues);
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();

/**
 * Get package rows first (paged)
 * Then fetch prices separately to avoid pagination issues from LEFT JOIN expansion.
 */
$dataSql = "
    SELECT
        p.id,
        p.name,
        p.active,
        p.created_at,
        EXISTS(
            SELECT 1
            FROM transactions t
            WHERE t.package_id = p.id
              AND t.branch_id = p.branch_id
            LIMIT 1
        ) AS is_used
    FROM packages p
    {$whereSql}
    ORDER BY p.{$sort} {$dir}, p.id DESC
    LIMIT ? OFFSET ?
";
$dataStmt = $conn->prepare($dataSql);

$bindTypes2 = $bindTypes . "ii";
$bindValues2 = $bindValues;
$bindValues2[] = $limit;
$bindValues2[] = $offset;

$dataStmt->bind_param($bindTypes2, ...$bindValues2);
$dataStmt->execute();
$res = $dataStmt->get_result();

$rows = [];
$packageIds = [];

while ($r = $res->fetch_assoc()) {
    $id = (int)$r['id'];
    $packageIds[] = $id;

    $rows[$id] = [
        'id' => $id,
        'name' => (string)$r['name'],
        'active' => (int)$r['active'],
        'created_at' => (string)$r['created_at'],
        'is_used' => ((int)$r['is_used'] === 1),
        'prices' => [],
    ];
}
$dataStmt->close();

/** Load prices for the paged packages */
if (!empty($packageIds)) {
    $placeholders = implode(',', array_fill(0, count($packageIds), '?'));
    $types = str_repeat('i', count($packageIds));

    $priceSql = "
        SELECT package_id, vehicle_type, price
        FROM package_prices
        WHERE package_id IN ($placeholders)
        ORDER BY package_id ASC, vehicle_type ASC
    ";
    $priceStmt = $conn->prepare($priceSql);
    $priceStmt->bind_param($types, ...$packageIds);
    $priceStmt->execute();
    $priceRes = $priceStmt->get_result();

    while ($r = $priceRes->fetch_assoc()) {
        $pid = (int)$r['package_id'];
        if (isset($rows[$pid])) {
            $rows[$pid]['prices'][(string)$r['vehicle_type']] = number_format((float)$r['price'], 2, '.', '');
        }
    }
    $priceStmt->close();
}

json_ok([
    'rows' => array_values($rows),
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)ceil($total / max(1, $limit)),
]);