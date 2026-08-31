<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';
require_once __DIR__ . '/../../_audit.php';
require_admin_download();

$q        = trim((string)($_GET['q'] ?? ''));
$from     = trim((string)($_GET['from'] ?? ''));
$to       = trim((string)($_GET['to'] ?? ''));
$username = trim((string)($_GET['username'] ?? ''));
$action   = trim((string)($_GET['action'] ?? ''));
$format   = strtolower(trim((string)($_GET['format'] ?? 'csv')));

if (!in_array($format, ['csv', 'xlsx'], true)) {
    $format = 'csv';
}

$uid = (int)($_SESSION['user_id'] ?? 0);

$stmtB = $conn->prepare("
    SELECT branch_id, full_name, username
    FROM users
    WHERE id = ? AND role = 'admin'
    LIMIT 1
");
$stmtB->bind_param("i", $uid);
$stmtB->execute();
$adminRow = $stmtB->get_result()->fetch_assoc();
$stmtB->close();

$branchId = (int)($adminRow['branch_id'] ?? 0);

if ($branchId <= 0) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Admin branch is not set.";
    exit;
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
    $types  .= "ss";
    $vals[]  = $from . " 00:00:00";
    $vals[]  = $to . " 00:00:00";
} elseif ($fromOk) {
    $where[] = "a.created_at >= ?";
    $types  .= "s";
    $vals[]  = $from . " 00:00:00";
} elseif ($toOk) {
    $where[] = "a.created_at < DATE_ADD(?, INTERVAL 1 DAY)";
    $types  .= "s";
    $vals[]  = $to . " 00:00:00";
}

$whereSql = "WHERE " . implode(" AND ", $where);

$sql = "
    SELECT
        a.id,
        a.created_at,
        u.full_name,
        u.username,
        u.role,
        a.ip_address,
        a.user_agent,
        a.action,
        a.entity_type,
        a.entity_id,
        a.meta_json
    FROM audit_logs a
    LEFT JOIN users u ON u.id = a.user_id
    {$whereSql}
    ORDER BY a.created_at DESC, a.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$vals);
$stmt->execute();
$res = $stmt->get_result();

$headers = [
    'Audit ID',
    'Created At',
    'Full Name',
    'Username',
    'Role',
    'IP Address',
    'User Agent',
    'Action',
    'Entity Type',
    'Entity ID',
    'Summary',
    'Meta JSON',
];

$rows = [];
while ($r = $res->fetch_assoc()) {
    $summary = '';

    if (!empty($r['meta_json'])) {
        $decoded = json_decode((string)$r['meta_json'], true);
        if (is_array($decoded)) {
            $summary = (string)($decoded['message'] ?? '');
            if ($summary === '') {
                $parts = [];
                foreach (['plate_no', 'queue_no', 'status', 'payment_method', 'reason'] as $k) {
                    if (isset($decoded[$k]) && $decoded[$k] !== '') {
                        $parts[] = $k . ': ' . $decoded[$k];
                    }
                }
                $summary = implode(' | ', $parts);
            }
        }
    }

    $rows[] = [
        $r['id'],
        $r['created_at'],
        $r['full_name'],
        $r['username'],
        $r['role'],
        $r['ip_address'],
        $r['user_agent'],
        $r['action'],
        $r['entity_type'],
        $r['entity_id'],
        $summary,
        $r['meta_json'],
    ];
}
$stmt->close();

// log the export itself
audit_write($conn, [
    'branch_id'   => $branchId,
    'user_id'     => $uid,
    'action'      => 'AUDIT_EXPORT',
    'entity_type' => 'audit_logs',
    'entity_id'   => 0,
    'meta' => [
        'message' => 'Exported audit trail',
        'format'  => $format,
        'filters' => [
            'q'        => $q,
            'from'     => $from,
            'to'       => $to,
            'username' => $username,
            'action'   => $action,
        ],
        'row_count' => count($rows),
    ],
]);

$fnameBase = 'audit_trail_branch_' . $branchId . '_' . date('Ymd_His');

$wantXlsx = ($format === 'xlsx');
$canXlsx = false;

$autoload = __DIR__ . '/../../vendor/autoload.php';
if ($wantXlsx) {
    if (file_exists($autoload)) {
        require_once $autoload;
    }
    $canXlsx = class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class);
}

if ($wantXlsx && $canXlsx) {
    $sheetData = [];
    $sheetData[] = $headers;

    foreach ($rows as $row) {
        $sheetData[] = $row;
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Audit Trail');
    $sheet->fromArray($sheetData, null, 'A1', true);
    $sheet->freezePane('A2');

    $highestCol = $sheet->getHighestColumn();
    foreach (range('A', $highestCol) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename={$fnameBase}.xlsx");
    header('Cache-Control: max-age=0');
    header('X-Content-Type-Options: nosniff');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename={$fnameBase}.csv");
header('X-Content-Type-Options: nosniff');

$out = fopen('php://output', 'w');
fputcsv($out, $headers);

foreach ($rows as $row) {
    fputcsv($out, $row);
}

fclose($out);
exit;