<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_helpers.php';

require_method('POST');
require_admin();
require_csrf();

$data = read_json_body(65536);
$userId = current_user_id();
$pestIds = tx_validate_pest_ids(
    $conn,
    is_array($data['pest_ids'] ?? null) ? $data['pest_ids'] : []
);

$conn->begin_transaction();

try {
    [$customerId, $siteId, $customer] = tx_resolve_customer_and_site($conn, $data, $userId);
    $fields = tx_parse_transaction_fields($conn, $data, 'create');

    $fields['customer_id'] = $customerId;
    $fields['customer_site_id'] = $siteId;
    $fields['created_by'] = $userId;
    $fields['updated_by'] = $userId;

    $transactionId = tx_db_insert($conn, 'transactions', $fields);
    $transactionNumber = sprintf('TRX-%s-%06d', tx_business_year(), $transactionId);

    tx_db_update_by_id($conn, 'transactions', $transactionId, [
        'transaction_number' => $transactionNumber,
    ]);

    tx_sync_pests($conn, $transactionId, $pestIds);

    $transactionForSchedule = array_merge($fields, [
        'transaction_number' => $transactionNumber,
    ]);
    tx_sync_generated_visits($conn, $transactionId, $transactionForSchedule, $userId);

    $initialPayment = is_array($data['initial_payment'] ?? null)
        ? $data['initial_payment']
        : [];

    $initialAmount = tx_decimal($initialPayment['amount'] ?? null, true) ?? 0.0;

    if ($initialAmount < 0) {
        json_fail('Initial payment cannot be negative.', 422);
    }

    if ($initialAmount > (float) $fields['contract_amount'] + 0.00001) {
        json_fail('Initial payment cannot exceed the contract amount.', 422);
    }

    if ($initialAmount > 0) {
        $paymentDate = tx_date(
            $initialPayment['payment_date'] ?? null,
            true,
            'Initial payment date'
        );

        $paymentMethod = tx_enum(
            $initialPayment['payment_method'] ?? '',
            ['cash', 'bank_transfer', 'gcash', 'check', 'government_disbursement', 'other'],
            'Payment method'
        );

        tx_db_insert($conn, 'payments', [
            'transaction_id' => $transactionId,
            'amount' => $initialAmount,
            'payment_date' => $paymentDate,
            'payment_method' => $paymentMethod,
            'reference_number' => tx_nullable_string($initialPayment['reference_number'] ?? null, 150),
            'remarks' => tx_nullable_string($initialPayment['remarks'] ?? null, 500),
            'status' => 'recorded',
            'created_by' => $userId,
        ]);
    }

    tx_log_activity(
        $conn,
        $transactionId,
        'transaction_created',
        sprintf(
            'Transaction %s was created for %s with a contract amount of %s.',
            $transactionNumber,
            (string) ($customer['display_name'] ?? 'customer'),
            tx_format_money((float) $fields['contract_amount'])
        ),
        $userId
    );

    if ($initialAmount > 0) {
        tx_log_activity(
            $conn,
            $transactionId,
            'payment_recorded',
            'Initial payment of ' . tx_format_money($initialAmount) . ' was recorded.',
            $userId
        );
    }

    $conn->commit();

    json_ok([
        'message' => 'Service transaction created successfully.',
        'transaction_id' => $transactionId,
        'transaction_number' => $transactionNumber,
    ], 201);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
