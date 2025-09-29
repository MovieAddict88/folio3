<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?page=students');
    exit;
}

$student_id = $_GET['id'];
$dbh = db_connect();
$stmt = $dbh->prepare("SELECT name, qr_code_id FROM students WHERE id = :id");
$stmt->bindParam(':id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: index.php?page=students');
    exit;
}

$qr_code_url = 'public/qrcodes/' . $student['qr_code_id'] . '.png';
?>

<h1 class="mb-4">QR Code for <?php echo htmlspecialchars($student['name']); ?></h1>

<div class="card">
    <div class="card-body text-center">
        <?php if (file_exists($qr_code_url)): ?>
            <img src="<?php echo $qr_code_url; ?>" alt="QR Code for <?php echo htmlspecialchars($student['name']); ?>" class="img-fluid mb-4">
            <br>
            <button class="btn btn-primary" onclick="window.print();">Print QR Code</button>
        <?php else: ?>
            <div class="alert alert-danger">QR Code image not found.</div>
        <?php endif; ?>
        <a href="index.php?page=students" class="btn btn-secondary">Back to Students</a>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .card, .card * {
        visibility: visible;
    }
    .card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
    }
    .btn {
        display: none;
    }
}
</style>