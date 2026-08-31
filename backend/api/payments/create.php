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
$transactionId = max(0, (int) ($data['transaction_id'] ?? 0));

if ($transactionId <= 0) {
    json_fail('Transaction ID is required.', 422);
}

$transaction = tx_transaction_row($conn, $transactionId);

if (!$transaction) {
    json_fail('Transaction not found.', 404);
}

if ((string) $transaction['transaction_status'] === 'cancelled') {
    json_fail('Payments cannot be recorded on a cancelled transaction.', 409);
}

$amount = tx_decimal($data['amount'] ?? null, false);

if ($amount === null || $amount <= 0) {
    json_fail('Payment amount must be greater than zero.', 422);
}

$paymentDate = tx_date($data['payment_date'] ?? null, true, 'Payment date');
$paymentMethod = tx_enum(
    $data['payment_method'] ?? '',
    ['cash', 'bank_transfer', 'gcash', 'check', 'government_disbursement', 'other'],
    'Payment method'
);

$totalPaid = tx_payment_total($conn, $transactionId);
$balance = max(0, round((float) $transaction['contract_amount'] - $totalPaid, 2));

if ($amount > $balance + 0.00001) {
    json_fail(
        'Payment amount cannot exceed the outstanding balance of ' . tx_format_money($balance) . '.',
        422,
        ['balance' => $balance]
    );
}

$conn->begin_transaction();

try {
    $paymentId = tx_db_insert($conn, 'payments', [
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'payment_date' => $paymentDate,
        'payment_method' => $paymentMethod,
        'reference_number' => tx_nullable_string($data['reference_number'] ?? null, 150),
        'remarks' => tx_nullable_string($data['remarks'] ?? null, 500),
        'status' => 'recorded',
        'created_by' => $userId,
    ]);

    tx_log_activity(
        $conn,
        $transactionId,
        'payment_recorded',
        'Payment of ' . tx_format_money($amount) . ' was recorded.',
        $userId
    );

    $conn->commit();

    $newTotal = round($totalPaid + $amount, 2);

    json_ok([
        'message' => 'Payment recorded successfully.',
        'payment_id' => $paymentId,
        'total_paid' => $newTotal,
        'balance' => max(0, round((float) $transaction['contract_amount'] - $newTotal, 2)),
        'payment_status' => tx_payment_status((float) $transaction['contract_amount'], $newTotal),
    ], 201);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
