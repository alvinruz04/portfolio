<?php
require '../db.php';

// Default password
$plainPassword = '1234';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Prepare statement
$stmt = $conn->prepare("INSERT INTO clients (email, password, phone, created_at) VALUES (?, ?, ?, ?)");

// Loop through 10 clients
for ($i = 1; $i <= 10; $i++) {
    $email = "client{$i}@studio.com";
    $phone = "0917000000" . str_pad($i, 2, '0', STR_PAD_LEFT); // e.g., 09170000001
    $createdAt = date('Y-m-d H:i:s');

    $stmt->bind_param("ssss", $email, $hashedPassword, $phone, $createdAt);

    if ($stmt->execute()) {
        echo "✅ Seeded: $email ($phone)<br>";
    } else {
        echo "❌ Error seeding $email: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();
