<?php
// --- Mock External Payment Gateway ---

// Retrieve payment details from the query string
$invoiceId = $_GET['invoice_id'] ?? null;
$amount = $_GET['amount'] ?? null;
$paymentMethod = $_GET['payment_method'] ?? 'Unknown';
$callbackUrl = $_GET['callback_url'] ?? null;

// Basic validation
if (!$invoiceId || !$amount || !$callbackUrl) {
    // In a real gateway, this would be a branded error page.
    die("<h1>Error: Invalid Payment Request</h1><p>Missing required payment details.</p>");
}

// Simulate a successful transaction ID
$transactionId = 'EXT_TXN_' . strtoupper(uniqid());

// Prepare the callback parameters
$callbackParams = [
    'invoice_id' => $invoiceId,
    'transaction_id' => $transactionId,
    'status' => 'success', // Simulate a successful payment
    'payment_method' => $paymentMethod,
    'amount' => $amount
    // In a real scenario, the gateway would also return a hash to be verified by the application
    // 'hash' => hash_hmac('sha256', $invoiceId . $transactionId . 'success', 'YOUR_SECRET_KEY')
];

// Construct the final callback URL
$finalCallbackUrl = $callbackUrl . '?' . http_build_query($callbackParams);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External Payment Gateway Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .gateway-card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .gateway-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card gateway-card">
                    <div class="card-header gateway-header">
                        <h3 class="text-center mb-0">Pay with <?php echo htmlspecialchars($paymentMethod); ?></h3>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title text-center">Confirm Your Payment</h5>
                        <p class="text-center text-muted">You are paying for Invoice #<?php echo htmlspecialchars($invoiceId); ?></p>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class="mb-0"><strong>Amount:</strong></p>
                            <p class="mb-0 fs-5"><strong>$<?php echo htmlspecialchars(number_format($amount, 2)); ?></strong></p>
                        </div>
                        <div class="alert alert-warning mt-4">
                            This is a simulation of an external payment page. Click the button below to confirm the payment and be redirected back to the merchant website.
                        </div>
                        <div class="d-grid mt-4">
                            <a href="<?php echo htmlspecialchars($finalCallbackUrl); ?>" class="btn btn-success btn-lg">Confirm and Complete Payment</a>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-transparent">
                        <a href="<?php echo htmlspecialchars($callbackUrl . '?status=cancelled&invoice_id=' . $invoiceId); ?>">Cancel Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>