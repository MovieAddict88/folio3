<?php

namespace App\Controllers;

use App\Models\Student;

class StudentController {
    public function index() {
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /admin/login');
            exit;
        }

        $studentModel = new Student();
        $students = $studentModel->findAll();

        require_once __DIR__ . '/../../views/admin/students/index.php';
    }

    public function create() {
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /admin/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $student_id = $_POST['student_id'];

            $studentModel = new Student();
            if ($studentModel->create($name, $student_id)) {
                header('Location: /admin/students');
                exit;
            } else {
                $error = 'Failed to create student.';
            }
        }

        require_once __DIR__ . '/../../views/admin/students/create.php';
    }
}
?>