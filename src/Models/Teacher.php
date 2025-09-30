<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Teacher {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM teachers WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAttendanceByTeacherId($teacher_id) {
        $stmt = $this->conn->prepare("
            SELECT a.attendance_date, s.name as student_name, a.status
            FROM attendance a
            JOIN students s ON a.student_id = s.id
            WHERE a.teacher_id = :teacher_id
            ORDER BY a.attendance_date DESC
        ");
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>