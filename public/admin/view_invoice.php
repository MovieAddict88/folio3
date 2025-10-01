<?php
session_start();
require_once '../../config/db.php';
require_once '../../src/Invoice.php';
require_once '../../src/Payment.php';

// Auth check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?error=Access denied.');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: invoices.php?error=No invoice selected.');
    exit;
}

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$details = $invoice->getDetailsById($_GET['id']);

if (!$details) {
    header('Location: invoices.php?error=Invoice not found.');
    exit;
}

$invoiceData = $details['invoice'];
$items = $details['items'];
$paymentData = null;

if ($invoiceData['status'] === 'paid') {
    $payment = new Payment($pdo);
    $paymentData = $payment->findByInvoiceId($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo htmlspecialchars($invoiceData['id']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .invoice-box {
            max-width: 800px;
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
    <?php include '_nav.php'; ?>
    <div class="container mt-5">
        <div class="invoice-box">
            <div class="row">
                <div class="col-sm-6">
                    <h2 class="mb-4">Invoice #<?php echo htmlspecialchars($invoiceData['id']); ?></h2>
                    <strong>Billed To:</strong><br>
                    <?php echo htmlspecialchars($invoiceData['username']); ?><br>
                    <?php echo htmlspecialchars($invoiceData['email']); ?>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <strong>Status:</strong> <span class="badge bg-<?php echo $invoiceData['status'] === 'paid' ? 'success' : ($invoiceData['status'] === 'pending' ? 'warning' : 'danger'); ?>"><?php echo ucfirst(htmlspecialchars($invoiceData['status'])); ?></span><br>
                    <strong>Date Created:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($invoiceData['created_at']))); ?><br>
                    <strong>Date Due:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($invoiceData['due_date']))); ?>
                </div>
            </div>

            <hr>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['quantity'] * $item['price'], 2)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Grand Total:</th>
                        <th>$<?php echo htmlspecialchars(number_format($invoiceData['total_amount'], 2)); ?></th>
                    </tr>
                </tfoot>
            </table>

            <?php if ($paymentData): ?>
            <hr>
            <div class="row mt-4">
                <div class="col">
                    <h4>Payment Information</h4>
                    <ul class="list-unstyled">
                        <li><strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentData['payment_method']); ?></li>
                        <li><strong>Transaction ID:</strong> <?php echo htmlspecialchars($paymentData['transaction_id']); ?></li>
                        <li><strong>Payment Date:</strong> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($paymentData['payment_date']))); ?></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                 <a href="invoices.php" class="btn btn-secondary">Back to Invoices</a>
                 <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
            </div>
        </div>
    </div>
</body>
</html>