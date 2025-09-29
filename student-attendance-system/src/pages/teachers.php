<?php
$dbh = db_connect();
$stmt = $dbh->prepare("SELECT * FROM teachers ORDER BY created_at DESC");
$stmt->execute();
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Manage Teachers</h1>

<div class="card">
    <div class="card-header">
        <a href="index.php?page=add_teacher" class="btn btn-success">Add New Teacher</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($teachers) > 0): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                <td>
                                    <a href="index.php?page=edit_teacher&id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="index.php?page=delete_teacher&id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this teacher? This will also delete their login account.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No teachers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>