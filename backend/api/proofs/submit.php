<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_upload.php';

require_csrf();

$userId = (int)($_SESSION['user_id'] ?? 0);
$role = (string)($_SESSION['role'] ?? '');

if ($userId <= 0 || $role !== 'user') {
    json_fail('Unauthorized.', 401);
}

if (!isset($_FILES['proof'])) {
    json_fail('Proof image is required.', 422);
}

$customerNote = trim((string)($_POST['customer_note'] ?? ''));
if (mb_strlen($customerNote) > 255) {
    $customerNote = mb_substr($customerNote, 0, 255);
}

$saved = null;

try {
    $conn->begin_transaction();

    // Lock this user row to prevent double-submit race conditions.
    $userStmt = $conn->prepare("
        SELECT id, status
        FROM users
        WHERE id = ? AND role = 'user'
        LIMIT 1
        FOR UPDATE
    ");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    $userStmt->close();

    if (!$user) {
        $conn->rollback();
        json_fail('User account not found.', 404);
    }

    if ((string)$user['status'] !== 'active') {
        $conn->rollback();
        json_fail('Your account is inactive. You cannot submit proof right now.', 403);
    }

    $pendingStmt = $conn->prepare("
        SELECT id
        FROM purchase_proofs
        WHERE user_id = ? AND status = 'pending'
        LIMIT 1
    ");
    $pendingStmt->bind_param("i", $userId);
    $pendingStmt->execute();
    $hasPending = $pendingStmt->get_result()->num_rows > 0;
    $pendingStmt->close();

    if ($hasPending) {
        $conn->rollback();
        json_fail('You already have a pending proof for review. Please wait for admin approval or rejection before submitting another one.', 409);
    }

    $saved = save_purchase_proof_image($_FILES['proof']);

    $stmt = $conn->prepare("
        INSERT INTO purchase_proofs (
            user_id,
            proof_file,
            original_name,
            mime_type,
            file_size,
            customer_note,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");

    if (!$stmt) {
        throw new RuntimeException('Failed to prepare proof submission: ' . $conn->error);
    }

    $stmt->bind_param(
        "isssis",
        $userId,
        $saved['filename'],
        $saved['original_name'],
        $saved['mime_type'],
        $saved['file_size'],
        $customerNote
    );

    if (!$stmt->execute()) {
        throw new RuntimeException('Failed to save proof submission: ' . $stmt->error);
    }

    $newId = (int)$conn->insert_id;
    $stmt->close();

    $conn->commit();

    json_ok([
        'id' => $newId,
        'message' => 'Proof submitted successfully. Please wait for admin review.',
    ]);
} catch (Throwable $e) {
    if ($conn->errno === 0) {
        // no-op
    }

    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // ignore rollback error
    }

    if ($saved && !empty($saved['filename'])) {
        delete_purchase_proof_file($saved['filename']);
    }

    json_fail($e->getMessage(), 422);
}