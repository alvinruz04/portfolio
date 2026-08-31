<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_admin();

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
if ($page > 100000) $page = 100000;
$offset = ($page - 1) * $limit;

$q      = trim((string)($_GET['q'] ?? ''));
$from   = trim((string)($_GET['from'] ?? ''));
$to     = trim((string)($_GET['to'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));

$branchId = current_admin_branch_id($conn);
if ($branchId <= 0) json_fail('Admin branch is not set.', 403);

$where = ["t.branch_id = ?"];
$types = "i";
$vals  = [$branchId];

// search
if ($q !== '') {
    if (mb_strlen($q) > 100) $q = mb_substr($q, 0, 100);
    $where[] = "(
        t.plate_no LIKE ?
        OR t.vehicle_type LIKE ?
        OR t.car_model LIKE ?
        OR t.package_name_snapshot LIKE ?
        OR u.full_name LIKE ?
        OR t.payment_method LIKE ?
        OR CAST(t.id AS CHAR) LIKE ?
    )";
    $like = "%{$q}%";
    $types .= "sssssss";
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
}

// status
$allowedStatuses = ['WAITING', 'WASHING', 'DONE', 'CANCEL_PENDING', 'CANCELLED'];
if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $where[] = "t.status = ?";
    $types .= "s";
    $vals[] = $status;
}

// date range
$fromOk = false;
$toOk   = false;

if ($from !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $from);
    if ($dt && $dt->format('Y-m-d') === $from) $fromOk = true;
}

if ($to !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $to);
    if ($dt && $dt->format('Y-m-d') === $to) $toOk = true;
}

if ($fromOk && $toOk) {
    $where[] = "t.created_at >= ? AND t.created_at < (DATE_ADD(?, INTERVAL 1 DAY))";
    $types  .= "ss";
    $vals[]  = $from . " 00:00:00";
    $vals[]  = $to . " 00:00:00";
} elseif ($fromOk) {
    $where[] = "t.created_at >= ?";
    $types  .= "s";
    $vals[]  = $from . " 00:00:00";
} elseif ($toOk) {
    $where[] = "t.created_at < (DATE_ADD(?, INTERVAL 1 DAY))";
    $types  .= "s";
    $vals[]  = $to . " 00:00:00";
}

$whereSql = "WHERE " . implode(" AND ", $where);

/** total */
$countSql = "
    SELECT COUNT(*) AS total
    FROM transactions t
    LEFT JOIN users u ON u.id = t.cashier_id
    {$whereSql}
";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param($types, ...$vals);
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);

/** summary */
$summarySql = "
    SELECT
        COALESCE(SUM(CASE WHEN t.status = 'DONE' THEN t.total_amount ELSE 0 END), 0) AS done_total,
        COALESCE(SUM(CASE WHEN t.status = 'DONE' AND t.payment_method = 'cash' THEN t.total_amount ELSE 0 END), 0) AS cash_total,
        COALESCE(SUM(CASE WHEN t.status = 'DONE' AND t.payment_method = 'gcash' THEN t.total_amount ELSE 0 END), 0) AS gcash_total,
        COALESCE(SUM(CASE WHEN t.status = 'DONE' AND t.payment_method = 'bank' THEN t.total_amount ELSE 0 END), 0) AS bank_total
    FROM transactions t
    LEFT JOIN users u ON u.id = t.cashier_id
    {$whereSql}
";
$summaryStmt = $conn->prepare($summarySql);
$summaryStmt->bind_param($types, ...$vals);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc() ?: [
    'done_total' => 0,
    'cash_total' => 0,
    'gcash_total' => 0,
    'bank_total' => 0,
];

/** rows */
$dataSql = "
    SELECT
        t.id,
        t.branch_id,
        t.cashier_id,
        u.full_name AS cashier_name,
        t.plate_no,
        t.vehicle_type,
        t.car_model,
        t.package_id,
        t.package_name_snapshot,
        t.package_price_snapshot,
        t.addons_total,
        t.subtotal,
        t.discount_type,
        t.discount_value,
        t.discount_amount,
        t.total_amount,
        t.bay_id,
        t.payment_method,
        t.payment_reference,
        t.queue_no,
        t.status,
        t.cancel_reason,
        t.cancel_requested_by,
        t.cancel_requested_at,
        t.cancel_approved_by,
        t.cancel_approved_at,
        t.cancel_approval_note,
        t.created_at,
        t.started_at,
        t.done_at
    FROM transactions t
    LEFT JOIN users u ON u.id = t.cashier_id
    {$whereSql}
    ORDER BY t.created_at DESC, t.id DESC
    LIMIT ? OFFSET ?
";
$dataStmt = $conn->prepare($dataSql);

$types2 = $types . "ii";
$vals2  = $vals;
$vals2[] = $limit;
$vals2[] = $offset;

$dataStmt->bind_param($types2, ...$vals2);
$dataStmt->execute();

$res = $dataStmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;

json_ok([
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)ceil($total / $limit),
    'summary' => [
        'done_total'  => (float)($summary['done_total'] ?? 0),
        'cash_total'  => (float)($summary['cash_total'] ?? 0),
        'gcash_total' => (float)($summary['gcash_total'] ?? 0),
        'bank_total'  => (float)($summary['bank_total'] ?? 0),
    ],
]);