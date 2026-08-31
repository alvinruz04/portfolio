<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_method('POST');
require_csrf();

destroy_current_session();

json_ok([
    'message' => 'Logged out successfully.',
]);
