<?php
function db_connect() {
    try {
        $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
}