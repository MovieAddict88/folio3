<?php
session_start();

// If config file doesn't exist, redirect to installer
if (!file_exists('../config/config.php')) {
    header('Location: ../install.php');
    exit;
}

require_once '../config/config.php';
require_once '../src/includes/database.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

include_once '../src/includes/header.php';

$page_path = '../src/pages/' . $page . '.php';

if (file_exists($page_path)) {
    include_once $page_path;
} else {
    include_once '../src/pages/404.php';
}

include_once '../src/includes/footer.php';