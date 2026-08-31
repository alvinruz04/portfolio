<?php
declare(strict_types=1);

define(
    'ANNOUNCEMENT_UPLOAD_DIR',
    dirname(__DIR__, 2) . '/frontend/public/uploads/announcements'
);

require_once __DIR__ . '/../../backend/api/announcements/update.php';