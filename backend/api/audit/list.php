<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_admin();

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
if ($page > 100000) $page = 100000;
$offset = ($page - 1) * $limit;

$q        = trim((string)($_GET['q'] ?? ''));
$from     = trim((string)($_GET['from'] ?? ''));
$to       = trim((string)($_GET['to'] ?? ''));
$username = trim((string)($_GET['username'] ?? ''));
$action   = trim((string)($_GET['action'] ?? ''));

$branchId = current_admin_branch_id($conn);
if ($branchId <= 0) {
    json_fail('Admin branch is not set.', 403);
}

$where = ["a.branch_id = ?"];
$types = "i";
$vals  = [$branchId];

// search
if ($q !== '') {
    if (mb_strlen($q) > 100) $q = mb_substr($q, 0, 100);

    $where[] = "(
        u.full_name LIKE ?
        OR u.username LIKE ?
        OR a.action LIKE ?
        OR a.entity_type LIKE ?
        OR CAST(a.entity_id AS CHAR) LIKE ?
        OR a.meta_json LIKE ?
        OR a.ip_address LIKE ?
        OR a.user_agent LIKE ?
    )";

    $like = "%{$q}%";
    $types .= "ssssssss";
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
    $vals[] = $like;
}

// username filter
if ($username !== '') {
    if (mb_strlen($username) > 100) $username = mb_substr($username, 0, 100);
    $where[] = "u.username = ?";
    $types .= "s";
    $vals[] = $username;
}

// action filter
if ($action !== '') {
    if (mb_strlen($action) > 100) $action = mb_substr($action, 0, 100);
    $where[] = "a.action = ?";
    $types .= "s";
    $vals[] = $action;
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
    $where[] = "a.created_at >= ? AND a.created_at < DATE_ADD(?, INTERVAL 1 DAY)";
    $types .= "ss";
    $vals[] = $from . " 00:00:00";
    $vals[] = $to . " 00:00:00";
} elseif ($fromOk) {
    $where[] = "a.created_at >= ?";
    $types .= "s";
    $vals[] = $from . " 00:00:00";
} elseif ($toOk) {
    $where[] = "a.created_at < DATE_ADD(?, INTERVAL 1 DAY)";
    $types .= "s";
    $vals[] = $to . " 00:00:00";
}

$whereSql = "WHERE " . implode(" AND ", $where);

// total
$countSql = "
    SELECT COUNT(*) AS total
    FROM audit_logs a
    LEFT JOIN users u ON u.id = a.user_id
    {$whereSql}
";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param($types, ...$vals);
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();

// rows
$dataSql = "
    SELECT
        a.id,
        a.branch_id,
        a.user_id,
        u.full_name,
        u.username,
        u.role,
        a.ip_address,
        a.user_agent,
        a.action,
        a.entity_type,
        a.entity_id,
        a.meta_json,
        a.created_at
    FROM audit_logs a
    LEFT JOIN users u ON u.id = a.user_id
    {$whereSql}
    ORDER BY a.created_at DESC, a.id DESC
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

while ($r = $res->fetch_assoc()) {
    $rows[] = [
        'id'          => (int)$r['id'],
        'branch_id'   => (int)$r['branch_id'],
        'user_id'     => (int)$r['user_id'],
        'full_name'   => $r['full_name'],
        'username'    => $r['username'],
        'role'        => $r['role'],
        'ip_address'  => $r['ip_address'],
        'user_agent'  => $r['user_agent'],
        'action'      => $r['action'],
        'entity_type' => $r['entity_type'],
        'entity_id'   => $r['entity_id'] !== null ? (int)$r['entity_id'] : null,
        'meta_json'   => $r['meta_json'],
        'created_at'  => $r['created_at'],
    ];
}
$dataStmt->close();

// dropdown options for this branch
$usernames = [];
$stmtUsers = $conn->prepare("
    SELECT DISTINCT COALESCE(u.username, '') AS username
    FROM audit_logs a
    LEFT JOIN users u ON u.id = a.user_id
    WHERE a.branch_id = ?
    ORDER BY username ASC
");
$stmtUsers->bind_param("i", $branchId);
$stmtUsers->execute();
$resUsers = $stmtUsers->get_result();
while ($r = $resUsers->fetch_assoc()) {
    $value = trim((string)($r['username'] ?? ''));
    if ($value !== '') {
        $usernames[] = $value;
    }
}
$stmtUsers->close();

$actions = [];
$stmtActions = $conn->prepare("
    SELECT DISTINCT action
    FROM audit_logs
    WHERE branch_id = ?
    ORDER BY action ASC
");
$stmtActions->bind_param("i", $branchId);
$stmtActions->execute();
$resActions = $stmtActions->get_result();
while ($r = $resActions->fetch_assoc()) {
    $value = trim((string)($r['action'] ?? ''));
    if ($value !== '') {
        $actions[] = $value;
    }
}
$stmtActions->close();

json_ok([
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)ceil($total / $limit),
    'filters' => [
        'usernames' => $usernames,
        'actions' => $actions,
    ],
]);