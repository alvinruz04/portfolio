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
$paymentId = max(0, (int) ($data['payment_id'] ?? 0));
$reason = tx_string($data['reason'] ?? '', 500);

if ($paymentId <= 0) {
    json_fail('Payment ID is required.', 422);
}

if ($reason === '') {
    json_fail('A reason is required to void a payment.', 422);
}

$statement = $conn->prepare(
    'SELECT id, transaction_id, amount, status
     FROM payments
     WHERE id = ?
     LIMIT 1'
);
$statement->bind_param('i', $paymentId);
$statement->execute();
$payment = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$payment) {
    json_fail('Payment not found.', 404);
}

if ((string) $payment['status'] === 'voided') {
    json_ok(['message' => 'Payment is already voided.']);
}

$transactionId = (int) $payment['transaction_id'];

$conn->begin_transaction();

try {
    tx_db_update_by_id($conn, 'payments', $paymentId, [
        'status' => 'voided',
        'void_reason' => $reason,
        'voided_at' => gmdate('Y-m-d H:i:s'),
        'voided_by' => $userId,
    ]);

    tx_log_activity(
        $conn,
        $transactionId,
        'payment_voided',
        'Payment of ' . tx_format_money((float) $payment['amount']) . ' was voided. Reason: ' . $reason,
        $userId
    );

    $conn->commit();

    json_ok(['message' => 'Payment voided successfully.']);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
