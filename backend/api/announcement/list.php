<?php
declare(strict_types=1);

require_once __DIR__ . '/../_bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = min(20, max(5, (int)($_GET['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

$q = trim((string)($_GET['q'] ?? ''));

$where = ['is_active = 1'];
$types = '';
$vals = [];

if ($q !== '') {
    if (mb_strlen($q) > 100) {
        $q = mb_substr($q, 0, 100);
    }

    $like = "%{$q}%";
    $where[] = 'title LIKE ?';
    $types .= 's';
    $vals[] = $like;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

/**
 * Total count
 */
$countSql = "SELECT COUNT(*) AS total FROM announcements {$whereSql}";
$stmt = $conn->prepare($countSql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare count query.',
    ]);
    exit;
}

if ($types !== '') {
    $stmt->bind_param($types, ...$vals);
}

$stmt->execute();
$resTotal = $stmt->get_result();
$total = 0;

if ($rowTotal = $resTotal->fetch_assoc()) {
    $total = (int)$rowTotal['total'];
}

$stmt->close();

/**
 * Main list
 * Important:
 * Do not include image here.
 * This keeps the public table light and prevents image loading until View is clicked.
 */
$sql = "
    SELECT
        id,
        title,
        created_at,
        updated_at
    FROM announcements
    {$whereSql}
    ORDER BY created_at DESC, id DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare announcement list query.',
    ]);
    exit;
}

$types2 = $types . 'ii';
$vals2 = $vals;
$vals2[] = $limit;
$vals2[] = $offset;

$stmt->bind_param($types2, ...$vals2);
$stmt->execute();

$res = $stmt->get_result();
$rows = [];

while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'rows' => $rows,
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'totalPages' => max(1, (int)ceil($total / $limit)),
]);