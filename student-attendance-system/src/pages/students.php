<?php
$dbh = db_connect();
$stmt = $dbh->prepare("SELECT * FROM students ORDER BY created_at DESC");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Manage Students</h1>

<div class="card">
    <div class="card-header">
        <a href="index.php?page=add_student" class="btn btn-success">Add New Student</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll Number</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td>
                                    <a href="index.php?page=view_qr&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">View QR</a>
                                    <a href="index.php?page=edit_student&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="index.php?page=delete_student&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>