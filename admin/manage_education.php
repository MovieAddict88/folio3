<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO education (year, degree, institution, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_POST['year'], $_POST['degree'], $_POST['institution'], $_POST['description']);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE education SET year=?, degree=?, institution=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $_POST['year'], $_POST['degree'], $_POST['institution'], $_POST['description'], $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM education WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=education");
    exit();
}

$educations = $conn->query("SELECT * FROM education ORDER BY year DESC");
?>

<div class="management-form">
    <h3>Manage Education</h3>
    <form action="dashboard.php?page=education" method="POST">
        <input type="hidden" name="id" id="edu_id">
        <div class="form-group">
            <label for="edu_year">Year</label>
            <input type="text" id="edu_year" name="year" required>
        </div>
        <div class="form-group">
            <label for="edu_degree">Degree</label>
            <input type="text" id="edu_degree" name="degree" required>
        </div>
        <div class="form-group">
            <label for="edu_institution">Institution</label>
            <input type="text" id="edu_institution" name="institution" required>
        </div>
        <div class="form-group">
            <label for="edu_description">Description</label>
            <textarea id="edu_description" name="description"></textarea>
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Entries</h4>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Degree</th>
                <th>Institution</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $educations->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['degree']); ?></td>
                <td><?php echo htmlspecialchars($row['institution']); ?></td>
                <td>
                    <button onclick="editEdu(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=education" method="POST" style="display:inline;">
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
function editEdu(data) {
    document.getElementById('edu_id').value = data.id;
    document.getElementById('edu_year').value = data.year;
    document.getElementById('edu_degree').value = data.degree;
    document.getElementById('edu_institution').value = data.institution;
    document.getElementById('edu_description').value = data.description;
}
</script>