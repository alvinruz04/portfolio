<?php
declare(strict_types=1);

/**
 * Helpers for the APESCON master transaction module.
 *
 * This file assumes backend/api/_bootstrap.php has already been loaded and
 * therefore $conn, json_fail(), current_user_id(), etc. are available.
 */

function tx_bind(mysqli_stmt $statement, string $types, array &$values): void
{
    if ($types === '') {
        return;
    }

    $params = [$types];

    foreach ($values as $index => $value) {
        $params[] = &$values[$index];
    }

    $statement->bind_param(...$params);
}

function tx_db_type(mixed $value): string
{
    if (is_int($value) || is_bool($value)) {
        return 'i';
    }

    if (is_float($value)) {
        return 'd';
    }

    return 's';
}

function tx_db_insert(mysqli $conn, string $table, array $data): int
{
    if ($data === []) {
        throw new InvalidArgumentException('Insert data cannot be empty.');
    }

    $columns = array_keys($data);
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $columnSql = implode(', ', array_map(static fn (string $column): string => "`{$column}`", $columns));

    $sql = "INSERT INTO `{$table}` ({$columnSql}) VALUES ({$placeholders})";
    $statement = $conn->prepare($sql);

    $types = '';
    $values = array_values($data);

    foreach ($values as $value) {
        $types .= tx_db_type($value);
    }

    tx_bind($statement, $types, $values);
    $statement->execute();
    $id = (int) $conn->insert_id;
    $statement->close();

    return $id;
}

function tx_db_update_by_id(mysqli $conn, string $table, int $id, array $data): void
{
    if ($id <= 0 || $data === []) {
        return;
    }

    $assignments = [];

    foreach (array_keys($data) as $column) {
        $assignments[] = "`{$column}` = ?";
    }

    $sql = "UPDATE `{$table}` SET " . implode(', ', $assignments) . ' WHERE id = ? LIMIT 1';
    $statement = $conn->prepare($sql);

    $values = array_values($data);
    $types = '';

    foreach ($values as $value) {
        $types .= tx_db_type($value);
    }

    $values[] = $id;
    $types .= 'i';

    tx_bind($statement, $types, $values);
    $statement->execute();
    $statement->close();
}

function tx_string(mixed $value, int $maxLength = 1000): string
{
    $value = trim((string) ($value ?? ''));

    if ($value === '') {
        return '';
    }

    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }

    return $value;
}

function tx_nullable_string(mixed $value, int $maxLength = 1000): ?string
{
    $value = tx_string($value, $maxLength);

    return $value === '' ? null : $value;
}

function tx_int(mixed $value, int $default = 0): int
{
    if ($value === null || $value === '') {
        return $default;
    }

    return (int) $value;
}

function tx_decimal(mixed $value, bool $allowNull = true): ?float
{
    if ($value === null || $value === '') {
        return $allowNull ? null : 0.0;
    }

    if (!is_numeric($value)) {
        json_fail('A numeric amount contains an invalid value.', 422);
    }

    return round((float) $value, 2);
}

function tx_date(mixed $value, bool $required = false, string $label = 'Date'): ?string
{
    $value = trim((string) ($value ?? ''));

    if ($value === '') {
        if ($required) {
            json_fail("{$label} is required.", 422);
        }

        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));
    $errors = DateTimeImmutable::getLastErrors();

    if (
        !$date
        || ($errors !== false && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0))
        || $date->format('Y-m-d') !== $value
    ) {
        json_fail("{$label} must use YYYY-MM-DD format.", 422);
    }

    return $value;
}

function tx_time(mixed $value): ?string
{
    $value = trim((string) ($value ?? ''));

    if ($value === '') {
        return null;
    }

    foreach (['H:i', 'H:i:s'] as $format) {
        $time = DateTimeImmutable::createFromFormat("!{$format}", $value, new DateTimeZone('UTC'));
        $errors = DateTimeImmutable::getLastErrors();

        if (
            $time
            && ($errors === false || (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0))
        ) {
            return $time->format('H:i:s');
        }
    }

    json_fail('Service time is invalid.', 422);
}

function tx_enum(mixed $value, array $allowed, string $label, ?string $default = null): string
{
    $value = trim((string) ($value ?? ''));

    if ($value === '' && $default !== null) {
        return $default;
    }

    if (!in_array($value, $allowed, true)) {
        json_fail("{$label} is invalid.", 422);
    }

    return $value;
}

function tx_bool(mixed $value): int
{
    if (is_bool($value)) {
        return $value ? 1 : 0;
    }

    return in_array($value, [1, '1', 'true', 'yes', 'on'], true) ? 1 : 0;
}

function tx_active_service_type_exists(mysqli $conn, int $serviceTypeId): bool
{
    $statement = $conn->prepare(
        "SELECT id FROM service_types WHERE id = ? AND status = 'active' LIMIT 1"
    );
    $statement->bind_param('i', $serviceTypeId);
    $statement->execute();
    $exists = (bool) $statement->get_result()->fetch_assoc();
    $statement->close();

    return $exists;
}

function tx_validate_pest_ids(mysqli $conn, array $pestIds): array
{
    $normalized = [];

    foreach ($pestIds as $value) {
        $id = (int) $value;

        if ($id > 0) {
            $normalized[$id] = $id;
        }
    }

    $ids = array_values($normalized);

    if ($ids === []) {
        return [];
    }

    $placeholders = implode(', ', array_fill(0, count($ids), '?'));
    $statement = $conn->prepare(
        "SELECT id FROM pest_types WHERE status = 'active' AND id IN ({$placeholders})"
    );

    $types = str_repeat('i', count($ids));
    $values = $ids;
    tx_bind($statement, $types, $values);
    $statement->execute();
    $result = $statement->get_result();
    $valid = [];

    while ($row = $result->fetch_assoc()) {
        $valid[] = (int) $row['id'];
    }

    $statement->close();
    sort($valid);
    sort($ids);

    if ($valid !== $ids) {
        json_fail('One or more selected pest types are invalid.', 422);
    }

    return $ids;
}

function tx_get_customer(mysqli $conn, int $customerId): ?array
{
    $statement = $conn->prepare(
        'SELECT id, customer_type, display_name, contact_person, contact_number,
                email_address, billing_address, tin, status
         FROM customers
         WHERE id = ?
         LIMIT 1'
    );
    $statement->bind_param('i', $customerId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();

    return $row;
}

function tx_get_site(mysqli $conn, int $siteId): ?array
{
    $statement = $conn->prepare(
        'SELECT id, customer_id, site_name, address_line, barangay, city, province,
                property_type, contact_person, contact_number, notes, status
         FROM customer_sites
         WHERE id = ?
         LIMIT 1'
    );
    $statement->bind_param('i', $siteId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();

    return $row;
}

function tx_create_customer(mysqli $conn, array $customer, int $userId): int
{
    $customerType = tx_enum(
        $customer['customer_type'] ?? '',
        ['residential', 'commercial', 'industrial', 'institutional', 'government', 'agricultural'],
        'Customer type',
        'residential'
    );

    $displayName = tx_string($customer['display_name'] ?? '', 190);

    if ($displayName === '') {
        json_fail('Customer name is required.', 422);
    }

    $email = tx_nullable_string($customer['email_address'] ?? null, 191);

    if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_fail('Customer email address is invalid.', 422);
    }

    return tx_db_insert($conn, 'customers', [
        'customer_type' => $customerType,
        'display_name' => $displayName,
        'contact_person' => tx_nullable_string($customer['contact_person'] ?? null, 150),
        'contact_number' => tx_nullable_string($customer['contact_number'] ?? null, 50),
        'email_address' => $email,
        'billing_address' => tx_nullable_string($customer['billing_address'] ?? null, 2000),
        'tin' => tx_nullable_string($customer['tin'] ?? null, 50),
        'status' => 'active',
        'created_by' => $userId,
        'updated_by' => $userId,
    ]);
}

function tx_create_site(mysqli $conn, int $customerId, array $site, int $userId): int
{
    $address = tx_string($site['address_line'] ?? '', 255);

    if ($address === '') {
        json_fail('Service address is required.', 422);
    }

    return tx_db_insert($conn, 'customer_sites', [
        'customer_id' => $customerId,
        'site_name' => tx_nullable_string($site['site_name'] ?? null, 190),
        'address_line' => $address,
        'barangay' => tx_nullable_string($site['barangay'] ?? null, 120),
        'city' => tx_nullable_string($site['city'] ?? null, 120),
        'province' => tx_nullable_string($site['province'] ?? null, 120),
        'property_type' => tx_nullable_string($site['property_type'] ?? null, 100),
        'contact_person' => tx_nullable_string($site['contact_person'] ?? null, 150),
        'contact_number' => tx_nullable_string($site['contact_number'] ?? null, 50),
        'notes' => tx_nullable_string($site['notes'] ?? null, 2000),
        'status' => 'active',
        'created_by' => $userId,
        'updated_by' => $userId,
    ]);
}

/**
 * Resolves the customer/site portion of a transaction payload.
 * Existing records are never silently modified here.
 */
function tx_resolve_customer_and_site(mysqli $conn, array $data, int $userId): array
{
    $customerMode = tx_enum(
        $data['customer_mode'] ?? '',
        ['existing', 'new'],
        'Customer selection mode',
        'existing'
    );

    if ($customerMode === 'new') {
        $customerData = is_array($data['customer'] ?? null) ? $data['customer'] : [];
        $siteData = is_array($data['site'] ?? null) ? $data['site'] : [];

        $customerId = tx_create_customer($conn, $customerData, $userId);
        $siteId = tx_create_site($conn, $customerId, $siteData, $userId);
        $customer = tx_get_customer($conn, $customerId);

        return [$customerId, $siteId, $customer];
    }

    $customerId = tx_int($data['customer_id'] ?? 0);

    if ($customerId <= 0) {
        json_fail('Please select a customer.', 422);
    }

    $customer = tx_get_customer($conn, $customerId);

    if (!$customer || (string) $customer['status'] !== 'active') {
        json_fail('The selected customer is unavailable.', 422);
    }

    $siteMode = tx_enum(
        $data['site_mode'] ?? '',
        ['existing', 'new'],
        'Service location mode',
        'existing'
    );

    if ($siteMode === 'new') {
        $siteData = is_array($data['site'] ?? null) ? $data['site'] : [];
        $siteId = tx_create_site($conn, $customerId, $siteData, $userId);

        return [$customerId, $siteId, $customer];
    }

    $siteId = tx_int($data['customer_site_id'] ?? 0);

    if ($siteId <= 0) {
        json_fail('Please select a service location.', 422);
    }

    $site = tx_get_site($conn, $siteId);

    if (
        !$site
        || (int) $site['customer_id'] !== $customerId
        || (string) $site['status'] !== 'active'
    ) {
        json_fail('The selected service location does not belong to this customer.', 422);
    }

    return [$customerId, $siteId, $customer];
}

function tx_parse_transaction_fields(mysqli $conn, array $data, string $context = 'create'): array
{
    $serviceTypeId = tx_int($data['service_type_id'] ?? 0);

    if ($serviceTypeId <= 0 || !tx_active_service_type_exists($conn, $serviceTypeId)) {
        json_fail('Please select a valid service type.', 422);
    }

    $contractAmount = tx_decimal($data['contract_amount'] ?? null, false);

    if ($contractAmount === null || $contractAmount <= 0) {
        json_fail('Contract amount must be greater than zero.', 422);
    }

    $contractStartDate = tx_date($data['contract_start_date'] ?? null, true, 'Contract start date');
    $contractEndDate = tx_date($data['contract_end_date'] ?? null, false, 'Contract end date');

    if ($contractEndDate !== null && $contractEndDate < $contractStartDate) {
        json_fail('Contract end date cannot be earlier than the contract start date.', 422);
    }

    $initialServiceDate = tx_date($data['initial_service_date'] ?? null, true, 'Initial service date');

    $frequency = tx_enum(
        $data['service_frequency'] ?? '',
        ['one_time', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual', 'custom'],
        'Service frequency',
        'one_time'
    );

    $followUpVisits = max(0, tx_int($data['follow_up_visits'] ?? 0));

    if ($followUpVisits > 60) {
        json_fail('Follow-up visits cannot exceed 60 in this version.', 422);
    }

    $customIntervalDays = tx_int($data['custom_interval_days'] ?? 0);

    if ($frequency === 'one_time') {
        $followUpVisits = 0;
        $customIntervalDays = 0;
    }

    if ($frequency === 'custom' && $followUpVisits > 0 && $customIntervalDays <= 0) {
        json_fail('Custom schedule interval must be greater than zero days.', 422);
    }

    $warrantyIncluded = tx_bool($data['warranty_included'] ?? false);
    $warrantyStart = tx_date($data['warranty_start_date'] ?? null, false, 'Warranty start date');
    $warrantyEnd = tx_date($data['warranty_end_date'] ?? null, false, 'Warranty end date');

    if ($warrantyIncluded === 1) {
        if ($warrantyStart === null || $warrantyEnd === null) {
            json_fail('Warranty start and end dates are required when warranty is included.', 422);
        }

        if ($warrantyEnd < $warrantyStart) {
            json_fail('Warranty end date cannot be earlier than the warranty start date.', 422);
        }
    } else {
        $warrantyStart = null;
        $warrantyEnd = null;
    }

    $allowedStatuses = $context === 'create'
        ? ['draft', 'active']
        : ['draft', 'active', 'completed'];

    $transactionStatus = tx_enum(
        $data['transaction_status'] ?? '',
        $allowedStatuses,
        'Transaction status',
        'active'
    );

    $quotationAmount = tx_decimal($data['quotation_amount'] ?? null, true);

    if ($quotationAmount !== null && $quotationAmount < 0) {
        json_fail('Quotation amount cannot be negative.', 422);
    }

    $areaCoverage = tx_decimal($data['area_coverage'] ?? null, true);

    if ($areaCoverage !== null && $areaCoverage < 0) {
        json_fail('Area coverage cannot be negative.', 422);
    }

    $personnelCount = tx_int($data['personnel_count'] ?? 0);

    if ($personnelCount < 0 || $personnelCount > 500) {
        json_fail('Personnel count is invalid.', 422);
    }

    return [
        'service_type_id' => $serviceTypeId,
        'survey_date' => tx_date($data['survey_date'] ?? null, false, 'Survey date'),
        'surveyed_by' => tx_nullable_string($data['surveyed_by'] ?? null, 150),
        'quotation_number' => tx_nullable_string($data['quotation_number'] ?? null, 100),
        'quotation_date' => tx_date($data['quotation_date'] ?? null, false, 'Quotation date'),
        'quotation_valid_until' => tx_date($data['quotation_valid_until'] ?? null, false, 'Quotation validity date'),
        'quotation_amount' => $quotationAmount,
        'contract_number' => tx_nullable_string($data['contract_number'] ?? null, 100),
        'contract_date' => tx_date($data['contract_date'] ?? null, false, 'Contract date'),
        'contract_amount' => $contractAmount,
        'contract_start_date' => $contractStartDate,
        'contract_end_date' => $contractEndDate,
        'treatment_method' => tx_nullable_string($data['treatment_method'] ?? null, 5000),
        'chemical_details' => tx_nullable_string($data['chemical_details'] ?? null, 5000),
        'area_coverage' => $areaCoverage,
        'area_unit' => tx_nullable_string($data['area_unit'] ?? null, 20),
        'personnel_count' => $personnelCount > 0 ? $personnelCount : null,
        'service_scope' => tx_nullable_string($data['service_scope'] ?? null, 10000),
        'payment_terms' => tx_enum(
            $data['payment_terms'] ?? '',
            [
                'full_before_service',
                'full_after_service',
                'partial_installment',
                'per_visit',
                'government_processing',
                'other',
            ],
            'Payment terms',
            'full_before_service'
        ),
        'initial_service_date' => $initialServiceDate,
        'initial_service_time' => tx_time($data['initial_service_time'] ?? null),
        'assigned_team' => tx_nullable_string($data['assigned_team'] ?? null, 190),
        'service_supervisor' => tx_nullable_string($data['service_supervisor'] ?? null, 150),
        'service_frequency' => $frequency,
        'follow_up_visits' => $followUpVisits,
        'custom_interval_days' => $customIntervalDays > 0 ? $customIntervalDays : null,
        'warranty_included' => $warrantyIncluded,
        'warranty_start_date' => $warrantyStart,
        'warranty_end_date' => $warrantyEnd,
        'warranty_notes' => $warrantyIncluded === 1
            ? tx_nullable_string($data['warranty_notes'] ?? null, 5000)
            : null,
        'government_project_title' => tx_nullable_string($data['government_project_title'] ?? null, 255),
        'government_reference' => tx_nullable_string($data['government_reference'] ?? null, 150),
        'philgeps_reference' => tx_nullable_string($data['philgeps_reference'] ?? null, 150),
        'purchase_order_number' => tx_nullable_string($data['purchase_order_number'] ?? null, 150),
        'notice_to_proceed_date' => tx_date($data['notice_to_proceed_date'] ?? null, false, 'Notice to Proceed date'),
        'government_representative' => tx_nullable_string($data['government_representative'] ?? null, 190),
        'transaction_status' => $transactionStatus,
        'notes' => tx_nullable_string($data['notes'] ?? null, 10000),
    ];
}

function tx_payment_total(mysqli $conn, int $transactionId): float
{
    $statement = $conn->prepare(
        "SELECT COALESCE(SUM(amount), 0) AS total
         FROM payments
         WHERE transaction_id = ? AND status = 'recorded'"
    );
    $statement->bind_param('i', $transactionId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return round((float) ($row['total'] ?? 0), 2);
}

function tx_payment_status(float $contractAmount, float $totalPaid): string
{
    if ($totalPaid <= 0.00001) {
        return 'unpaid';
    }

    if ($totalPaid + 0.00001 < $contractAmount) {
        return 'partial';
    }

    return 'paid';
}

function tx_add_months_clamped(DateTimeImmutable $base, int $months): DateTimeImmutable
{
    $year = (int) $base->format('Y');
    $month = (int) $base->format('n');
    $day = (int) $base->format('j');

    $totalMonths = ($year * 12 + ($month - 1)) + $months;
    $targetYear = intdiv($totalMonths, 12);
    $targetMonth = ($totalMonths % 12) + 1;

    $first = $base->setDate($targetYear, $targetMonth, 1);
    $daysInMonth = (int) $first->format('t');

    return $first->setDate($targetYear, $targetMonth, min($day, $daysInMonth));
}

function tx_schedule_date(string $initialDate, string $frequency, int $sequence, ?int $customIntervalDays): string
{
    $base = new DateTimeImmutable($initialDate, new DateTimeZone('UTC'));

    return match ($frequency) {
        'weekly' => $base->modify('+' . $sequence . ' weeks')->format('Y-m-d'),
        'monthly' => tx_add_months_clamped($base, $sequence)->format('Y-m-d'),
        'quarterly' => tx_add_months_clamped($base, $sequence * 3)->format('Y-m-d'),
        'semi_annual' => tx_add_months_clamped($base, $sequence * 6)->format('Y-m-d'),
        'annual' => tx_add_months_clamped($base, $sequence * 12)->format('Y-m-d'),
        'custom' => $base->modify('+' . ($sequence * max(1, (int) $customIntervalDays)) . ' days')->format('Y-m-d'),
        default => $base->format('Y-m-d'),
    };
}

function tx_follow_up_title(string $frequency, int $sequence): string
{
    $label = match ($frequency) {
        'weekly' => 'Weekly Follow-Up',
        'monthly' => 'Monthly Follow-Up',
        'quarterly' => 'Quarterly Visit',
        'semi_annual' => 'Semi-Annual Visit',
        'annual' => 'Annual Visit',
        'custom' => 'Scheduled Follow-Up',
        default => 'Follow-Up Visit',
    };

    return "{$label} #{$sequence}";
}

function tx_visit_type_for_frequency(string $frequency): string
{
    return $frequency === 'quarterly' ? 'quarterly' : 'scheduled_follow_up';
}

function tx_log_activity(
    mysqli $conn,
    int $transactionId,
    string $actionCode,
    string $description,
    ?int $userId = null
): void {
    tx_db_insert($conn, 'transaction_activity', [
        'transaction_id' => $transactionId,
        'action_code' => tx_string($actionCode, 60),
        'description' => tx_string($description, 1000),
        'actor_user_id' => $userId,
    ]);
}

function tx_record_visit_history(
    mysqli $conn,
    int $visitId,
    string $action,
    ?string $oldDate,
    ?string $newDate,
    ?string $reason,
    ?int $userId
): void {
    tx_db_insert($conn, 'service_visit_history', [
        'service_visit_id' => $visitId,
        'action' => $action,
        'old_scheduled_date' => $oldDate,
        'new_scheduled_date' => $newDate,
        'reason' => $reason,
        'performed_by' => $userId,
    ]);
}

/**
 * Creates/updates only the contract-generated service visits.
 * Completed, cancelled and missed generated visits are preserved as historical facts.
 */
function tx_sync_generated_visits(
    mysqli $conn,
    int $transactionId,
    array $transaction,
    int $userId,
    bool $recalculateDates = true
): void
{
    $desired = [];
    $initialDate = (string) $transaction['initial_service_date'];
    $initialTime = $transaction['initial_service_time'] ?? null;
    $assignedTeam = $transaction['assigned_team'] ?? null;
    $supervisor = $transaction['service_supervisor'] ?? null;
    $frequency = (string) $transaction['service_frequency'];
    $followUps = (int) $transaction['follow_up_visits'];
    $customIntervalDays = isset($transaction['custom_interval_days'])
        ? (int) $transaction['custom_interval_days']
        : null;

    $desired[1] = [
        'visit_number' => 1,
        'visit_type' => 'initial',
        'title' => 'Initial Service',
        'scheduled_date' => $initialDate,
        'scheduled_time' => $initialTime,
        'assigned_team' => $assignedTeam,
        'service_supervisor' => $supervisor,
    ];

    for ($sequence = 1; $sequence <= $followUps; $sequence++) {
        $visitNumber = $sequence + 1;
        $desired[$visitNumber] = [
            'visit_number' => $visitNumber,
            'visit_type' => tx_visit_type_for_frequency($frequency),
            'title' => tx_follow_up_title($frequency, $sequence),
            'scheduled_date' => tx_schedule_date($initialDate, $frequency, $sequence, $customIntervalDays),
            'scheduled_time' => $initialTime,
            'assigned_team' => $assignedTeam,
            'service_supervisor' => $supervisor,
        ];
    }

    $statement = $conn->prepare(
        'SELECT id, visit_number, scheduled_date, status
         FROM service_visits
         WHERE transaction_id = ?
           AND generated_from_contract = 1
         ORDER BY visit_number ASC'
    );
    $statement->bind_param('i', $transactionId);
    $statement->execute();
    $result = $statement->get_result();
    $existing = [];

    while ($row = $result->fetch_assoc()) {
        $existing[(int) $row['visit_number']] = $row;
    }

    $statement->close();

    foreach ($desired as $visitNumber => $visit) {
        $current = $existing[$visitNumber] ?? null;

        if (!$current) {
            $visitId = tx_db_insert($conn, 'service_visits', [
                'transaction_id' => $transactionId,
                'visit_number' => $visit['visit_number'],
                'visit_type' => $visit['visit_type'],
                'title' => $visit['title'],
                'scheduled_date' => $visit['scheduled_date'],
                'scheduled_time' => $visit['scheduled_time'],
                'assigned_team' => $visit['assigned_team'],
                'service_supervisor' => $visit['service_supervisor'],
                'generated_from_contract' => 1,
                'status' => 'scheduled',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            tx_record_visit_history(
                $conn,
                $visitId,
                'created',
                null,
                $visit['scheduled_date'],
                'Generated from the transaction service schedule.',
                $userId
            );

            continue;
        }

        $status = (string) $current['status'];

        if (in_array($status, ['completed', 'cancelled', 'missed'], true)) {
            continue;
        }

        $oldDate = (string) $current['scheduled_date'];
        $newDate = $recalculateDates ? $visit['scheduled_date'] : $oldDate;

        tx_db_update_by_id($conn, 'service_visits', (int) $current['id'], [
            'visit_type' => $visit['visit_type'],
            'title' => $visit['title'],
            'scheduled_date' => $newDate,
            'scheduled_time' => $visit['scheduled_time'],
            'assigned_team' => $visit['assigned_team'],
            'service_supervisor' => $visit['service_supervisor'],
            'status' => $oldDate === $newDate ? $status : 'rescheduled',
            'updated_by' => $userId,
        ]);

        if ($oldDate !== $newDate) {
            tx_record_visit_history(
                $conn,
                (int) $current['id'],
                'rescheduled',
                $oldDate,
                $newDate,
                'Schedule updated from transaction details.',
                $userId
            );
        }
    }

    foreach ($existing as $visitNumber => $current) {
        if (isset($desired[$visitNumber])) {
            continue;
        }

        if (!in_array((string) $current['status'], ['scheduled', 'rescheduled'], true)) {
            continue;
        }

        $visitId = (int) $current['id'];
        $oldDate = (string) $current['scheduled_date'];

        tx_db_update_by_id($conn, 'service_visits', $visitId, [
            'status' => 'cancelled',
            'updated_by' => $userId,
        ]);

        tx_record_visit_history(
            $conn,
            $visitId,
            'cancelled',
            $oldDate,
            $oldDate,
            'Removed from the generated schedule after the transaction schedule was updated.',
            $userId
        );
    }
}

function tx_sync_pests(mysqli $conn, int $transactionId, array $pestIds): void
{
    $delete = $conn->prepare('DELETE FROM transaction_pests WHERE transaction_id = ?');
    $delete->bind_param('i', $transactionId);
    $delete->execute();
    $delete->close();

    if ($pestIds === []) {
        return;
    }

    $insert = $conn->prepare(
        'INSERT INTO transaction_pests (transaction_id, pest_type_id) VALUES (?, ?)'
    );

    foreach ($pestIds as $pestId) {
        $insert->bind_param('ii', $transactionId, $pestId);
        $insert->execute();
    }

    $insert->close();
}

function tx_transaction_row(mysqli $conn, int $transactionId): ?array
{
    $statement = $conn->prepare(
        'SELECT * FROM transactions WHERE id = ? LIMIT 1'
    );
    $statement->bind_param('i', $transactionId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc() ?: null;
    $statement->close();

    return $row;
}

function tx_has_completed_initial_visit(mysqli $conn, int $transactionId): bool
{
    $statement = $conn->prepare(
        "SELECT id
         FROM service_visits
         WHERE transaction_id = ?
           AND generated_from_contract = 1
           AND visit_number = 1
           AND status = 'completed'
         LIMIT 1"
    );
    $statement->bind_param('i', $transactionId);
    $statement->execute();
    $exists = (bool) $statement->get_result()->fetch_assoc();
    $statement->close();

    return $exists;
}

function tx_open_generated_visits(mysqli $conn, int $transactionId): int
{
    $statement = $conn->prepare(
        "SELECT COUNT(*) AS total
         FROM service_visits
         WHERE transaction_id = ?
           AND generated_from_contract = 1
           AND status IN ('scheduled', 'rescheduled', 'missed')"
    );
    $statement->bind_param('i', $transactionId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    return (int) ($row['total'] ?? 0);
}

function tx_unresolved_desired_generated_visits(
    mysqli $conn,
    int $transactionId,
    int $desiredVisitCount
): int {
    $desiredVisitCount = max(1, $desiredVisitCount);

    $statement = $conn->prepare(
        "SELECT COUNT(*) AS resolved
         FROM service_visits
         WHERE transaction_id = ?
           AND generated_from_contract = 1
           AND visit_number BETWEEN 1 AND ?
           AND status IN ('completed', 'cancelled')"
    );
    $statement->bind_param('ii', $transactionId, $desiredVisitCount);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();

    $resolved = (int) ($row['resolved'] ?? 0);

    return max(0, $desiredVisitCount - $resolved);
}

function tx_business_now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
}

function tx_business_today(): string
{
    return tx_business_now()->format('Y-m-d');
}

function tx_business_year(): string
{
    return tx_business_now()->format('Y');
}

function tx_format_money(float $amount): string
{
    return '₱' . number_format($amount, 2);
}
