<?php
declare(strict_types=1);

function seller_upload_dir(): string {
    /*
     * backend/api/sellers/_upload.php
     *
     * Local:
     *   .../youglowbabe-website/frontend/public/uploads/sellers
     *
     * Production:
     *   /home/a2pxyb754zh7/public_html/uploads/sellers
     */

    $projectRoot = dirname(__DIR__, 3);

    // Production GoDaddy/cPanel path
    $productionPublic = $projectRoot . '/public_html';

    if (is_dir($productionPublic)) {
        return $productionPublic . '/uploads/sellers';
    }

    // Local Vue public folder
    return $projectRoot . '/frontend/public/uploads/sellers';
}

function seller_upload_url_name(string $originalName): string {
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeExt = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? $ext : 'jpg';
    return 'seller_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $safeExt;
}

function save_seller_avatar(array $file): string {
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Avatar upload failed. Upload error code: ' . $error);
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Avatar must not exceed 5MB.');
    }

    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid uploaded file.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    $dir = seller_upload_dir();

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Failed to create upload directory: ' . $dir);
        }
    }

    if (!is_writable($dir)) {
        throw new RuntimeException('Upload directory is not writable: ' . $dir);
    }

    $filename = seller_upload_url_name((string)($file['name'] ?? 'avatar.jpg'));
    $dest = $dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Failed to move uploaded avatar to: ' . $dest);
    }

    return $filename;
}

function delete_seller_avatar_file(?string $filename): void {
    $filename = trim((string)$filename);
    if ($filename === '') return;

    $path = seller_upload_dir() . DIRECTORY_SEPARATOR . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

function normalize_optional_link(?string $value): ?string {
    $value = trim((string)$value);
    if ($value === '') return null;
    if (!preg_match('#^https?://#i', $value)) {
        $value = 'https://' . $value;
    }
    return $value;
}