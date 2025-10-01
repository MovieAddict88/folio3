<?php
session_start();
require_once '../config/db.php';
require_once '../src/Invoice.php';
require_once '../src/Payment.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$invoiceId = $_GET['invoice_id'] ?? null;

if (!$invoiceId) {
    header('Location: dashboard.php?error=No invoice specified.');
    exit;
}

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$payment = new Payment($pdo);

$invoiceData = $invoice->getById($invoiceId);
$paymentData = $payment->findByInvoiceId($invoiceId);

// Authorization check
if (!$invoiceData || $invoiceData['user_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php?error=Access denied.');
    exit;
}

if (!$paymentData) {
     header('Location: view_invoice.php?id=' . $invoiceId . '&error=Payment details not found.');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .receipt-box {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="receipt-box">
            <div class="text-center">
                <h2 class="mb-4">Payment Successful!</h2>
                <p class="lead">Thank you for your payment.</p>
            </div>
            <hr>
            <h4 class="mb-3">Receipt Details</h4>
            <ul class="list-unstyled">
                <li><strong>Invoice #:</strong> <?php echo htmlspecialchars($invoiceData['id']); ?></li>
                <li><strong>Payment Date:</strong> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($paymentData['payment_date']))); ?></li>
                <li><strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentData['payment_method']); ?></li>
                <li><strong>Transaction ID:</strong> <?php echo htmlspecialchars($paymentData['transaction_id']); ?></li>
                <li class="mt-3"><strong>Amount Paid:</strong> <span class="h5">$<?php echo htmlspecialchars(number_format($paymentData['amount'], 2)); ?></span></li>
            </ul>
            <hr>
            <div class="text-center mt-4">
                <a href="view_invoice.php?id=<?php echo htmlspecialchars($invoiceData['id']); ?>" class="btn btn-primary">View Invoice</a>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>