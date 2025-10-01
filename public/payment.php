<?php
session_start();
require_once '../config/db.php';
require_once '../src/Invoice.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php?error=No invoice selected.');
    exit;
}

$pdo = getDBConnection();
$invoice = new Invoice($pdo);
$invoiceData = $invoice->getById($_GET['id']);

if (!$invoiceData || $invoiceData['user_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php?error=Access denied or invoice not found.');
    exit;
}

if ($invoiceData['status'] !== 'pending') {
    header('Location: view_invoice.php?id=' . $_GET['id'] . '&error=Invoice is not pending.');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for Invoice #<?php echo htmlspecialchars($invoiceData['id']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Payment for Invoice #<?php echo htmlspecialchars($invoiceData['id']); ?></h3>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Total Amount: $<?php echo htmlspecialchars(number_format($invoiceData['total_amount'], 2)); ?></h5>
                        <p class="card-text">Please select a payment method:</p>
                        <form action="handle_payment.php" method="POST">
                            <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoiceData['id']); ?>">
                            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($invoiceData['total_amount']); ?>">
                            <div class="list-group">
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="radio" name="payment_method" value="GCASH" required>
                                    GCASH
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="radio" name="payment_method" value="PAYMAYA">
                                    PAYMAYA
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="radio" name="payment_method" value="PAYPAL">
                                    PAYPAL
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="radio" name="payment_method" value="GOTYME">
                                    GOTYME
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-1" type="radio" name="payment_method" value="BANK_TRANSFER">
                                    BANK TRANSFER
                                </label>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Submit Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="view_invoice.php?id=<?php echo htmlspecialchars($invoiceData['id']); ?>">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>