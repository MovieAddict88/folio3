<?php
session_start();
require_once '../config/db.php';
require_once '../src/Invoice.php';
require_once '../src/Payment.php';
require_once '../src/Notification.php';

// User must be logged in to have a session context
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- Handle Callback from External Gateway ---

$invoiceId = $_GET['invoice_id'] ?? null;
$transactionId = $_GET['transaction_id'] ?? null;
$status = $_GET['status'] ?? 'failed';
$paymentMethod = $_GET['payment_method'] ?? 'Unknown';
$amount = $_GET['amount'] ?? null;

// Handle failed or cancelled payments
if ($status !== 'success') {
    $errorMessage = $status === 'cancelled' ? 'Payment was cancelled.' : 'Payment failed.';
    header('Location: payment.php?id=' . $invoiceId . '&error=' . urlencode($errorMessage));
    exit;
}

// Validate essential data
if (!$invoiceId || !$transactionId || !$amount) {
    header('Location: dashboard.php?error=Invalid payment confirmation data.');
    exit;
}

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$invoiceData = $invoice->getById($invoiceId);

// Authorization and validation
// Ensure the invoice exists, belongs to the logged-in user, and is still pending.
if (!$invoiceData || $invoiceData['user_id'] != $_SESSION['user_id'] || $invoiceData['status'] !== 'pending') {
    header('Location: dashboard.php?error=Payment cannot be processed for this invoice.');
    exit;
}

// --- Finalize Payment ---

$payment = new Payment($pdo);
$notification = new Notification($pdo);

try {
    $pdo->beginTransaction();

    // 1. Record the payment with the external transaction ID
    $payment->create($invoiceId, $amount, $paymentMethod, $transactionId);

    // 2. Update the invoice status to 'paid'
    $invoice->updateStatus($invoiceId, 'paid');

    // 3. Create a notification for the admin
    $adminUserId = 1; // Assuming admin user ID is 1
    $message = "Payment of $" . number_format($amount, 2) . " for Invoice #$invoiceId was confirmed via $paymentMethod (Transaction ID: $transactionId).";
    $notification->create($adminUserId, $message);

    $pdo->commit();

    // Redirect to the final receipt page
    header('Location: receipt.php?invoice_id=' . $invoiceId);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    // In a real app, log the error message ($e->getMessage())
    header('Location: payment.php?id=' . $invoiceId . '&error=A critical error occurred while finalizing your payment. Please contact support.');
    exit;
}
?>