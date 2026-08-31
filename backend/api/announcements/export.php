<?php
declare(strict_types=1);

require_once __DIR__ . '/../../_bootstrap_download.php';

require_admin_download();

$q         = trim((string)($_GET['q'] ?? ''));
$is_active = trim((string)($_GET['is_active'] ?? ''));

$where = ['1=1'];
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

if ($is_active !== '' && ($is_active === '0' || $is_active === '1')) {
    $where[] = 'is_active = ?';
    $types .= 'i';
    $vals[] = (int)$is_active;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT
        id,
        title,
        image,
        image_original_name,
        is_active,
        created_at,
        updated_at
    FROM announcements
    {$whereSql}
    ORDER BY created_at DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    json_fail_download('Failed to prepare export query: ' . $conn->error, 500);
}

if ($types !== '') {
    $stmt->bind_param($types, ...$vals);
}

$stmt->execute();
$res = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="announcements_report.csv"');
header('X-Content-Type-Options: nosniff');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');

fputcsv($out, [
    'ID',
    'Title',
    'Image',
    'Image Original Name',
    'Status',
    'Created At',
    'Updated At',
]);

while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
        $r['id'],
        $r['title'],
        '/uploads/announcements/' . $r['image'],
        $r['image_original_name'],
        ((int)$r['is_active'] === 1 ? 'active' : 'inactive'),
        $r['created_at'],
        $r['updated_at'],
    ]);
}

fclose($out);
$stmt->close();
exit;