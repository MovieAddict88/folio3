<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?page=students');
    exit;
}

$student_id = $_GET['id'];
$dbh = db_connect();

// First, get the qr_code_id to delete the file
$stmt = $dbh->prepare("SELECT qr_code_id FROM students WHERE id = :id");
$stmt->bindParam(':id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    // Delete the QR code image file
    $qr_code_path = '../public/qrcodes/' . $student['qr_code_id'] . '.png';
    if (file_exists($qr_code_path)) {
        unlink($qr_code_path);
    }

    // Delete the student record from the database
    $stmt = $dbh->prepare("DELETE FROM students WHERE id = :id");
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();
}

header('Location: index.php?page=students');
exit;
?>