<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

require_method('POST');
require_admin();
require_csrf();

$data = read_json_body(8192);
$userId = current_user_id();
$transactionId = max(0, (int) ($data['id'] ?? 0));
$reason = tx_string($data['reason'] ?? '', 500);

if ($transactionId <= 0) {
    json_fail('Transaction ID is required.', 422);
}

if ($reason === '') {
    json_fail('Cancellation reason is required.', 422);
}

$transaction = tx_transaction_row($conn, $transactionId);

if (!$transaction) {
    json_fail('Transaction not found.', 404);
}

if ((string) $transaction['transaction_status'] === 'cancelled') {
    json_ok(['message' => 'Transaction is already cancelled.']);
}

$conn->begin_transaction();

try {
    tx_db_update_by_id($conn, 'transactions', $transactionId, [
        'transaction_status' => 'cancelled',
        'cancelled_at' => gmdate('Y-m-d H:i:s'),
        'cancelled_by' => $userId,
        'cancellation_reason' => $reason,
        'updated_by' => $userId,
    ]);

    $pendingVisits = [];
    $visitStatement = $conn->prepare(
        "SELECT id, scheduled_date
         FROM service_visits
         WHERE transaction_id = ?
           AND status IN ('scheduled', 'rescheduled')"
    );
    $visitStatement->bind_param('i', $transactionId);
    $visitStatement->execute();
    $visitResult = $visitStatement->get_result();

    while ($visit = $visitResult->fetch_assoc()) {
        $pendingVisits[] = [
            'id' => (int) $visit['id'],
            'scheduled_date' => (string) $visit['scheduled_date'],
        ];
    }

    $visitStatement->close();

    $statement = $conn->prepare(
        "UPDATE service_visits
         SET status = 'cancelled', updated_by = ?
         WHERE transaction_id = ?
           AND status IN ('scheduled', 'rescheduled')"
    );
    $statement->bind_param('ii', $userId, $transactionId);
    $statement->execute();
    $statement->close();

    foreach ($pendingVisits as $visit) {
        tx_record_visit_history(
            $conn,
            $visit['id'],
            'cancelled',
            $visit['scheduled_date'],
            $visit['scheduled_date'],
            'Transaction cancelled: ' . $reason,
            $userId
        );
    }

    tx_log_activity(
        $conn,
        $transactionId,
        'transaction_cancelled',
        'Transaction cancelled. Reason: ' . $reason,
        $userId
    );

    $conn->commit();

    json_ok(['message' => 'Transaction cancelled successfully.']);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
