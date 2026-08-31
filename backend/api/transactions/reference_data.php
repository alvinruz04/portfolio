<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('GET');
require_admin();

$services = [];
$result = $conn->query(
    "SELECT id, code, name, description
     FROM service_types
     WHERE status = 'active'
     ORDER BY sort_order ASC, name ASC"
);

while ($row = $result->fetch_assoc()) {
    $services[] = [
        'id' => (int) $row['id'],
        'code' => (string) $row['code'],
        'name' => (string) $row['name'],
        'description' => $row['description'],
    ];
}

$pests = [];
$result = $conn->query(
    "SELECT id, name
     FROM pest_types
     WHERE status = 'active'
     ORDER BY sort_order ASC, name ASC"
);

while ($row = $result->fetch_assoc()) {
    $pests[] = [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
    ];
}

json_ok([
    'service_types' => $services,
    'pest_types' => $pests,
]);
