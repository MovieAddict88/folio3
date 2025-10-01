<?php
session_start();
require_once '../config/db.php';
require_once '../src/Invoice.php';
require_once '../src/Payment.php';
require_once '../src/Notification.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$invoiceId = $_POST['invoice_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$paymentMethod = $_POST['payment_method'] ?? null;

if (!$invoiceId || !$amount || !$paymentMethod) {
    header('Location: dashboard.php?error=Invalid payment data.');
    exit;
}

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$invoiceData = $invoice->getById($invoiceId);

// Authorization and validation
if (!$invoiceData || $invoiceData['user_id'] != $_SESSION['user_id'] || $invoiceData['status'] !== 'pending') {
    header('Location: dashboard.php?error=Payment cannot be processed.');
    exit;
}

// Simulate payment processing
$transactionId = 'TXN_' . strtoupper(uniqid());

$payment = new Payment($pdo);
$notification = new Notification($pdo);

try {
    $pdo->beginTransaction();

    // 1. Record the payment
    $payment->create($invoiceId, $amount, $paymentMethod, $transactionId);

    // 2. Update invoice status
    $invoice->updateStatus($invoiceId, 'paid');

    // 3. Create a notification for all admins
    // In a more complex app, you'd fetch all admin users
    $adminUserId = 1; // Assuming admin user has ID 1
    $message = "Payment of $$amount for Invoice #$invoiceId received via $paymentMethod.";
    $notification->create($adminUserId, $message);

    $pdo->commit();

    // Redirect to a receipt page
    header('Location: receipt.php?invoice_id=' . $invoiceId);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // In a real app, log the error
    header('Location: payment.php?id=' . $invoiceId . '&error=An error occurred. Please try again.');
    exit;
}