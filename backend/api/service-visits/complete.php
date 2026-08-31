<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../transactions/_helpers.php';

require_method('POST');
require_admin();
require_csrf();

$data = read_json_body(16384);
$userId = current_user_id();
$visitId = max(0, (int) ($data['visit_id'] ?? 0));
$actualDate = tx_date($data['actual_service_date'] ?? null, true, 'Actual service date');
$notes = tx_nullable_string($data['completion_notes'] ?? null, 10000);

if ($visitId <= 0) {
    json_fail('Service visit ID is required.', 422);
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
    json_fail('A visit under a cancelled transaction cannot be completed.', 409);
}

if ((string) $visit['status'] === 'cancelled') {
    json_fail('A cancelled service visit cannot be completed.', 409);
}

$conn->begin_transaction();

try {
    tx_db_update_by_id($conn, 'service_visits', $visitId, [
        'status' => 'completed',
        'actual_service_date' => $actualDate,
        'completion_notes' => $notes,
        'updated_by' => $userId,
    ]);

    tx_record_visit_history(
        $conn,
        $visitId,
        'completed',
        (string) $visit['scheduled_date'],
        (string) $visit['scheduled_date'],
        $notes,
        $userId
    );

    tx_log_activity(
        $conn,
        (int) $visit['transaction_id'],
        'service_completed',
        sprintf('%s completed on %s.', (string) $visit['title'], $actualDate),
        $userId
    );

    $conn->commit();

    json_ok(['message' => 'Service visit marked as completed.']);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
