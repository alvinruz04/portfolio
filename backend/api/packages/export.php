<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';
require_once __DIR__ . '/../../_audit.php';

require_admin_download();

$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));

$uid = (int)($_SESSION['user_id'] ?? 0);
$stmtB = $conn->prepare("SELECT branch_id FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
$stmtB->bind_param("i", $uid);
$stmtB->execute();
$branch_id = (int)(($stmtB->get_result()->fetch_assoc()['branch_id'] ?? 0));
$stmtB->close();

if ($branch_id <= 0) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Admin branch is not set.';
    exit;
}

$where = ["p.branch_id = ?"];
$types = 'i';
$vals = [$branch_id];

if ($q !== '') {
    if (mb_strlen($q) > 150) $q = mb_substr($q, 0, 150);
    $where[] = "(p.name LIKE ?)";
    $like = "%{$q}%";
    $types .= 's';
    $vals[] = $like;
}

if ($status === 'active') {
    $where[] = "p.active = 1";
} elseif ($status === 'inactive') {
    $where[] = "p.active = 0";
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT
        p.id,
        p.name,
        p.active,
        p.created_at,
        pp.vehicle_type,
        pp.price
    FROM packages p
    LEFT JOIN package_prices pp ON pp.package_id = p.id
    {$whereSql}
    ORDER BY p.created_at DESC, p.id DESC, pp.vehicle_type ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$vals);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [
        (int)$r['id'],
        (string)$r['name'],
        ((int)$r['active'] === 1) ? 'active' : 'inactive',
        (string)($r['vehicle_type'] ?? ''),
        isset($r['price']) ? number_format((float)$r['price'], 2, '.', '') : '',
        (string)$r['created_at'],
    ];
}
$stmt->close();

/** Optional branch name */
$branchName = '';
$b = $conn->prepare("SELECT name FROM branches WHERE id = ? LIMIT 1");
$b->bind_param("i", $branch_id);
$b->execute();
$brow = $b->get_result()->fetch_assoc();
$b->close();
$branchName = (string)($brow['name'] ?? '');

if ($branch_id > 0 && $uid > 0) {
    audit_write($conn, [
        'branch_id'   => $branch_id,
        'user_id'     => $uid,
        'action'      => 'PACKAGE_EXPORT',
        'entity_type' => 'package',
        'entity_id'   => 0,
        'meta' => [
            'message' => 'Exported packages report',
            'filters' => [
                'q' => $q,
                'status' => $status,
            ],
            'row_count' => count($rows),
            'target_branch_id' => $branch_id,
            'target_branch_name' => $branchName,
            'performed_by_role' => (string)($_SESSION['role'] ?? ''),
            'performed_by_username' => (string)($_SESSION['username'] ?? ''),
            'format' => 'csv',
        ],
    ]);
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=packages_report.csv');
header('X-Content-Type-Options: nosniff');

$out = fopen('php://output', 'w');
fputcsv($out, ['Package ID', 'Package Name', 'Active', 'Vehicle Type', 'Price', 'Created At']);

foreach ($rows as $row) {
    fputcsv($out, $row);
}

fclose($out);
exit;