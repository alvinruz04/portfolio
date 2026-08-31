<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_admin();

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(5, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

$status = trim((string)($_GET['status'] ?? 'pending'));
$q = trim((string)($_GET['q'] ?? ''));

$allowedStatuses = ['pending', 'approved', 'rejected'];
if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
    $status = 'pending';
}

$where = ['1=1'];
$types = '';
$vals = [];

if ($status !== '') {
    $where[] = 'pp.status = ?';
    $types .= 's';
    $vals[] = $status;
}

if ($q !== '') {
    if (mb_strlen($q) > 100) {
        $q = mb_substr($q, 0, 100);
    }

    $like = "%{$q}%";
    $where[] = '(u.name LIKE ? OR u.email_address LIKE ? OR CAST(pp.id AS CHAR) LIKE ?)';
    $types .= 'sss';
    array_push($vals, $like, $like, $like);
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$countSql = "
    SELECT COUNT(*) AS total
    FROM purchase_proofs pp
    INNER JOIN users u ON u.id = pp.user_id
    {$whereSql}
";

$countStmt = $conn->prepare($countSql);
if (!$countStmt) {
    json_fail('Failed to prepare proof count query: ' . $conn->error, 500);
}

if ($types !== '') {
    $countStmt->bind_param($types, ...$vals);
}

$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();

$dataSql = "
    SELECT
        pp.id,
        pp.user_id,
        u.name AS user_name,
        u.email_address,
        u.user_points,
        pp.status,
        pp.points_awarded,
        pp.rejection_reason,
        pp.customer_note,
        pp.original_name,
        pp.mime_type,
        pp.file_size,
        pp.proof_file,
        pp.created_at,
        pp.reviewed_at,
        pp.proof_deleted_at,
        reviewer.name AS reviewed_by_name
    FROM purchase_proofs pp
    INNER JOIN users u ON u.id = pp.user_id
    LEFT JOIN users reviewer ON reviewer.id = pp.reviewed_by
    {$whereSql}
    ORDER BY pp.created_at DESC
    LIMIT ? OFFSET ?
";

$dataStmt = $conn->prepare($dataSql);
if (!$dataStmt) {
    json_fail('Failed to prepare proof list query: ' . $conn->error, 500);
}

$types2 = $types . 'ii';
$vals2 = $vals;
$vals2[] = $limit;
$vals2[] = $offset;

$dataStmt->bind_param($types2, ...$vals2);
$dataStmt->execute();
$res = $dataStmt->get_result();

$rows = [];

while ($row = $res->fetch_assoc()) {
    $row['id'] = (int)$row['id'];
    $row['user_id'] = (int)$row['user_id'];
    $row['user_points'] = (int)$row['user_points'];
    $row['points_awarded'] = (int)$row['points_awarded'];
    $row['file_size'] = (int)$row['file_size'];
    $row['proof_available'] = !empty($row['proof_file']) && $row['status'] === 'pending';

    unset($row['proof_file']);

    $rows[] = $row;
}

$dataStmt->close();

json_ok([
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => (int)max(1, ceil($total / $limit)),
]);