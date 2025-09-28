<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO experience (year_range, position, institution, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_POST['year_range'], $_POST['position'], $_POST['institution'], $_POST['description']);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE experience SET year_range=?, position=?, institution=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $_POST['year_range'], $_POST['position'], $_POST['institution'], $_POST['description'], $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM experience WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=experience");
    exit();
}

$experiences = $conn->query("SELECT * FROM experience ORDER BY year_range DESC");
?>

<div class="management-form">
    <h3>Manage Experience</h3>
    <form action="dashboard.php?page=experience" method="POST">
        <input type="hidden" name="id" id="exp_id">
        <div class="form-group">
            <label for="exp_year_range">Year Range</label>
            <input type="text" id="exp_year_range" name="year_range" required>
        </div>
        <div class="form-group">
            <label for="exp_position">Position</label>
            <input type="text" id="exp_position" name="position" required>
        </div>
        <div class="form-group">
            <label for="exp_institution">Institution</label>
            <input type="text" id="exp_institution" name="institution" required>
        </div>
        <div class="form-group">
            <label for="exp_description">Description</label>
            <textarea id="exp_description" name="description"></textarea>
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Entries</h4>
    <table>
        <thead>
            <tr>
                <th>Year Range</th>
                <th>Position</th>
                <th>Institution</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $experiences->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['year_range']); ?></td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><?php echo htmlspecialchars($row['institution']); ?></td>
                <td>
                    <button onclick="editExp(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=experience" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editExp(data) {
    document.getElementById('exp_id').value = data.id;
    document.getElementById('exp_year_range').value = data.year_range;
    document.getElementById('exp_position').value = data.position;
    document.getElementById('exp_institution').value = data.institution;
    document.getElementById('exp_description').value = data.description;
}
</script>