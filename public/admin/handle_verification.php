<?php
session_start();
require_once '../../config/db.php';
require_once '../../src/Invoice.php';
require_once '../../src/Payment.php';
require_once '../../src/Notification.php';
require_once '../../src/User.php';

// Authenticate admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?error=Access denied.');
    exit;
}

// Check for POST request and required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['invoice_id']) || !isset($_POST['action'])) {
    header('Location: invoices.php?error=Invalid request.');
    exit;
}

$invoiceId = $_POST['invoice_id'];
$action = $_POST['action'];

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$payment = new Payment($pdo);
$notification = new Notification($pdo);
$user = new User($pdo);

$invoiceData = $invoice->getById($invoiceId);

// Validate invoice exists and is pending verification
if (!$invoiceData || $invoiceData['status'] !== 'pending_verification') {
    header('Location: invoices.php?error=Invalid invoice or action cannot be performed.');
    exit;
}

$customer = $user->getById($invoiceData['user_id']);
if (!$customer) {
    header('Location: invoices.php?error=Could not find the customer associated with this invoice.');
    exit;
}

try {
    $pdo->beginTransaction();

    if ($action === 'approve') {
        // 1. Update invoice status to 'paid'
        $invoice->updateStatus($invoiceId, 'paid');

        // 2. Create notification for the user
        $message = "Your payment for Invoice #{$invoiceId} has been approved. Thank you!";
        $notification->create($customer['id'], $message);

        $successMessage = "Invoice #{$invoiceId} has been approved and marked as paid.";

    } elseif ($action === 'reject') {
        // 1. Revert invoice status to 'rejected'
        $invoice->updateStatus($invoiceId, 'rejected');

        // 2. Delete the payment record associated with this invoice
        $payment->deleteByInvoiceId($invoiceId);

        // 3. Create a notification for the user
        $message = "Your payment for Invoice #{$invoiceId} was rejected. Please try submitting your payment again or contact support for assistance.";
        $notification->create($customer['id'], $message);

        $successMessage = "Invoice #{$invoiceId} has been rejected. The user has been notified.";

    } else {
        throw new Exception("Invalid action specified.");
    }

    $pdo->commit();
    header('Location: invoices.php?success=' . urlencode($successMessage));
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Verification handling error: ' . $e->getMessage());
    header('Location: view_invoice.php?id=' . $invoiceId . '&error=An error occurred while processing the action.');
    exit;
}