<?php
declare(strict_types=1);

const ANNOUNCEMENT_MAX_BYTES = 3145728; // 3MB
const ANNOUNCEMENT_REQUIRED_WIDTH = 1080;
const ANNOUNCEMENT_REQUIRED_HEIGHT = 1350;

function announcement_upload_dir(): string
{
    if (defined('ANNOUNCEMENT_UPLOAD_DIR')) {
        $customDir = rtrim((string)ANNOUNCEMENT_UPLOAD_DIR, DIRECTORY_SEPARATOR);
        if (!is_dir($customDir) && !mkdir($customDir, 0755, true)) {
            throw new RuntimeException('Failed to create custom announcement upload directory.');
        }
        return realpath($customDir) ?: $customDir;
    }

    $projectRoot = dirname(__DIR__, 3);

    $candidates = [
        $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'announcements',
        $projectRoot . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'announcements',
    ];

    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = rtrim((string)$_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
        $candidates[] = $docRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'announcements';
    }

    foreach ($candidates as $dir) {
        $webRoot = dirname(dirname($dir));

        if (!is_dir($webRoot)) {
            continue;
        }

        $uploadsRoot = dirname($dir);
        if (!is_dir($uploadsRoot) && !mkdir($uploadsRoot, 0755, true)) {
            continue;
        }

        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            continue;
        }

        if (is_writable($dir)) {
            return realpath($dir) ?: $dir;
        }
    }

    throw new RuntimeException('Announcement upload directory is not available or writable.');
}

function announcement_validate_image_upload(array $file): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid image upload.');
    }

    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        $error = (int)$file['error'];

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('Image is too large.');
        }

        if ($error === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('Announcement image is required.');
        }

        throw new RuntimeException('Image upload failed.');
    }

    $tmpName = (string)$file['tmp_name'];
    $size = (int)$file['size'];
    $originalName = basename((string)$file['name']);

    if (!is_uploaded_file($tmpName)) {
        throw new RuntimeException('Invalid uploaded image.');
    }

    if ($size <= 0) {
        throw new RuntimeException('Uploaded image is empty.');
    }

    if ($size > ANNOUNCEMENT_MAX_BYTES) {
        throw new RuntimeException('Image must not exceed 3MB.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string)finfo_file($finfo, $tmpName) : '';
    if ($finfo) {
        finfo_close($finfo);
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!array_key_exists($mime, $allowed)) {
        throw new RuntimeException('Invalid image type. Allowed: WEBP, JPG, JPEG, PNG.');
    }

    $imageInfo = @getimagesize($tmpName);
    if (!$imageInfo) {
        throw new RuntimeException('Uploaded file is not a valid image.');
    }

    $width = (int)$imageInfo[0];
    $height = (int)$imageInfo[1];

    if ($width !== ANNOUNCEMENT_REQUIRED_WIDTH || $height !== ANNOUNCEMENT_REQUIRED_HEIGHT) {
        throw new RuntimeException(
            'Invalid image dimension. Required size is ' .
            ANNOUNCEMENT_REQUIRED_WIDTH . 'x' . ANNOUNCEMENT_REQUIRED_HEIGHT . ' px.'
        );
    }

    return [
        'tmp_name' => $tmpName,
        'mime' => $mime,
        'extension' => $allowed[$mime],
        'original_name' => $originalName,
        'size' => $size,
        'width' => $width,
        'height' => $height,
    ];
}

function announcement_store_image(array $file): array
{
    $meta = announcement_validate_image_upload($file);
    $dir = announcement_upload_dir();

    $filename = 'ann_' . date('Ymd_His') . '_' . bin2hex(random_bytes(8)) . '.' . $meta['extension'];
    $target = $dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($meta['tmp_name'], $target)) {
        throw new RuntimeException('Failed to save announcement image.');
    }

    @chmod($target, 0644);

    return [
        'filename' => $filename,
        'original_name' => $meta['original_name'],
    ];
}

function announcement_delete_image(?string $filename): void
{
    $filename = basename((string)$filename);

    if ($filename === '') {
        return;
    }

    try {
        $dir = announcement_upload_dir();
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    } catch (Throwable $e) {
        // Do not block DB operation if file cleanup fails.
    }
}