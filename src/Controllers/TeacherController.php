<?php

namespace App\Controllers;

use App\Models\Teacher;

class TeacherController {
    public function login() {
        session_start();
        if (isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true) {
            header('Location: /teacher/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $teacherModel = new Teacher();
            $teacher = $teacherModel->findByEmail($email);

            if ($teacher && password_verify($password, $teacher['password'])) {
                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_id'] = $teacher['id'];
                $_SESSION['teacher_name'] = $teacher['name'];
                header('Location: /teacher/dashboard');
                exit;
            } else {
                $error = 'Invalid credentials';
            }
        }

        require_once __DIR__ . '/../../views/teacher/login.php';
    }

    public function dashboard() {
        session_start();
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            header('Location: /teacher/login');
            exit;
        }

        $teacherModel = new Teacher();
        $attendance = $teacherModel->getAttendanceByTeacherId($_SESSION['teacher_id']);

        require_once __DIR__ . '/../../views/teacher/dashboard.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /teacher/login');
        exit;
    }
}
?>