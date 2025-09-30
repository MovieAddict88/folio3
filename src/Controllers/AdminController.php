<?php

namespace App\Controllers;

use App\Core\Database;

class AdminController {
    public function login() {
        // In a real application, you would have a more robust session check.
        session_start();
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: /admin/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                header('Location: /admin/dashboard');
                exit;
            } else {
                $error = 'Invalid credentials';
            }
        }

        // Load the login view
        require_once __DIR__ . '/../../views/admin/login.php';
    }

    public function dashboard() {
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /admin/login');
            exit;
        }

        // Load the dashboard view
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}
?>