<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';

require_admin();
require_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$id     = (int)($input['id'] ?? 0);
$name   = trim((string)($input['name'] ?? ''));
$active = isset($input['active']) ? (int)!!$input['active'] : 1;
$prices = $input['prices'] ?? [];

if ($id <= 0) json_fail('Invalid package ID.', 422);
if ($name === '') json_fail('Package name is required.', 422);
if (mb_strlen($name) > 150) $name = mb_substr($name, 0, 150);
if (!is_array($prices)) json_fail('Invalid prices payload.', 422);

$allowedVehicleTypes = [
    'CAR_SMALL',
    'CAR_SEMI_MEDIUM',
    'CAR_MEDIUM',
    'CAR_LARGE',
    'CAR_XL',
    'BIKE_SCOOTER',
    'BIKE_BIGBIKE',
];

$normalizedPrices = [];
foreach ($prices as $vehicleType => $price) {
    if (!in_array($vehicleType, $allowedVehicleTypes, true)) continue;

    if (!is_numeric($price)) {
        json_fail("Invalid price for {$vehicleType}.", 422);
    }

    $num = round((float)$price, 2);
    if ($num < 0) json_fail("Invalid price for {$vehicleType}.", 422);

    $normalizedPrices[$vehicleType] = $num;
}

if (count($normalizedPrices) === 0) {
    json_fail('Please provide at least one vehicle price.', 422);
}

$branch_id = current_admin_branch_id($conn);
if ($branch_id <= 0) json_fail('Admin branch is not set.', 403);

/** Must exist + belong to branch */
$exists = $conn->prepare("
    SELECT id, name, active
    FROM packages
    WHERE id = ? AND branch_id = ?
    LIMIT 1
");
$exists->bind_param("ii", $id, $branch_id);
$exists->execute();
$existing = $exists->get_result()->fetch_assoc();
$exists->close();

if (!$existing) json_fail('Package not found.', 404);

/** duplicate name except self */
$u = $conn->prepare("SELECT id FROM packages WHERE branch_id = ? AND name = ? AND id <> ? LIMIT 1");
$u->bind_param("isi", $branch_id, $name, $id);
$u->execute();
if ($u->get_result()->num_rows > 0) {
    $u->close();
    json_fail('Package name already exists in your branch.', 409);
}
$u->close();

/** get old prices for audit */
$oldPrices = [];
$oldStmt = $conn->prepare("
    SELECT vehicle_type, price
    FROM package_prices
    WHERE package_id = ?
");
$oldStmt->bind_param("i", $id);
$oldStmt->execute();
$oldRes = $oldStmt->get_result();
while ($r = $oldRes->fetch_assoc()) {
    $oldPrices[(string)$r['vehicle_type']] = round((float)$r['price'], 2);
}
$oldStmt->close();

/** check if package has historical usage */
$isUsed = false;
$use = $conn->prepare("
    SELECT id
    FROM transactions
    WHERE package_id = ? AND branch_id = ?
    LIMIT 1
");
$use->bind_param("ii", $id, $branch_id);
$use->execute();
$isUsed = (bool)$use->get_result()->fetch_assoc();
$use->close();

/**
 * If package already used:
 * allow ONLY active/inactive change
 * block name and price changes
 */
if ($isUsed) {
    $oldName = (string)($existing['name'] ?? '');

    $normalizedOldPrices = $oldPrices;
    $normalizedNewPrices = [];
    foreach ($normalizedPrices as $k => $v) {
        $normalizedNewPrices[$k] = round((float)$v, 2);
    }

    ksort($normalizedOldPrices);
    ksort($normalizedNewPrices);

    if ($oldName !== $name || $normalizedOldPrices !== $normalizedNewPrices) {
        json_fail(
            'This package is already used in transactions. You may only change its active/inactive status.',
            422
        );
    }
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("
        UPDATE packages
        SET name = ?, active = ?
        WHERE id = ? AND branch_id = ?
    ");
    $stmt->bind_param("siii", $name, $active, $id, $branch_id);

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        throw new Exception('Failed to update package: ' . $err, 500);
    }
    $stmt->close();

    /**
     * Refresh package_prices only if package is not historically used.
     * For used packages, only active flag can change, so no need to touch prices.
     */
    if (!$isUsed) {
        $del = $conn->prepare("DELETE FROM package_prices WHERE package_id = ?");
        $del->bind_param("i", $id);
        if (!$del->execute()) {
            $err = $del->error;
            $del->close();
            throw new Exception('Failed to refresh package prices: ' . $err, 500);
        }
        $del->close();

        $stmtPrice = $conn->prepare("
            INSERT INTO package_prices (package_id, vehicle_type, price)
            VALUES (?, ?, ?)
        ");

        foreach ($normalizedPrices as $vehicleType => $price) {
            $stmtPrice->bind_param("isd", $id, $vehicleType, $price);
            if (!$stmtPrice->execute()) {
                $err = $stmtPrice->error;
                $stmtPrice->close();
                throw new Exception('Failed to insert package price: ' . $err, 500);
            }
        }
        $stmtPrice->close();
    }

    /** Optional branch name */
    $branchName = '';
    $b = $conn->prepare("SELECT name FROM branches WHERE id = ? LIMIT 1");
    $b->bind_param("i", $branch_id);
    $b->execute();
    $brow = $b->get_result()->fetch_assoc();
    $b->close();
    $branchName = (string)($brow['name'] ?? '');

    $actorUserId = (int)($_SESSION['user_id'] ?? 0);

    if ($branch_id > 0 && $actorUserId > 0) {
        audit_write($conn, [
            'branch_id'   => $branch_id,
            'user_id'     => $actorUserId,
            'action'      => 'PACKAGE_UPDATE',
            'entity_type' => 'package',
            'entity_id'   => $id,
            'meta' => [
                'message' => $isUsed ? 'Package status updated (historically used)' : 'Package updated',
                'target_package_id' => $id,
                'is_used_in_transactions' => $isUsed ? 1 : 0,
                'old_name' => (string)($existing['name'] ?? ''),
                'new_name' => $name,
                'old_active' => (int)($existing['active'] ?? 0),
                'new_active' => (int)$active,
                'old_prices' => $oldPrices,
                'new_prices' => $normalizedPrices,
                'target_branch_id' => $branch_id,
                'target_branch_name' => $branchName,
                'performed_by_role' => (string)($_SESSION['role'] ?? ''),
                'performed_by_username' => (string)($_SESSION['username'] ?? ''),
            ],
        ]);
    }

    $conn->commit();

    json_ok([
        'message' => $isUsed
            ? 'Package status updated successfully.'
            : 'Package updated successfully.',
        'is_used' => $isUsed,
    ]);
} catch (Throwable $e) {
    try { $conn->rollback(); } catch (Throwable $ignore) {}

    $code = (int)$e->getCode();
    if ($code < 400 || $code > 599) $code = 500;
    json_fail($e->getMessage(), $code);
}