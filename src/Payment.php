<?php

class Payment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($invoiceId, $amount, $paymentMethod, $transactionId = null) {
        $sql = "INSERT INTO payments (invoice_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$invoiceId, $amount, $paymentMethod, $transactionId]);
    }

    public function findByInvoiceId($invoiceId) {
        $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$invoiceId]);
        return $stmt->fetch();
    }
}