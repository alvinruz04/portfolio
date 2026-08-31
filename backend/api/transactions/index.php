<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

require_method('GET');
require_admin();

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(50, max(5, (int) ($_GET['limit'] ?? 15)));
$offset = ($page - 1) * $limit;
$q = trim((string) ($_GET['q'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$customerType = trim((string) ($_GET['customer_type'] ?? ''));
$paymentStatus = trim((string) ($_GET['payment_status'] ?? ''));
$serviceTypeId = max(0, (int) ($_GET['service_type_id'] ?? 0));

if (mb_strlen($q) > 120) {
    $q = mb_substr($q, 0, 120);
}

$where = ['1=1'];
$types = '';
$values = [];

if ($q !== '') {
    $like = "%{$q}%";
    $where[] = '(t.transaction_number LIKE ? OR c.display_name LIKE ? OR t.contract_number LIKE ? OR t.quotation_number LIKE ?)';
    $types .= 'ssss';
    array_push($values, $like, $like, $like, $like);
}

if (in_array($status, ['draft', 'active', 'completed', 'cancelled'], true)) {
    $where[] = 't.transaction_status = ?';
    $types .= 's';
    $values[] = $status;
}

if (in_array(
    $customerType,
    ['residential', 'commercial', 'industrial', 'institutional', 'government', 'agricultural'],
    true
)) {
    $where[] = 'c.customer_type = ?';
    $types .= 's';
    $values[] = $customerType;
}

if ($serviceTypeId > 0) {
    $where[] = 't.service_type_id = ?';
    $types .= 'i';
    $values[] = $serviceTypeId;
}

$paymentExpression = "
    CASE
        WHEN COALESCE(pay.total_paid, 0) <= 0 THEN 'unpaid'
        WHEN COALESCE(pay.total_paid, 0) < t.contract_amount THEN 'partial'
        ELSE 'paid'
    END
";

if (in_array($paymentStatus, ['unpaid', 'partial', 'paid'], true)) {
    $where[] = "({$paymentExpression}) = ?";
    $types .= 's';
    $values[] = $paymentStatus;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$fromSql = "
    FROM transactions t
    INNER JOIN customers c ON c.id = t.customer_id
    INNER JOIN customer_sites cs ON cs.id = t.customer_site_id
    INNER JOIN service_types st ON st.id = t.service_type_id
    LEFT JOIN (
        SELECT transaction_id, SUM(amount) AS total_paid
        FROM payments
        WHERE status = 'recorded'
        GROUP BY transaction_id
    ) pay ON pay.transaction_id = t.id
    LEFT JOIN (
        SELECT transaction_id, MIN(scheduled_date) AS next_service_date
        FROM service_visits
        WHERE status IN ('scheduled', 'rescheduled')
          AND scheduled_date >= DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR))
        GROUP BY transaction_id
    ) nxt ON nxt.transaction_id = t.id
";

$countStatement = $conn->prepare("SELECT COUNT(*) AS total {$fromSql} {$whereSql}");
$countValues = $values;
tx_bind($countStatement, $types, $countValues);
$countStatement->execute();
$total = (int) ($countStatement->get_result()->fetch_assoc()['total'] ?? 0);
$countStatement->close();

$sql = "
    SELECT
        t.id,
        t.transaction_number,
        t.contract_number,
        t.contract_amount,
        t.contract_start_date,
        t.contract_end_date,
        t.initial_service_date,
        t.warranty_included,
        t.warranty_end_date,
        t.transaction_status,
        t.created_at,
        c.id AS customer_id,
        c.customer_type,
        c.display_name AS customer_name,
        cs.id AS customer_site_id,
        cs.site_name,
        cs.address_line,
        cs.city,
        cs.province,
        st.id AS service_type_id,
        st.name AS service_type_name,
        COALESCE(pay.total_paid, 0) AS total_paid,
        GREATEST(t.contract_amount - COALESCE(pay.total_paid, 0), 0) AS balance,
        {$paymentExpression} AS payment_status,
        nxt.next_service_date
    {$fromSql}
    {$whereSql}
    ORDER BY t.created_at DESC, t.id DESC
    LIMIT ? OFFSET ?
";

$listTypes = $types . 'ii';
$listValues = $values;
$listValues[] = $limit;
$listValues[] = $offset;

$statement = $conn->prepare($sql);
tx_bind($statement, $listTypes, $listValues);
$statement->execute();
$result = $statement->get_result();
$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = [
        'id' => (int) $row['id'],
        'transaction_number' => (string) $row['transaction_number'],
        'contract_number' => $row['contract_number'],
        'contract_amount' => (float) $row['contract_amount'],
        'contract_start_date' => $row['contract_start_date'],
        'contract_end_date' => $row['contract_end_date'],
        'initial_service_date' => $row['initial_service_date'],
        'warranty_included' => (bool) $row['warranty_included'],
        'warranty_end_date' => $row['warranty_end_date'],
        'transaction_status' => (string) $row['transaction_status'],
        'created_at' => $row['created_at'],
        'customer_id' => (int) $row['customer_id'],
        'customer_type' => (string) $row['customer_type'],
        'customer_name' => (string) $row['customer_name'],
        'customer_site_id' => (int) $row['customer_site_id'],
        'site_name' => $row['site_name'],
        'address_line' => (string) $row['address_line'],
        'city' => $row['city'],
        'province' => $row['province'],
        'service_type_id' => (int) $row['service_type_id'],
        'service_type_name' => (string) $row['service_type_name'],
        'total_paid' => (float) $row['total_paid'],
        'balance' => (float) $row['balance'],
        'payment_status' => (string) $row['payment_status'],
        'next_service_date' => $row['next_service_date'],
    ];
}

$statement->close();

$summary = [
    'active_transactions' => 0,
    'outstanding_payments' => 0,
    'visits_due_next_30_days' => 0,
    'visits_this_month' => 0,
];

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM transactions WHERE transaction_status = 'active'"
);
$summary['active_transactions'] = (int) ($result->fetch_assoc()['total'] ?? 0);

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM transactions t
     LEFT JOIN (
        SELECT transaction_id, SUM(amount) AS total_paid
        FROM payments
        WHERE status = 'recorded'
        GROUP BY transaction_id
     ) p ON p.transaction_id = t.id
     WHERE t.transaction_status IN ('draft', 'active', 'completed')
       AND COALESCE(p.total_paid, 0) + 0.00001 < t.contract_amount"
);
$summary['outstanding_payments'] = (int) ($result->fetch_assoc()['total'] ?? 0);

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM service_visits sv
     INNER JOIN transactions t ON t.id = sv.transaction_id
     WHERE t.transaction_status = 'active'
       AND sv.status IN ('scheduled', 'rescheduled')
       AND sv.scheduled_date BETWEEN DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR)) AND DATE_ADD(DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR)), INTERVAL 30 DAY)"
);
$summary['visits_due_next_30_days'] = (int) ($result->fetch_assoc()['total'] ?? 0);

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM service_visits sv
     INNER JOIN transactions t ON t.id = sv.transaction_id
     WHERE t.transaction_status = 'active'
       AND sv.status IN ('scheduled', 'rescheduled', 'completed')
       AND YEAR(sv.scheduled_date) = YEAR(DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR)))
       AND MONTH(sv.scheduled_date) = MONTH(DATE(DATE_ADD(UTC_TIMESTAMP(), INTERVAL 8 HOUR)))"
);
$summary['visits_this_month'] = (int) ($result->fetch_assoc()['total'] ?? 0);

json_ok([
    'transactions' => $transactions,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'pages' => max(1, (int) ceil($total / $limit)),
    ],
    'summary' => $summary,
]);
