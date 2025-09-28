<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Mobile Top Bar -->
    <header class="top-bar">
        <button id="menu-toggle" class="menu-toggle">☰</button>
        <h1 class="portfolio-title-mobile"></h1>
        <button id="theme-toggle-mobile" class="theme-toggle">🌙</button>
    </header>

    <!-- Sidebar/Drawer Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="" alt="Profile" class="profile-pic">
            <h1 class="portfolio-title-desktop"></h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#about" class="nav-link">About Me</a></li>
                <li><a href="#education" class="nav-link">Education</a></li>
                <li><a href="#experience" class="nav-link">Experience</a></li>
                <li><a href="#skills" class="nav-link">Skills</a></li>
                <li><a href="#projects" class="nav-link">Projects</a></li>
                <li><a href="#testimonials" class="nav-link">Testimonials</a></li>
                <li><a href="#downloads" class="nav-link">Downloads</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <button id="theme-toggle-desktop" class="theme-toggle">🌙</button>
            <a href="admin/" class="admin-login-link">⚙️ Admin Login</a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content" id="main-content">
        <!-- Sections will be dynamically loaded here -->
        <section id="home" class="portfolio-section active"></section>
        <section id="about" class="portfolio-section"></section>
        <section id="education" class="portfolio-section"></section>
        <section id="experience" class="portfolio-section"></section>
        <section id="skills" class="portfolio-section"></section>
        <section id="projects" class="portfolio-section"></section>
        <section id="testimonials" class="portfolio-section"></section>
        <section id="downloads" class="portfolio-section"></section>
        <section id="contact" class="portfolio-section"></section>
    </main>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>