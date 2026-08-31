<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

require_method('GET');
require_auth();

json_ok([
    'csrf' => ensure_csrf_token(),
]);
