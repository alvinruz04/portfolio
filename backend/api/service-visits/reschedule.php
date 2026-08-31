<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../transactions/_helpers.php';

require_method('POST');
require_admin();
require_csrf();

$data = read_json_body(8192);
$userId = current_user_id();
$visitId = max(0, (int) ($data['visit_id'] ?? 0));
$newDate = tx_date($data['scheduled_date'] ?? null, true, 'New service date');
$newTime = tx_time($data['scheduled_time'] ?? null);
$reason = tx_string($data['reason'] ?? '', 500);

if ($visitId <= 0) {
    json_fail('Service visit ID is required.', 422);
}

if ($reason === '') {
    json_fail('Rescheduling reason is required.', 422);
}

$statement = $conn->prepare(
    'SELECT sv.*, t.transaction_status
     FROM service_visits sv
     INNER JOIN transactions t ON t.id = sv.transaction_id
     WHERE sv.id = ?
     LIMIT 1'
);
$statement->bind_param('i', $visitId);
$statement->execute();
$visit = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$visit) {
    json_fail('Service visit not found.', 404);
}

if ((string) $visit['transaction_status'] === 'cancelled') {
    json_fail('A visit under a cancelled transaction cannot be rescheduled.', 409);
}

if (in_array((string) $visit['status'], ['completed', 'cancelled'], true)) {
    json_fail('This service visit can no longer be rescheduled.', 409);
}

$oldDate = (string) $visit['scheduled_date'];

$conn->begin_transaction();

try {
    tx_db_update_by_id($conn, 'service_visits', $visitId, [
        'scheduled_date' => $newDate,
        'scheduled_time' => $newTime,
        'status' => 'rescheduled',
        'updated_by' => $userId,
    ]);

    tx_record_visit_history(
        $conn,
        $visitId,
        'rescheduled',
        $oldDate,
        $newDate,
        $reason,
        $userId
    );

    tx_log_activity(
        $conn,
        (int) $visit['transaction_id'],
        'service_rescheduled',
        sprintf('Service visit rescheduled from %s to %s. Reason: %s', $oldDate, $newDate, $reason),
        $userId
    );

    $conn->commit();

    json_ok(['message' => 'Service visit rescheduled successfully.']);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
