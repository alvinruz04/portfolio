<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';
require_admin_download();

$q      = trim((string)($_GET['q'] ?? ''));
$from   = trim((string)($_GET['from'] ?? ''));
$to     = trim((string)($_GET['to'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$format = strtolower(trim((string)($_GET['format'] ?? 'csv')));

if (!in_array($format, ['csv', 'xlsx'], true)) {
    $format = 'csv';
}

$uid = (int)($_SESSION['user_id'] ?? 0);
$stmtB = $conn->prepare("SELECT branch_id FROM users WHERE id=? AND role='admin' LIMIT 1");
$stmtB->bind_param("i", $uid);
$stmtB->execute();
$branchId = (int)(($stmtB->get_result()->fetch_assoc()['branch_id'] ?? 0));

if ($branchId <= 0) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Admin branch is not set.";
    exit;
}

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

$sql = "
    SELECT
        t.id,
        t.created_at,
        t.plate_no,
        t.vehicle_type,
        t.car_model,
        u.full_name AS cashier_name,
        t.package_name_snapshot,
        t.package_price_snapshot,
        t.addons_total,
        t.subtotal,
        t.discount_type,
        t.discount_value,
        t.discount_amount,
        t.total_amount,
        t.payment_method,
        t.payment_reference,
        t.queue_no,
        t.status,
        t.cancel_reason,
        t.cancel_approval_note,
        t.started_at,
        t.done_at
    FROM transactions t
    LEFT JOIN users u ON u.id = t.cashier_id
    {$whereSql}
    ORDER BY t.created_at DESC, t.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$vals);
$stmt->execute();
$res = $stmt->get_result();

$headers = [
    'Transaction ID',
    'Created At',
    'Plate No',
    'Vehicle Type',
    'Car Model',
    'Cashier',
    'Package',
    'Package Price',
    'Add-ons Total',
    'Subtotal',
    'Discount Type',
    'Discount Value',
    'Discount Amount',
    'Total Amount',
    'Payment Method',
    'Payment Reference',
    'Queue No',
    'Status',
    'Cancel Reason',
    'Cancel Approval Note',
    'Started At',
    'Done At',
];

$fnameBase = 'branch_transactions_' . $branchId . '_' . date('Ymd_His');

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

    while ($r = $res->fetch_assoc()) {
        $sheetData[] = [
            $r['id'],
            $r['created_at'],
            $r['plate_no'],
            $r['vehicle_type'],
            $r['car_model'],
            $r['cashier_name'],
            $r['package_name_snapshot'],
            $r['package_price_snapshot'],
            $r['addons_total'],
            $r['subtotal'],
            $r['discount_type'],
            $r['discount_value'],
            $r['discount_amount'],
            $r['total_amount'],
            $r['payment_method'],
            $r['payment_reference'],
            $r['queue_no'],
            $r['status'],
            $r['cancel_reason'],
            $r['cancel_approval_note'],
            $r['started_at'],
            $r['done_at'],
        ];
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Transactions');
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

// fallback / default CSV
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename={$fnameBase}.csv");
header('X-Content-Type-Options: nosniff');

$out = fopen('php://output', 'w');
fputcsv($out, $headers);

while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
        $r['id'],
        $r['created_at'],
        $r['plate_no'],
        $r['vehicle_type'],
        $r['car_model'],
        $r['cashier_name'],
        $r['package_name_snapshot'],
        $r['package_price_snapshot'],
        $r['addons_total'],
        $r['subtotal'],
        $r['discount_type'],
        $r['discount_value'],
        $r['discount_amount'],
        $r['total_amount'],
        $r['payment_method'],
        $r['payment_reference'],
        $r['queue_no'],
        $r['status'],
        $r['cancel_reason'],
        $r['cancel_approval_note'],
        $r['started_at'],
        $r['done_at'],
    ]);
}

fclose($out);
exit;