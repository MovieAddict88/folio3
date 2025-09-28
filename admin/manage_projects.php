<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// File upload path
define('UPLOAD_PATH', '../assets/images/projects/');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simplified image handling: assumes a comma-separated string of URLs or filenames
    // A real implementation would handle multi-file uploads more robustly
    $image_urls = json_encode(explode(',', $_POST['image_urls']));

    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, category, image_urls, external_link) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $_POST['title'], $_POST['description'], $_POST['category'], $image_urls, $_POST['external_link']);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, category=?, image_urls=?, external_link=? WHERE id=?");
        $stmt->bind_param("sssssi", $_POST['title'], $_POST['description'], $_POST['category'], $image_urls, $_POST['external_link'], $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM projects WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=projects");
    exit();
}

$projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");
?>

<div class="management-form">
    <h3>Manage Projects</h3>
    <form action="dashboard.php?page=projects" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="project_id">
        <div class="form-group">
            <label for="project_title">Title</label>
            <input type="text" id="project_title" name="title" required>
        </div>
        <div class="form-group">
            <label for="project_description">Description</label>
            <textarea id="project_description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="project_category">Category</label>
            <input type="text" id="project_category" name="category">
        </div>
        <div class="form-group">
            <label for="project_image_urls">Image Filenames (comma-separated)</label>
            <input type="text" id="project_image_urls" name="image_urls" placeholder="e.g., img1.jpg,img2.png">
        </div>
        <div class="form-group">
            <label for="project_external_link">External Link</label>
            <input type="text" id="project_external_link" name="external_link">
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Projects</h4>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $projects->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>
                    <button onclick="editProject(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=projects" method="POST" style="display:inline;">
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
function editProject(data) {
    document.getElementById('project_id').value = data.id;
    document.getElementById('project_title').value = data.title;
    document.getElementById('project_description').value = data.description;
    document.getElementById('project_category').value = data.category;
    // Decode JSON and join back to a string for the input field
    const images = JSON.parse(data.image_urls || '[]');
    document.getElementById('project_image_urls').value = images.join(',');
    document.getElementById('project_external_link').value = data.external_link;
}
</script>