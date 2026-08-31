<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';

require_admin();
require_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$name   = trim((string)($input['name'] ?? ''));
$active = isset($input['active']) ? (int)!!$input['active'] : 1;
$prices = $input['prices'] ?? [];

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
    if (!in_array($vehicleType, $allowedVehicleTypes, true)) {
        continue;
    }

    if (!is_numeric($price)) {
        json_fail("Invalid price for {$vehicleType}.", 422);
    }

    $num = round((float)$price, 2);
    if ($num < 0) {
        json_fail("Invalid price for {$vehicleType}.", 422);
    }

    $normalizedPrices[$vehicleType] = $num;
}

if (count($normalizedPrices) === 0) {
    json_fail('Please provide at least one vehicle price.', 422);
}

ksort($normalizedPrices);

$branch_id = current_admin_branch_id($conn);
if ($branch_id <= 0) json_fail('Admin branch is not set.', 403);

/** Prevent duplicate package name inside branch */
$chk = $conn->prepare("
    SELECT id
    FROM packages
    WHERE branch_id = ? AND name = ?
    LIMIT 1
");
$chk->bind_param("is", $branch_id, $name);
$chk->execute();

if ($chk->get_result()->fetch_assoc()) {
    $chk->close();
    json_fail('Package name already exists in your branch.', 409);
}
$chk->close();

try {
    $conn->begin_transaction();

    /** Create package */
    $stmt = $conn->prepare("
        INSERT INTO packages (branch_id, name, active)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isi", $branch_id, $name, $active);

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        throw new Exception('DB insert failed: ' . $err, 500);
    }

    $newId = (int)$conn->insert_id;
    $stmt->close();

    /** Create package prices */
    $stmtPrice = $conn->prepare("
        INSERT INTO package_prices (package_id, vehicle_type, price)
        VALUES (?, ?, ?)
    ");

    foreach ($normalizedPrices as $vehicleType => $price) {
        $stmtPrice->bind_param("isd", $newId, $vehicleType, $price);
        if (!$stmtPrice->execute()) {
            $err = $stmtPrice->error;
            $stmtPrice->close();
            throw new Exception('Failed to insert package price: ' . $err, 500);
        }
    }
    $stmtPrice->close();

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
            'action'      => 'PACKAGE_CREATE',
            'entity_type' => 'package',
            'entity_id'   => $newId,
            'meta' => [
                'message' => 'Package created',
                'target_package_id' => $newId,
                'is_used_in_transactions' => 0,
                'target_name' => $name,
                'target_active' => (int)$active,
                'target_prices' => $normalizedPrices,
                'target_branch_id' => $branch_id,
                'target_branch_name' => $branchName,
                'performed_by_role' => (string)($_SESSION['role'] ?? ''),
                'performed_by_username' => (string)($_SESSION['username'] ?? ''),
            ],
        ]);
    }

    $conn->commit();

    json_ok([
        'id' => $newId,
        'message' => 'Package created successfully.',
    ]);
} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $ignore) {}

    $code = (int)$e->getCode();
    if ($code < 400 || $code > 599) $code = 500;

    json_fail($e->getMessage(), $code);
}