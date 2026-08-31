<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/_upload.php';

require_admin();
require_csrf();

$adminId = (int)($_SESSION['user_id'] ?? 0);

$id = (int)($_POST['id'] ?? 0);
$action = trim((string)($_POST['action'] ?? ''));

if ($id <= 0) {
    json_fail('Invalid proof ID.', 422);
}

if (!in_array($action, ['approve', 'reject'], true)) {
    json_fail('Invalid review action.', 422);
}

$points = 0;
$reason = null;

if ($action === 'approve') {
    $points = (int)($_POST['points'] ?? 0);

    if ($points <= 0) {
        json_fail('Points must be greater than 0.', 422);
    }

    if ($points > 999999) {
        json_fail('Points value is too high.', 422);
    }
}

if ($action === 'reject') {
    $reason = trim((string)($_POST['reason'] ?? ''));

    if ($reason === '') {
        json_fail('Rejection reason is required.', 422);
    }

    if (mb_strlen($reason) > 500) {
        $reason = mb_substr($reason, 0, 500);
    }
}

$oldFile = '';

try {
    $conn->begin_transaction();

    $get = $conn->prepare("
        SELECT *
        FROM purchase_proofs
        WHERE id = ?
        LIMIT 1
        FOR UPDATE
    ");
    $get->bind_param("i", $id);
    $get->execute();
    $proof = $get->get_result()->fetch_assoc();
    $get->close();

    if (!$proof) {
        $conn->rollback();
        json_fail('Proof submission not found.', 404);
    }

    if ((string)$proof['status'] !== 'pending') {
        $conn->rollback();
        json_fail('This proof has already been reviewed.', 409);
    }

    $oldFile = (string)($proof['proof_file'] ?? '');
    $proofUserId = (int)$proof['user_id'];

    if ($action === 'approve') {
        $credit = $conn->prepare("
            UPDATE users
            SET user_points = user_points + ?
            WHERE id = ?
            LIMIT 1
        ");
        $credit->bind_param("ii", $points, $proofUserId);

        if (!$credit->execute()) {
            throw new RuntimeException('Failed to credit points: ' . $credit->error);
        }

        $credit->close();

        $status = 'approved';
        $update = $conn->prepare("
            UPDATE purchase_proofs
            SET
                status = ?,
                points_awarded = ?,
                rejection_reason = NULL,
                reviewed_by = ?,
                reviewed_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");
        $update->bind_param("siii", $status, $points, $adminId, $id);
    } else {
        $status = 'rejected';
        $zero = 0;

        $update = $conn->prepare("
            UPDATE purchase_proofs
            SET
                status = ?,
                points_awarded = ?,
                rejection_reason = ?,
                reviewed_by = ?,
                reviewed_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");
        $update->bind_param("sisii", $status, $zero, $reason, $adminId, $id);
    }

    if (!$update->execute()) {
        throw new RuntimeException('Failed to update proof review: ' . $update->error);
    }

    $update->close();

    $conn->commit();
} catch (Throwable $e) {
    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // ignore
    }

    json_fail($e->getMessage(), 500);
}

// File deletion happens after DB review so the user immediately cannot view it anymore.
$fileDeleted = delete_purchase_proof_file($oldFile);

if ($fileDeleted) {
    $clear = $conn->prepare("
        UPDATE purchase_proofs
        SET proof_file = NULL, proof_deleted_at = NOW()
        WHERE id = ?
        LIMIT 1
    ");
    $clear->bind_param("i", $id);
    $clear->execute();
    $clear->close();
}

audit_write($conn, [
    'branch_id'   => 0,
    'user_id'     => $adminId,
    'action'      => $action === 'approve' ? 'PROOF_APPROVE' : 'PROOF_REJECT',
    'entity_type' => 'purchase_proof',
    'entity_id'   => $id,
    'meta' => [
        'message' => $action === 'approve'
            ? 'Proof approved and points credited'
            : 'Proof rejected',
        'proof_user_id' => $proofUserId ?? 0,
        'points_awarded' => $points,
        'reason' => $reason,
        'file_deleted' => $fileDeleted,
    ],
]);

json_ok([
    'message' => $action === 'approve'
        ? 'Proof approved. Points credited and image deleted.'
        : 'Proof rejected. Image deleted.',
    'file_deleted' => $fileDeleted,
]);