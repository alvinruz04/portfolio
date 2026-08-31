<?php
session_start();

// OPTIONAL: Admin-only access
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

// Validate input
if (!isset($_GET['file']) || !preg_match('/^[a-zA-Z0-9_.-]+\.(jpg|jpeg|png|webp|pdf)$/i', $_GET['file'])) {
    http_response_code(400);
    echo "Invalid file.";
    exit;
}

$filename = $_GET['file'];
$filepath = __DIR__ . "/uploads/proofs/" . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    echo "Image not found.";
    exit;
}

// Get MIME type safely
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filepath);
finfo_close($finfo);

header("Content-Type: $mime");
readfile($filepath);
