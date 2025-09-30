<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use QRcode;

class Student {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($name, $student_id) {
        // Include the QR code library
        require_once __DIR__ . '/../lib/phpqrcode.php';

        // Generate a unique filename for the QR code
        $qr_code_file = 'qr_' . $student_id . '.png';
        $qr_code_dir = __DIR__ . '/../../public/images/qrcodes/';
        $qr_code_path = $qr_code_dir . $qr_code_file;

        // Create the directory if it doesn't exist
        if (!file_exists($qr_code_dir)) {
            mkdir($qr_code_dir, 0755, true);
        }

        // Generate the QR code and save it to the file
        QRcode::png($student_id, $qr_code_path);

        // Save the student to the database
        $stmt = $this->conn->prepare("INSERT INTO students (name, student_id, qr_code) VALUES (:name, :student_id, :qr_code)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':qr_code', $qr_code_file);

        return $stmt->execute();
    }

    public function findAll() {
        $stmt = $this->conn->prepare("SELECT * FROM students");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>