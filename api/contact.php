<?php
header('Content-Type: application/json');
require_once('../includes/db.php');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit();
}

// Sanitize inputs
$name = htmlspecialchars(strip_tags($name));
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(strip_tags($message));

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO contact_form_submissions (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        // Optionally, send an auto-reply email here
        // mail($email, "Thank you for your message", "We have received your message and will get back to you shortly.", "From: no-reply@yourdomain.com");

        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully.']);
    } else {
        throw new Exception('Failed to save message.');
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>