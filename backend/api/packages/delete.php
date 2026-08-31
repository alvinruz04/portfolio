<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';

require_admin();
require_csrf();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int)($input['id'] ?? 0);

if ($id <= 0) json_fail('Invalid package ID.', 422);

$branch_id = current_admin_branch_id($conn);
if ($branch_id <= 0) json_fail('Admin branch is not set.', 403);

/** Target package must exist and belong to branch */
$get = $conn->prepare("
    SELECT id, name, active
    FROM packages
    WHERE id = ? AND branch_id = ?
    LIMIT 1
");
$get->bind_param("ii", $id, $branch_id);
$get->execute();
$target = $get->get_result()->fetch_assoc();
$get->close();

if (!$target) json_fail('Package not found.', 404);

/** Check if package has historical usage */
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

if ($isUsed) {
    json_fail(
        'This package is already used in transactions and cannot be deleted. Set it to inactive instead.',
        422
    );
}

/** Old prices for audit */
$oldPrices = [];
$oldStmt = $conn->prepare("
    SELECT vehicle_type, price
    FROM package_prices
    WHERE package_id = ?
    ORDER BY vehicle_type ASC
");
$oldStmt->bind_param("i", $id);
$oldStmt->execute();
$oldRes = $oldStmt->get_result();
while ($r = $oldRes->fetch_assoc()) {
    $oldPrices[(string)$r['vehicle_type']] = round((float)$r['price'], 2);
}
$oldStmt->close();

try {
    $conn->begin_transaction();

    /** Delete child prices first */
    $delPrice = $conn->prepare("DELETE FROM package_prices WHERE package_id = ?");
    $delPrice->bind_param("i", $id);
    if (!$delPrice->execute()) {
        $err = $delPrice->error;
        $delPrice->close();
        throw new Exception('Failed to delete package prices: ' . $err, 500);
    }
    $delPrice->close();

    /** Delete package */
    $stmt = $conn->prepare("
        DELETE FROM packages
        WHERE id = ? AND branch_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $id, $branch_id);

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        throw new Exception('DB delete failed: ' . $err, 500);
    }

    if ($stmt->affected_rows !== 1) {
        $stmt->close();
        throw new Exception('Package not found.', 404);
    }
    $stmt->close();

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
            'action'      => 'PACKAGE_DELETE',
            'entity_type' => 'package',
            'entity_id'   => $id,
            'meta' => [
                'message' => 'Package deleted',
                'target_package_id' => $id,
                'is_used_in_transactions' => 0,
                'target_name' => (string)($target['name'] ?? ''),
                'target_active' => (int)($target['active'] ?? 0),
                'target_prices' => $oldPrices,
                'target_branch_id' => $branch_id,
                'target_branch_name' => $branchName,
                'performed_by_role' => (string)($_SESSION['role'] ?? ''),
                'performed_by_username' => (string)($_SESSION['username'] ?? ''),
            ],
        ]);
    }

    $conn->commit();

    json_ok([
        'message' => 'Package deleted successfully.',
    ]);
} catch (Throwable $e) {
    try { $conn->rollback(); } catch (Throwable $ignore) {}

    $code = (int)$e->getCode();
    if ($code < 400 || $code > 599) $code = 500;
    json_fail($e->getMessage(), $code);
}