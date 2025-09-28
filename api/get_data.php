<?php
header('Content-Type: application/json');
require_once('../includes/db.php');

$response = [];

// Fetch all data from each table
$tables = ['settings', 'about', 'education', 'experience', 'skills', 'projects', 'testimonials'];

foreach ($tables as $table) {
    // For settings and about, we fetch the single row
    if ($table === 'settings' || $table === 'about') {
        $result = $conn->query("SELECT * FROM $table LIMIT 1");
        $response[$table] = $result->fetch_assoc();
    } else {
        // For other tables, fetch all entries
        $result = $conn->query("SELECT * FROM $table");
        $response[$table] = [];
        while ($row = $result->fetch_assoc()) {
            $response[$table][] = $row;
        }
    }
}

// Close the connection
$conn->close();

// Return the data as JSON
echo json_encode($response, JSON_PRETTY_PRINT);
?>