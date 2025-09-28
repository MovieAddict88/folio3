<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once('../includes/db.php');

// Handle form submission for updating about section
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'];
    $philosophy = $_POST['philosophy'];
    $video_embed_url = $_POST['video_embed_url'];

    // Check if about data exists
    $result = $conn->query("SELECT id FROM about LIMIT 1");
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE about SET bio = ?, philosophy = ?, video_embed_url = ? WHERE id = 1");
        $stmt->bind_param("sss", $bio, $philosophy, $video_embed_url);
    } else {
        $stmt = $conn->prepare("INSERT INTO about (bio, philosophy, video_embed_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $bio, $philosophy, $video_embed_url);
    }

    if ($stmt->execute()) {
        echo "<p class='success'>About section updated successfully!</p>";
    } else {
        echo "<p class='error'>Failed to update about section.</p>";
    }
    $stmt->close();
}

// Fetch current about data
$result = $conn->query("SELECT * FROM about WHERE id = 1");
$about = $result->fetch_assoc();
?>

<div class="management-form">
    <h3>Manage About Me</h3>
    <form action="dashboard.php?page=about" method="POST">
        <div class="form-group">
            <label for="bio">Biography</label>
            <textarea id="bio" name="bio" required><?php echo htmlspecialchars($about['bio'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="philosophy">Philosophy</label>
            <textarea id="philosophy" name="philosophy"><?php echo htmlspecialchars($about['philosophy'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="video_embed_url">Intro Video URL (Embed)</label>
            <input type="text" id="video_embed_url" name="video_embed_url" value="<?php echo htmlspecialchars($about['video_embed_url'] ?? ''); ?>">
        </div>
        <button type="submit">Save About Section</button>
    </form>
</div>