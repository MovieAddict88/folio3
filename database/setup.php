<?php
// Include the database configuration
require_once __DIR__ . '/../config/database.php';

try {
    // Connect to MySQL server
    $dbh = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $dbh->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`;
                USE `" . DB_NAME . "`;");

    echo "Database created successfully (if it didn't exist).<br>";

    // SQL to create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS `admins` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(255) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `teachers` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(255) NOT NULL,
      `email` VARCHAR(255) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `students` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(255) NOT NULL,
      `student_id` VARCHAR(255) NOT NULL UNIQUE,
      `qr_code` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS `attendance` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `student_id` INT NOT NULL,
      `teacher_id` INT NOT NULL,
      `status` ENUM('present', 'absent') NOT NULL,
      `attendance_date` DATE NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (student_id) REFERENCES students(id),
      FOREIGN KEY (teacher_id) REFERENCES teachers(id)
    );
    ";

    // Execute the SQL
    $dbh->exec($sql);

    echo "Tables created successfully.<br>";

    // Insert a default admin user if one doesn't exist
    $stmt = $dbh->prepare("SELECT id FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $admin_pass = password_hash('password', PASSWORD_DEFAULT); // In a real app, use a secure password
        $dbh->exec("INSERT INTO admins (username, password) VALUES ('admin', '{$admin_pass}')");
        echo "Default admin user created.<br>";
    }

    // Insert a default teacher user if one doesn't exist
    $stmt = $dbh->prepare("SELECT id FROM teachers WHERE email = 'teacher@example.com'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $teacher_pass = password_hash('password', PASSWORD_DEFAULT); // Hash the password
        $dbh->exec("INSERT INTO teachers (name, email, password) VALUES ('John Doe', 'teacher@example.com', '{$teacher_pass}')");
        echo "Default teacher user created.<br>";
    }


} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}
?>