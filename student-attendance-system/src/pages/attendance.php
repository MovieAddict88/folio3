<?php
$dbh = db_connect();

$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT
            attendance.id,
            students.name as student_name,
            teachers.name as teacher_name,
            attendance.status,
            attendance.attendance_date
        FROM attendance
        JOIN students ON attendance.student_id = students.id
        JOIN teachers ON attendance.teacher_id = teachers.id";

if ($filter_date) {
    $sql .= " WHERE attendance.attendance_date = :filter_date";
}

$sql .= " ORDER BY attendance.attendance_date DESC, students.name ASC";

$stmt = $dbh->prepare($sql);

if ($filter_date) {
    $stmt->bindParam(':filter_date', $filter_date);
}

$stmt->execute();
$attendance_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Attendance Records</h1>

<div class="card">
    <div class="card-header">
        <form action="index.php" method="get" class="form-inline">
            <input type="hidden" name="page" value="attendance">
            <div class="form-group mr-2">
                <label for="date" class="mr-2">Filter by Date:</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="index.php?page=attendance" class="btn btn-secondary ml-2">Clear</a>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Recorded by</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($attendance_records) > 0): ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['teacher_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $record['status'] === 'present' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>