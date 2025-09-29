<?php
require_once '../vendor/autoload.php';
require_once '../src/includes/database.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $roll_number = $_POST['roll_number'];
    $class = $_POST['class'];
    $qr_code_id = uniqid(); // Generate a unique ID for the QR code

    // Generate QR Code
    $qr_code_path = '../public/qrcodes/' . $qr_code_id . '.png';
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($qr_code_id)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(10)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->build();

    $result->saveToFile($qr_code_path);

    // Save student to database
    $dbh = db_connect();
    $stmt = $dbh->prepare("INSERT INTO students (name, roll_number, class, qr_code_id) VALUES (:name, :roll_number, :class, :qr_code_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':roll_number', $roll_number);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':qr_code_id', $qr_code_id);
    $stmt->execute();

    header('Location: index.php?page=students');
    exit;
}
?>

<h1 class="mb-4">Add New Student</h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?page=add_student" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="roll_number">Roll Number</label>
                <input type="text" name="roll_number" id="roll_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="class">Class</label>
                <input type="text" name="class" id="class" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
            <a href="index.php?page=students" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>