<?php
// Database configuration settings
define('DB_HOST', 'YOUR_DATABASE_HOST'); // e.g., sqlXXX.infinityfree.com
define('DB_USER', 'YOUR_DATABASE_USERNAME');
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');
define('DB_NAME', 'YOUR_DATABASE_NAME');

/**
 * Creates a new database connection using PDO.
 * @return PDO|null
 */
function getDBConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // In a real application, you would log this error and show a generic message
        die('Database connection failed: ' . $e->getMessage());
    }
}
?>