<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?page=teachers');
    exit;
}

$teacher_id = $_GET['id'];
$dbh = db_connect();

// Handle form submission for updating teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $dbh->prepare("UPDATE teachers SET name = :name, email = :email WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $teacher_id);
    $stmt->execute();

    header('Location: index.php?page=teachers');
    exit;
}

// Fetch existing teacher data to pre-fill the form
$stmt = $dbh->prepare("SELECT * FROM teachers WHERE id = :id");
$stmt->bindParam(':id', $teacher_id);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    header('Location: index.php?page=teachers');
    exit;
}
?>

<h1 class="mb-4">Edit Teacher</h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?page=edit_teacher&id=<?php echo $teacher_id; ?>" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Teacher</button>
            <a href="index.php?page=teachers" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>