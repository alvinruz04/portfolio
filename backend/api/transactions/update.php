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
$transactionId = max(0, (int) ($data['id'] ?? 0));

if ($transactionId <= 0) {
    json_fail('Transaction ID is required.', 422);
}

$existing = tx_transaction_row($conn, $transactionId);

if (!$existing) {
    json_fail('Transaction not found.', 404);
}

if ((string) $existing['transaction_status'] === 'cancelled') {
    json_fail('Cancelled transactions are read-only.', 409);
}

$pestIds = tx_validate_pest_ids(
    $conn,
    is_array($data['pest_ids'] ?? null) ? $data['pest_ids'] : []
);

$conn->begin_transaction();

try {
    [$customerId, $siteId] = tx_resolve_customer_and_site($conn, $data, $userId);
    $fields = tx_parse_transaction_fields($conn, $data, 'update');
    $fields['customer_id'] = $customerId;
    $fields['customer_site_id'] = $siteId;
    $fields['updated_by'] = $userId;

    $totalPaid = tx_payment_total($conn, $transactionId);

    if ($totalPaid > (float) $fields['contract_amount'] + 0.00001) {
        json_fail(
            'Contract amount cannot be lower than the total recorded payments. Void or correct the payment first.',
            422
        );
    }

    if (
        (string) $existing['initial_service_date'] !== (string) $fields['initial_service_date']
        && tx_has_completed_initial_visit($conn, $transactionId)
    ) {
        json_fail(
            'The initial service date can no longer be changed because the initial visit is already completed.',
            409
        );
    }

    if ((string) $fields['transaction_status'] === 'completed') {
        $desiredVisitCount = 1 + (int) $fields['follow_up_visits'];
        $openVisits = tx_unresolved_desired_generated_visits(
            $conn,
            $transactionId,
            $desiredVisitCount
        );

        if ($openVisits > 0) {
            json_fail(
                'Complete, cancel, or resolve the remaining scheduled service visits before marking this transaction completed.',
                409,
                ['open_visits' => $openVisits]
            );
        }
    }

    // Date-plan changes intentionally recalculate pending generated visit dates.
    // Assignment/time-only changes preserve dates that staff may have manually rescheduled.
    $schedulePlanKeys = [
        'initial_service_date',
        'service_frequency',
        'follow_up_visits',
        'custom_interval_days',
    ];
    $scheduleMetadataKeys = [
        'initial_service_time',
        'assigned_team',
        'service_supervisor',
    ];

    $schedulePlanChanged = false;
    $scheduleMetadataChanged = false;

    foreach ($schedulePlanKeys as $key) {
        if ((string) ($existing[$key] ?? null) !== (string) ($fields[$key] ?? null)) {
            $schedulePlanChanged = true;
            break;
        }
    }

    foreach ($scheduleMetadataKeys as $key) {
        if ((string) ($existing[$key] ?? null) !== (string) ($fields[$key] ?? null)) {
            $scheduleMetadataChanged = true;
            break;
        }
    }

    $changes = [];

    if ((float) $existing['contract_amount'] !== (float) $fields['contract_amount']) {
        $changes[] = sprintf(
            'contract amount %s → %s',
            tx_format_money((float) $existing['contract_amount']),
            tx_format_money((float) $fields['contract_amount'])
        );
    }

    if ((string) $existing['initial_service_date'] !== (string) $fields['initial_service_date']) {
        $changes[] = sprintf(
            'initial service %s → %s',
            (string) $existing['initial_service_date'],
            (string) $fields['initial_service_date']
        );
    }

    if ((string) $existing['transaction_status'] !== (string) $fields['transaction_status']) {
        $changes[] = sprintf(
            'status %s → %s',
            (string) $existing['transaction_status'],
            (string) $fields['transaction_status']
        );
    }

    tx_db_update_by_id($conn, 'transactions', $transactionId, $fields);
    tx_sync_pests($conn, $transactionId, $pestIds);

    if ($schedulePlanChanged || $scheduleMetadataChanged) {
        $updatedTransaction = array_merge($existing, $fields);
        tx_sync_generated_visits(
            $conn,
            $transactionId,
            $updatedTransaction,
            $userId,
            $schedulePlanChanged
        );
    }

    $description = $changes === []
        ? 'Transaction details were updated.'
        : 'Transaction updated: ' . implode('; ', $changes) . '.';

    tx_log_activity(
        $conn,
        $transactionId,
        'transaction_updated',
        $description,
        $userId
    );

    $conn->commit();

    json_ok([
        'message' => 'Transaction updated successfully.',
        'transaction_id' => $transactionId,
    ]);
} catch (Throwable $exception) {
    $conn->rollback();
    throw $exception;
}
