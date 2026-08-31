<?php
declare(strict_types=1);

/** @var mysqli $conn */

require_once __DIR__ . '/../_bootstrap.php';

require_method('GET');
require_admin();

$q = trim((string) ($_GET['q'] ?? ''));
$limit = min(500, max(20, (int) ($_GET['limit'] ?? 250)));

if (mb_strlen($q) > 120) {
    $q = mb_substr($q, 0, 120);
}

if ($q === '') {
    $statement = $conn->prepare(
        "SELECT
            c.id,
            c.customer_type,
            c.display_name,
            c.contact_person,
            c.contact_number,
            c.email_address,
            c.billing_address,
            c.tin
         FROM customers c
         WHERE c.status = 'active'
         ORDER BY c.display_name ASC
         LIMIT ?"
    );
    $statement->bind_param('i', $limit);
} else {
    $like = "%{$q}%";
    $statement = $conn->prepare(
        "SELECT
            c.id,
            c.customer_type,
            c.display_name,
            c.contact_person,
            c.contact_number,
            c.email_address,
            c.billing_address,
            c.tin
         FROM customers c
         WHERE c.status = 'active'
           AND (
                c.display_name LIKE ?
                OR c.contact_person LIKE ?
                OR c.contact_number LIKE ?
                OR c.email_address LIKE ?
           )
         ORDER BY c.display_name ASC
         LIMIT ?"
    );
    $statement->bind_param('ssssi', $like, $like, $like, $like, $limit);
}

$statement->execute();
$result = $statement->get_result();
$customers = [];
$customerIds = [];

while ($row = $result->fetch_assoc()) {
    $id = (int) $row['id'];
    $customerIds[] = $id;
    $customers[$id] = [
        'id' => $id,
        'customer_type' => (string) $row['customer_type'],
        'display_name' => (string) $row['display_name'],
        'contact_person' => $row['contact_person'],
        'contact_number' => $row['contact_number'],
        'email_address' => $row['email_address'],
        'billing_address' => $row['billing_address'],
        'tin' => $row['tin'],
        'sites' => [],
    ];
}

$statement->close();

if ($customerIds !== []) {
    $placeholders = implode(', ', array_fill(0, count($customerIds), '?'));
    $siteStatement = $conn->prepare(
        "SELECT
            id,
            customer_id,
            site_name,
            address_line,
            barangay,
            city,
            province,
            property_type,
            contact_person,
            contact_number,
            notes
         FROM customer_sites
         WHERE status = 'active'
           AND customer_id IN ({$placeholders})
         ORDER BY customer_id ASC, site_name ASC, id ASC"
    );

    $types = str_repeat('i', count($customerIds));
    $params = [$types];

    foreach ($customerIds as $index => $id) {
        $params[] = &$customerIds[$index];
    }

    $siteStatement->bind_param(...$params);
    $siteStatement->execute();
    $siteResult = $siteStatement->get_result();

    while ($site = $siteResult->fetch_assoc()) {
        $customerId = (int) $site['customer_id'];

        if (!isset($customers[$customerId])) {
            continue;
        }

        $customers[$customerId]['sites'][] = [
            'id' => (int) $site['id'],
            'site_name' => $site['site_name'],
            'address_line' => (string) $site['address_line'],
            'barangay' => $site['barangay'],
            'city' => $site['city'],
            'province' => $site['province'],
            'property_type' => $site['property_type'],
            'contact_person' => $site['contact_person'],
            'contact_number' => $site['contact_number'],
            'notes' => $site['notes'],
        ];
    }

    $siteStatement->close();
}

json_ok([
    'customers' => array_values($customers),
]);
