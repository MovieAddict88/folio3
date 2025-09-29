<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?page=students');
    exit;
}

$student_id = $_GET['id'];
$dbh = db_connect();

// Handle form submission for updating student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $id_number = $_POST['id_number'];
    $class = $_POST['class'];

    $stmt = $dbh->prepare("UPDATE students SET name = :name, id_number = :id_number, class = :class WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id_number', $id_number);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':id', $student_id);
    $stmt->execute();

    header('Location: index.php?page=students');
    exit;
}

// Fetch existing student data to pre-fill the form
$stmt = $dbh->prepare("SELECT * FROM students WHERE id = :id");
$stmt->bindParam(':id', $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: index.php?page=students');
    exit;
}
?>

<h1 class="mb-4">Edit Student</h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?page=edit_student&id=<?php echo $student_id; ?>" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="id_number">ID Number</label>
                <input type="text" name="id_number" id="id_number" class="form-control" value="<?php echo htmlspecialchars($student['id_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="class">Class</label>
                <input type="text" name="class" id="class" class="form-control" value="<?php echo htmlspecialchars($student['class']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Student</button>
            <a href="index.php?page=students" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>