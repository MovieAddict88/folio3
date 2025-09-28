<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO skills (type, name, level) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $_POST['type'], $_POST['name'], $_POST['level']);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE skills SET type=?, name=?, level=? WHERE id=?");
        $stmt->bind_param("ssii", $_POST['type'], $_POST['name'], $_POST['level'], $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM skills WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=skills");
    exit();
}

$skills = $conn->query("SELECT * FROM skills ORDER BY type, name");
?>

<div class="management-form">
    <h3>Manage Skills</h3>
    <form action="dashboard.php?page=skills" method="POST">
        <input type="hidden" name="id" id="skill_id">
        <div class="form-group">
            <label for="skill_type">Type</label>
            <select id="skill_type" name="type" required>
                <option value="soft">Soft Skill</option>
                <option value="hard">Hard Skill</option>
            </select>
        </div>
        <div class="form-group">
            <label for="skill_name">Skill Name</label>
            <input type="text" id="skill_name" name="name" required>
        </div>
        <div class="form-group">
            <label for="skill_level">Level (1-100)</label>
            <input type="number" id="skill_level" name="level" min="1" max="100">
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Skills</h4>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $skills->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['level']); ?>%</td>
                <td>
                    <button onclick="editSkill(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=skills" method="POST" style="display:inline;">
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
function editSkill(data) {
    document.getElementById('skill_id').value = data.id;
    document.getElementById('skill_type').value = data.type;
    document.getElementById('skill_name').value = data.name;
    document.getElementById('skill_level').value = data.level;
}
</script>