<?php

namespace App\Controllers;

use App\Core\Database;

class AttendanceController {
    public function scan() {
        require_once __DIR__ . '/../../views/attendance/scan.php';
    }

    public function mark() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student_id = $_POST['student_id'];

            // In a real application, you would also need to know the teacher.
            // For now, we'll hardcode a teacher_id for simplicity.
            $teacher_id = 1;

            $db = Database::getInstance()->getConnection();

            // Check if the student exists
            $stmt = $db->prepare("SELECT id FROM students WHERE student_id = :student_id");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $student = $stmt->fetch();

            if ($student) {
                // Mark attendance
                $stmt = $db->prepare("INSERT INTO attendance (student_id, teacher_id, status, attendance_date) VALUES (:student_id, :teacher_id, 'present', CURDATE())");
                $stmt->bindParam(':student_id', $student['id']);
                $stmt->bindParam(':teacher_id', $teacher_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Attendance marked successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to mark attendance.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
            }
        }
    }
}
?>