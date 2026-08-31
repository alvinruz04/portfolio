<?php
require '../db.php';

// Admin account to seed
$username = 'admin@studio.com';
$plainPassword = 'admin123';

// Hash the password securely
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Prepare and execute insert
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {
    echo "✅ Admin account seeded successfully.";
} else {
    echo "❌ Error seeding admin: " . $stmt->error;
}
