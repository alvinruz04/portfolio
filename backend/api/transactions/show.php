<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

require_method('GET');
require_admin();

$transactionId = max(0, (int) ($_GET['id'] ?? 0));

if ($transactionId <= 0) {
    json_fail('Transaction ID is required.', 422);
}

$statement = $conn->prepare(
    "SELECT
        t.*,
        c.customer_type,
        c.display_name AS customer_name,
        c.contact_person AS customer_contact_person,
        c.contact_number AS customer_contact_number,
        c.email_address AS customer_email_address,
        c.billing_address,
        c.tin,
        cs.site_name,
        cs.address_line,
        cs.barangay,
        cs.city,
        cs.province,
        cs.property_type,
        cs.contact_person AS site_contact_person,
        cs.contact_number AS site_contact_number,
        cs.notes AS site_notes,
        st.code AS service_type_code,
        st.name AS service_type_name,
        COALESCE(pay.total_paid, 0) AS total_paid
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
     WHERE t.id = ?
     LIMIT 1"
);
$statement->bind_param('i', $transactionId);
$statement->execute();
$transaction = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$transaction) {
    json_fail('Transaction not found.', 404);
}

$totalPaid = (float) $transaction['total_paid'];
$contractAmount = (float) $transaction['contract_amount'];
$transaction['id'] = (int) $transaction['id'];
$transaction['customer_id'] = (int) $transaction['customer_id'];
$transaction['customer_site_id'] = (int) $transaction['customer_site_id'];
$transaction['service_type_id'] = (int) $transaction['service_type_id'];
$transaction['contract_amount'] = $contractAmount;
$transaction['quotation_amount'] = $transaction['quotation_amount'] !== null
    ? (float) $transaction['quotation_amount']
    : null;
$transaction['area_coverage'] = $transaction['area_coverage'] !== null
    ? (float) $transaction['area_coverage']
    : null;
$transaction['personnel_count'] = $transaction['personnel_count'] !== null
    ? (int) $transaction['personnel_count']
    : null;
$transaction['follow_up_visits'] = (int) $transaction['follow_up_visits'];
$transaction['custom_interval_days'] = $transaction['custom_interval_days'] !== null
    ? (int) $transaction['custom_interval_days']
    : null;
$transaction['warranty_included'] = (bool) $transaction['warranty_included'];
$transaction['total_paid'] = $totalPaid;
$transaction['balance'] = max(0, round($contractAmount - $totalPaid, 2));
$transaction['payment_status'] = tx_payment_status($contractAmount, $totalPaid);

$pests = [];
$statement = $conn->prepare(
    'SELECT p.id, p.name
     FROM transaction_pests tp
     INNER JOIN pest_types p ON p.id = tp.pest_type_id
     WHERE tp.transaction_id = ?
     ORDER BY p.sort_order ASC, p.name ASC'
);
$statement->bind_param('i', $transactionId);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $pests[] = [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
    ];
}

$statement->close();

$payments = [];
$statement = $conn->prepare(
    'SELECT
        p.id,
        p.amount,
        p.payment_date,
        p.payment_method,
        p.reference_number,
        p.remarks,
        p.status,
        p.void_reason,
        p.voided_at,
        p.created_at,
        u.name AS recorded_by_name,
        vu.name AS voided_by_name
     FROM payments p
     LEFT JOIN users u ON u.id = p.created_by
     LEFT JOIN users vu ON vu.id = p.voided_by
     WHERE p.transaction_id = ?
     ORDER BY p.payment_date DESC, p.id DESC'
);
$statement->bind_param('i', $transactionId);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $row['id'] = (int) $row['id'];
    $row['amount'] = (float) $row['amount'];
    $payments[] = $row;
}

$statement->close();

$visits = [];
$statement = $conn->prepare(
    'SELECT
        sv.id,
        sv.visit_number,
        sv.visit_type,
        sv.title,
        sv.scheduled_date,
        sv.scheduled_time,
        sv.assigned_team,
        sv.service_supervisor,
        sv.generated_from_contract,
        sv.status,
        sv.actual_service_date,
        sv.completion_notes,
        sv.created_at,
        sv.updated_at
     FROM service_visits sv
     WHERE sv.transaction_id = ?
     ORDER BY sv.scheduled_date ASC, sv.scheduled_time ASC, sv.id ASC'
);
$statement->bind_param('i', $transactionId);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $row['id'] = (int) $row['id'];
    $row['visit_number'] = $row['visit_number'] !== null ? (int) $row['visit_number'] : null;
    $row['generated_from_contract'] = (bool) $row['generated_from_contract'];
    $visits[] = $row;
}

$statement->close();

$activity = [];
$statement = $conn->prepare(
    'SELECT
        a.id,
        a.action_code,
        a.description,
        a.created_at,
        u.name AS actor_name
     FROM transaction_activity a
     LEFT JOIN users u ON u.id = a.actor_user_id
     WHERE a.transaction_id = ?
     ORDER BY a.created_at DESC, a.id DESC
     LIMIT 100'
);
$statement->bind_param('i', $transactionId);
$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $row['id'] = (int) $row['id'];
    $activity[] = $row;
}

$statement->close();

json_ok([
    'transaction' => $transaction,
    'pests' => $pests,
    'pest_ids' => array_map(static fn (array $pest): int => $pest['id'], $pests),
    'payments' => $payments,
    'visits' => $visits,
    'activity' => $activity,
]);
