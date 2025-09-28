<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// Define upload path for downloads
define('DOWNLOAD_UPLOAD_PATH', '../downloads/');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if (isset($_POST['add'])) {
        // Simplified: Assumes file is already uploaded to a directory
        $stmt = $conn->prepare("INSERT INTO downloads (file_name, file_path, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['file_name'], $_POST['file_path'], $password);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE downloads SET file_name=?, file_path=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $_POST['file_name'], $_POST['file_path'], $password, $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM downloads WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=downloads");
    exit();
}

$downloads = $conn->query("SELECT * FROM downloads ORDER BY id DESC");
?>

<div class="management-form">
    <h3>Manage Downloads</h3>
    <p>Note: This form manages database records. Upload files to the 'downloads' directory manually.</p>
    <form action="dashboard.php?page=downloads" method="POST">
        <input type="hidden" name="id" id="download_id">
        <div class="form-group">
            <label for="download_file_name">File Name (for display)</label>
            <input type="text" id="download_file_name" name="file_name" required>
        </div>
        <div class="form-group">
            <label for="download_file_path">File Path (relative to root)</label>
            <input type="text" id="download_file_path" name="file_path" placeholder="e.g., downloads/resume.pdf" required>
        </div>
        <div class="form-group">
            <label for="download_password">Password (optional)</label>
            <input type="password" id="download_password" name="password" placeholder="Leave blank for no password">
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Downloads</h4>
    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Path</th>
                <th>Download Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $downloads->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                <td><?php echo htmlspecialchars($row['file_path']); ?></td>
                <td><?php echo $row['download_count']; ?></td>
                <td>
                    <button onclick="editDownload(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=downloads" method="POST" style="display:inline;">
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
function editDownload(data) {
    document.getElementById('download_id').value = data.id;
    document.getElementById('download_file_name').value = data.file_name;
    document.getElementById('download_file_path').value = data.file_path;
    // Password field is not pre-filled for security
}
</script>