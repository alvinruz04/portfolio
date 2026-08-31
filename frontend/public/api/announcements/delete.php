<?php
declare(strict_types=1);

define(
    'ANNOUNCEMENT_UPLOAD_DIR',
    rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/uploads/announcements'
);

require_once '/home/a2pxyb754zh7/backend/api/announcements/delete.php';