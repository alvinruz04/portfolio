<?php
declare(strict_types=1);

function proof_upload_dir(): string {
    $projectRoot = dirname(__DIR__, 3);
    return $projectRoot . '/frontend/public/uploads/proofs';
}

function ensure_proof_upload_dir(): string {
    $dir = proof_upload_dir();

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Failed to create proof upload directory: ' . $dir);
        }
    }

    if (!is_writable($dir)) {
        throw new RuntimeException('Proof upload directory is not writable: ' . $dir);
    }

    // Prevent direct browser access on Apache/cPanel.
    // Images should be viewed through api/proofs/view.php only.
    $htaccess = $dir . DIRECTORY_SEPARATOR . '.htaccess';
    if (!is_file($htaccess)) {
        @file_put_contents($htaccess, implode("\n", [
            'Options -Indexes',
            '<IfModule mod_authz_core.c>',
            'Require all denied',
            '</IfModule>',
            '<IfModule !mod_authz_core.c>',
            'Deny from all',
            '</IfModule>',
            '',
        ]));
    }

    return $dir;
}

function sanitize_original_filename(string $name): string {
    $name = basename($name);
    $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name) ?: 'proof-image';
    $name = trim($name);

    if ($name === '') {
        $name = 'proof-image';
    }

    if (mb_strlen($name) > 255) {
        $name = mb_substr($name, 0, 255);
    }

    return $name;
}

function proof_filename_from_mime(string $mime): string {
    $map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $ext = $map[$mime] ?? 'jpg';

    return 'proof_' . date('Ymd_His') . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
}

function save_purchase_proof_image(array $file): array {
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Proof upload failed. Upload error code: ' . $error);
    }

    $size = (int)($file['size'] ?? 0);

    if ($size <= 0) {
        throw new RuntimeException('Uploaded proof is empty.');
    }

    // Recommended for purchase proof photos on shared hosting.
    if ($size > 5 * 1024 * 1024) {
        throw new RuntimeException('Proof image must not exceed 5MB.');
    }

    $tmp = (string)($file['tmp_name'] ?? '');

    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid uploaded proof image.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);

    $allowed = [
        'image/jpeg' => true,
        'image/png'  => true,
        'image/webp' => true,
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP proof images are allowed.');
    }

    $imageInfo = @getimagesize($tmp);
    if ($imageInfo === false) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    $width = (int)($imageInfo[0] ?? 0);
    $height = (int)($imageInfo[1] ?? 0);

    if ($width < 300 || $height < 300) {
        throw new RuntimeException('Proof image is too small. Minimum size is 300x300 pixels.');
    }

    if ($width > 8000 || $height > 8000) {
        throw new RuntimeException('Proof image is too large in dimensions. Maximum size is 8000x8000 pixels.');
    }

    $dir = ensure_proof_upload_dir();
    $filename = proof_filename_from_mime($mime);
    $dest = $dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Failed to save uploaded proof image.');
    }

    @chmod($dest, 0644);

    return [
        'filename' => $filename,
        'original_name' => sanitize_original_filename((string)($file['name'] ?? 'proof-image')),
        'mime_type' => $mime,
        'file_size' => $size,
        'width' => $width,
        'height' => $height,
    ];
}

function delete_purchase_proof_file(?string $filename): bool {
    $filename = trim((string)$filename);
    if ($filename === '') return true;

    $path = proof_upload_dir() . DIRECTORY_SEPARATOR . basename($filename);

    if (!is_file($path)) {
        return true;
    }

    return @unlink($path);
}