<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../transactions/_helpers.php';

require_method('GET');
require_admin();

$businessToday = new DateTimeImmutable(tx_business_today(), new DateTimeZone('Asia/Manila'));
$start = tx_date($_GET['start'] ?? null, false, 'Start date')
    ?? $businessToday->format('Y-m-01');
$end = tx_date($_GET['end'] ?? null, false, 'End date')
    ?? (new DateTimeImmutable($start, new DateTimeZone('Asia/Manila')))->format('Y-m-t');

if ($end < $start) {
    json_fail('End date cannot be earlier than start date.', 422);
}

$includeDrafts = tx_bool($_GET['include_drafts'] ?? false) === 1;
$statusClause = $includeDrafts
    ? "t.transaction_status IN ('draft', 'active')"
    : "t.transaction_status = 'active'";

$statement = $conn->prepare(
    "SELECT
        sv.id,
        sv.transaction_id,
        sv.visit_number,
        sv.visit_type,
        sv.title,
        sv.scheduled_date,
        sv.scheduled_time,
        sv.assigned_team,
        sv.service_supervisor,
        sv.status,
        t.transaction_number,
        t.contract_number,
        c.display_name AS customer_name,
        c.customer_type,
        cs.site_name,
        cs.address_line,
        cs.city,
        cs.province,
        st.name AS service_type_name
     FROM service_visits sv
     INNER JOIN transactions t ON t.id = sv.transaction_id
     INNER JOIN customers c ON c.id = t.customer_id
     INNER JOIN customer_sites cs ON cs.id = t.customer_site_id
     INNER JOIN service_types st ON st.id = t.service_type_id
     WHERE {$statusClause}
       AND sv.scheduled_date BETWEEN ? AND ?
     ORDER BY sv.scheduled_date ASC, sv.scheduled_time ASC, sv.id ASC"
);
$statement->bind_param('ss', $start, $end);
$statement->execute();
$result = $statement->get_result();
$visits = [];

while ($row = $result->fetch_assoc()) {
    $row['id'] = (int) $row['id'];
    $row['transaction_id'] = (int) $row['transaction_id'];
    $row['visit_number'] = $row['visit_number'] !== null ? (int) $row['visit_number'] : null;
    $visits[] = $row;
}

$statement->close();

json_ok([
    'start' => $start,
    'end' => $end,
    'visits' => $visits,
]);
