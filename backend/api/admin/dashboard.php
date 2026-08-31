<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_admin();

$branchId = current_admin_branch_id($conn);
if ($branchId <= 0) json_fail('Admin is not assigned to a branch.', 400);

$range = trim((string)($_GET['range'] ?? 'today')); // today|week|month
if (!in_array($range, ['today', 'week', 'month'], true)) $range = 'today';

$tz = 'Asia/Manila'; // adjust if needed

// ---------------------------
// Helpers
// ---------------------------
function build_series(array $keys, array $rawMap): array {
  $out = [];
  foreach ($keys as $k) $out[] = (float)($rawMap[$k] ?? 0);
  return $out;
}

function stmt_bind_and_fetch(mysqli $conn, string $sql, string $types, array $vals): array {
  $stmt = $conn->prepare($sql);
  if (!$stmt) json_fail('DB prepare failed.', 500);
  if ($types !== '') $stmt->bind_param($types, ...$vals);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = [];
  while ($row = $res->fetch_assoc()) $rows[] = $row;
  return $rows;
}

// ---------------------------
// Date window per range
// ---------------------------
$labels = [];
$rawMap = []; // label => total_sales
$startSql = '';
$endSql   = '';
$title    = '';

if ($range === 'today') {
  // 24 hours of today
  $title = 'Today (hourly)';

  // labels: 00..23
  for ($h = 0; $h < 24; $h++) $labels[] = str_pad((string)$h, 2, '0', STR_PAD_LEFT);

  // query sums per hour
  $sql = "
    SELECT LPAD(HOUR(created_at), 2, '0') AS h, COALESCE(SUM(total_amount),0) AS total
    FROM transactions
    WHERE branch_id = ?
      AND status = 'DONE'
      AND created_at >= CURDATE()
      AND created_at < (CURDATE() + INTERVAL 1 DAY)
    GROUP BY HOUR(created_at)
    ORDER BY HOUR(created_at)
  ";
  $rows = stmt_bind_and_fetch($conn, $sql, "i", [$branchId]);
  foreach ($rows as $r) $rawMap[(string)$r['h']] = (float)$r['total'];

} elseif ($range === 'week') {
  $title = 'This week (last 7 days)';

  // last 7 days incl today: D-6 ... today
  // labels: YYYY-MM-DD
  $d = new DateTime('now', new DateTimeZone($tz));
  $d->setTime(0,0,0);
  $start = (clone $d)->modify('-6 days');
  for ($i=0; $i<7; $i++) {
    $labels[] = $start->format('Y-m-d');
    $start->modify('+1 day');
  }

  $sql = "
    SELECT DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS total
    FROM transactions
    WHERE branch_id = ?
      AND status = 'DONE'
      AND created_at >= (CURDATE() - INTERVAL 6 DAY)
      AND created_at < (CURDATE() + INTERVAL 1 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
  ";
  $rows = stmt_bind_and_fetch($conn, $sql, "i", [$branchId]);
  foreach ($rows as $r) $rawMap[(string)$r['d']] = (float)$r['total'];

} else { // month
  $title = 'This month (daily)';

  // labels: every day of current month
  $now = new DateTime('now', new DateTimeZone($tz));
  $first = new DateTime($now->format('Y-m-01'), new DateTimeZone($tz));
  $nextMonth = (clone $first)->modify('+1 month');

  $cursor = clone $first;
  while ($cursor < $nextMonth) {
    $labels[] = $cursor->format('Y-m-d');
    $cursor->modify('+1 day');
  }

  $sql = "
    SELECT DATE(created_at) AS d, COALESCE(SUM(total_amount),0) AS total
    FROM transactions
    WHERE branch_id = ?
      AND status = 'DONE'
      AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
      AND created_at <  (DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
  ";
  $rows = stmt_bind_and_fetch($conn, $sql, "i", [$branchId]);
  foreach ($rows as $r) $rawMap[(string)$r['d']] = (float)$r['total'];
}

$salesSeries = build_series($labels, $rawMap);

// ---------------------------
// Stats cards (DONE only for totals)
// ---------------------------
$todayDone = stmt_bind_and_fetch($conn, "
  SELECT
    COUNT(*) AS orders,
    COALESCE(SUM(total_amount),0) AS sales
  FROM transactions
  WHERE branch_id = ?
    AND status = 'DONE'
    AND created_at >= CURDATE()
    AND created_at < (CURDATE() + INTERVAL 1 DAY)
", "i", [$branchId])[0] ?? ['orders'=>0,'sales'=>0];

$weekDone = stmt_bind_and_fetch($conn, "
  SELECT
    COUNT(*) AS orders,
    COALESCE(SUM(total_amount),0) AS sales
  FROM transactions
  WHERE branch_id = ?
    AND status = 'DONE'
    AND created_at >= (CURDATE() - INTERVAL 6 DAY)
    AND created_at < (CURDATE() + INTERVAL 1 DAY)
", "i", [$branchId])[0] ?? ['orders'=>0,'sales'=>0];

$monthDone = stmt_bind_and_fetch($conn, "
  SELECT
    COUNT(*) AS orders,
    COALESCE(SUM(total_amount),0) AS sales
  FROM transactions
  WHERE branch_id = ?
    AND status = 'DONE'
    AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    AND created_at <  (DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH)
", "i", [$branchId])[0] ?? ['orders'=>0,'sales'=>0];

// ---------------------------
// Top packages (this month, DONE only)
// ---------------------------
$topPackages = stmt_bind_and_fetch($conn, "
  SELECT
    package_name_snapshot AS name,
    COUNT(*) AS cnt
  FROM transactions
  WHERE branch_id = ?
    AND status = 'DONE'
    AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    AND created_at <  (DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH)
  GROUP BY package_name_snapshot
  ORDER BY cnt DESC, name ASC
  LIMIT 5
", "i", [$branchId]);

// ---------------------------
// Recent transactions (latest 10, all statuses)
// ---------------------------
$recent = stmt_bind_and_fetch($conn, "
  SELECT
    t.id,
    t.plate_no,
    t.package_name_snapshot,
    t.status,
    t.payment_method,
    t.total_amount,
    t.created_at,
    COALESCE(u.full_name, u.username, '—') AS cashier_name
  FROM transactions t
  LEFT JOIN users u ON u.id = t.cashier_id
  WHERE t.branch_id = ?
  ORDER BY t.id DESC
  LIMIT 10
", "i", [$branchId]);

// ---------------------------
// Branch resources
// ---------------------------
$crewCount = stmt_bind_and_fetch($conn, "
  SELECT COUNT(*) AS cnt
  FROM crews
  WHERE branch_id = ? AND active = 1
", "i", [$branchId])[0]['cnt'] ?? 0;

$bayCount = stmt_bind_and_fetch($conn, "
  SELECT COUNT(*) AS cnt
  FROM bays
  WHERE branch_id = ? AND active = 1
", "i", [$branchId])[0]['cnt'] ?? 0;

$cashierCount = stmt_bind_and_fetch($conn, "
  SELECT COUNT(*) AS cnt
  FROM users
  WHERE branch_id = ? AND role = 'cashier' AND active = 1
", "i", [$branchId])[0]['cnt'] ?? 0;

json_ok([
  'branch_id' => $branchId,

  'range' => $range,
  'sales_chart' => [
    'title' => $title,
    'labels' => $labels,
    'series' => $salesSeries,
  ],

  'totals' => [
    'today_sales' => (float)$todayDone['sales'],
    'week_sales'  => (float)$weekDone['sales'],
    'month_sales' => (float)$monthDone['sales'],
  ],

  'stats' => [
    'today_done_orders' => (int)$todayDone['orders'],
    'month_done_orders' => (int)$monthDone['orders'],
    'crew_count'        => (int)$crewCount,
    'bay_count'         => (int)$bayCount,
    'cashier_count'     => (int)$cashierCount,
  ],

  'top_packages' => $topPackages,
  'recent' => $recent,
]);