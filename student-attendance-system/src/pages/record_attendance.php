<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['qr_code_id'])) {
    echo json_encode(['success' => false, 'message' => 'QR Code ID not provided.']);
    exit;
}

$qr_code_id = $data['qr_code_id'];
$dbh = db_connect();

// Find the student by QR code ID
$stmt = $dbh->prepare("SELECT id, name FROM students WHERE qr_code_id = :qr_code_id");
$stmt->bindParam(':qr_code_id', $qr_code_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
    exit;
}

$student_id = $student['id'];
$student_name = $student['name'];
$attendance_date = date('Y-m-d');

// Check if attendance is already recorded for today
$stmt = $dbh->prepare("SELECT id FROM attendance WHERE student_id = :student_id AND attendance_date = :attendance_date");
$stmt->bindParam(':student_id', $student_id);
$stmt->bindParam(':attendance_date', $attendance_date);
$stmt->execute();

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => "Attendance for $student_name has already been recorded today."]);
    exit;
}

// Determine the teacher_id based on the logged-in user
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$teacher_id = null;

if ($user_role === 'teacher') {
    $stmt = $dbh->prepare("SELECT id FROM teachers WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($teacher) {
        $teacher_id = $teacher['id'];
    }
} elseif ($user_role === 'admin') {
    // If admin, default to the first teacher in the system
    $stmt = $dbh->prepare("SELECT id FROM teachers ORDER BY id ASC LIMIT 1");
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($teacher) {
        $teacher_id = $teacher['id'];
    }
}

if ($teacher_id === null) {
    echo json_encode(['success' => false, 'message' => 'No available teacher to record attendance. Please add a teacher first.']);
    exit;
}

// Record the attendance
$status = 'present';

$stmt = $dbh->prepare("INSERT INTO attendance (student_id, teacher_id, status, attendance_date) VALUES (:student_id, :teacher_id, :status, :attendance_date)");
$stmt->bindParam(':student_id', $student_id);
$stmt->bindParam(':teacher_id', $teacher_id);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':attendance_date', $attendance_date);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => "Attendance for $student_name recorded successfully."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to record attendance.']);
}
?>