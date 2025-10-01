<?php
require_once 'config/db.php';

$pdo = getDBConnection();

// Create user
$username = 'testuser';
$password = password_hash('password', PASSWORD_DEFAULT);
$email = 'testuser@example.com';
$stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $email]);
$userId = $pdo->lastInsertId();

// Create invoice
$totalAmount = 100.00;
$dueDate = date('Y-m-d', strtotime('+7 days'));
$status = 'rejected';
$stmt = $pdo->prepare("INSERT INTO invoices (user_id, total_amount, balance, due_date, status) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$userId, $totalAmount, $totalAmount, $dueDate, $status]);
$invoiceId = $pdo->lastInsertId();

echo "User and rejected invoice created successfully.\n";
?>