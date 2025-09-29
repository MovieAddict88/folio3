<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?page=teachers');
    exit;
}

$teacher_id = $_GET['id'];
$dbh = db_connect();

// First, get the user_id associated with this teacher
$stmt = $dbh->prepare("SELECT user_id FROM teachers WHERE id = :id");
$stmt->bindParam(':id', $teacher_id);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if ($teacher) {
    $user_id = $teacher['user_id'];

    // Delete the user from the users table.
    // The ON DELETE CASCADE constraint will automatically delete the corresponding teacher record.
    $stmt = $dbh->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

header('Location: index.php?page=teachers');
exit;
?>