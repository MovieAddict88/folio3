<?php
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once('../includes/db.php');

// Handle form submission for updating settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $site_title = htmlspecialchars($_POST['site_title']);
    $tagline = htmlspecialchars($_POST['tagline']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    // ... handle other fields and file uploads for profile_pic, etc.

    // For simplicity, this example updates only a few fields.
    // In a real application, you would handle all fields from the settings table.
    $stmt = $conn->prepare("UPDATE settings SET site_title = ?, tagline = ?, email = ? WHERE id = 1");
    $stmt->bind_param("sss", $site_title, $tagline, $email);

    if ($stmt->execute()) {
        echo "<p class='success'>Settings updated successfully!</p>";
    } else {
        echo "<p class='error'>Failed to update settings.</p>";
    }
    $stmt->close();
}

// Fetch current settings
$result = $conn->query("SELECT * FROM settings WHERE id = 1");
$settings = $result->fetch_assoc();
?>

<div class="management-form">
    <h3>Manage Site Settings</h3>
    <form action="dashboard.php?page=settings" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="site_title">Site Title</label>
            <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="tagline">Tagline</label>
            <input type="text" id="tagline" name="tagline" value="<?php echo htmlspecialchars($settings['tagline'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="email">Contact Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
        </div>
        <!-- Add other fields for social media, profile picture upload etc. -->
        <button type="submit">Save Settings</button>
    </form>
</div>