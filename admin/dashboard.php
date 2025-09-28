<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
require_once('../includes/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h3>Dashboard</h3>
            <ul>
                <li><a href="dashboard.php?page=settings">Site Settings</a></li>
                <li><a href="dashboard.php?page=about">About Me</a></li>
                <li><a href="dashboard.php?page=education">Education</a></li>
                <li><a href="dashboard.php?page=experience">Experience</a></li>
                <li><a href="dashboard.php?page=skills">Skills</a></li>
                <li><a href="dashboard.php?page=projects">Projects</a></li>
                <li><a href="dashboard.php?page=testimonials">Testimonials</a></li>
                <li><a href="dashboard.php?page=downloads">Downloads</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>Select a section from the sidebar to manage your portfolio content.</p>

            <?php
            // Simple router to include page content
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                $page_path = "manage_" . $page . ".php";
                if (file_exists($page_path)) {
                    include($page_path);
                } else {
                    echo "<p>Page not found.</p>";
                }
            }
            ?>
        </main>
    </div>
</body>
</html>