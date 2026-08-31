<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';
require_once __DIR__ . '/_upload.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    json_fail('Invalid proof ID.', 422);
}

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$currentRole = (string)($_SESSION['role'] ?? '');

if ($currentUserId <= 0) {
    json_fail('Unauthorized.', 401);
}

$stmt = $conn->prepare("
    SELECT id, user_id, proof_file, mime_type, status
    FROM purchase_proofs
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    json_fail('Proof not found.', 404);
}

$isAdmin = $currentRole === 'admin';
$isOwner = $currentRole === 'user' && (int)$row['user_id'] === $currentUserId;

if (!$isAdmin && !$isOwner) {
    json_fail('Forbidden.', 403);
}

if ((string)$row['status'] !== 'pending') {
    json_fail('Proof image is no longer available.', 404);
}

$filename = trim((string)($row['proof_file'] ?? ''));
if ($filename === '') {
    json_fail('Proof image has already been deleted.', 404);
}

$path = proof_upload_dir() . DIRECTORY_SEPARATOR . basename($filename);

if (!is_file($path)) {
    json_fail('Proof image file not found.', 404);
}

$mime = (string)($row['mime_type'] ?? 'image/jpeg');
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    $mime = 'image/jpeg';
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=0, no-cache');

readfile($path);
exit;