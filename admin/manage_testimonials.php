<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once('../includes/db.php');

// Handle Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO testimonials (quote, author, author_title, video_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_POST['quote'], $_POST['author'], $_POST['author_title'], $_POST['video_url']);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE testimonials SET quote=?, author=?, author_title=?, video_url=? WHERE id=?");
        $stmt->bind_param("ssssi", $_POST['quote'], $_POST['author'], $_POST['author_title'], $_POST['video_url'], $_POST['id']);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    }
    header("Location: dashboard.php?page=testimonials");
    exit();
}

$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
?>

<div class="management-form">
    <h3>Manage Testimonials</h3>
    <form action="dashboard.php?page=testimonials" method="POST">
        <input type="hidden" name="id" id="testimonial_id">
        <div class="form-group">
            <label for="testimonial_quote">Quote</label>
            <textarea id="testimonial_quote" name="quote" required></textarea>
        </div>
        <div class="form-group">
            <label for="testimonial_author">Author</label>
            <input type="text" id="testimonial_author" name="author" required>
        </div>
        <div class="form-group">
            <label for="testimonial_author_title">Author's Title</label>
            <input type="text" id="testimonial_author_title" name="author_title">
        </div>
        <div class="form-group">
            <label for="testimonial_video_url">Video URL (optional)</label>
            <input type="text" id="testimonial_video_url" name="video_url">
        </div>
        <button type="submit" name="add">Add New</button>
        <button type="submit" name="edit">Save Changes</button>
    </form>

    <hr>

    <h4>Existing Testimonials</h4>
    <table>
        <thead>
            <tr>
                <th>Author</th>
                <th>Quote</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $testimonials->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo substr(htmlspecialchars($row['quote']), 0, 50); ?>...</td>
                <td>
                    <button onclick="editTestimonial(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form action="dashboard.php?page=testimonials" method="POST" style="display:inline;">
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
function editTestimonial(data) {
    document.getElementById('testimonial_id').value = data.id;
    document.getElementById('testimonial_quote').value = data.quote;
    document.getElementById('testimonial_author').value = data.author;
    document.getElementById('testimonial_author_title').value = data.author_title;
    document.getElementById('testimonial_video_url').value = data.video_url;
}
</script>