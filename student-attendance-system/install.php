<?php
if (isset($_POST['install'])) {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);

    // Create necessary directories if they don't exist
    if (!is_dir('config')) {
        mkdir('config', 0775, true);
    }
    if (!is_dir('public/qrcodes')) {
        mkdir('public/qrcodes', 0775, true);
    }

    // Create config file
    $config_content = "<?php
define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
";
    if (file_put_contents('config/config.php', $config_content) === false) {
        die('Error creating config file. Please check folder permissions.');
    }

    // Create database connection
    try {
        $dbh = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db_name`;");
        $dbh->exec("USE `$db_name`;");
    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }

    // Create tables
    try {
        $sql = file_get_contents('database.sql');
        $dbh->exec($sql);
    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }

    // Insert admin user
    try {
        $stmt = $dbh->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'admin')");
        $stmt->bindParam(':username', $admin_user);
        $stmt->bindParam(':password', $admin_pass);
        $stmt->execute();
    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }


    echo '<div class="alert alert-success">Installation complete. Please delete install.php</div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance System - Installation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Student Attendance System Installation</h3>
                    </div>
                    <div class="card-body">
                        <form action="install.php" method="post">
                            <div class="form-group">
                                <label for="db_host">Database Host</label>
                                <input type="text" name="db_host" id="db_host" class="form-control" value="localhost" required>
                            </div>
                            <div class="form-group">
                                <label for="db_name">Database Name</label>
                                <input type="text" name="db_name" id="db_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="db_user">Database User</label>
                                <input type="text" name="db_user" id="db_user" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="db_pass">Database Password</label>
                                <input type="password" name="db_pass" id="db_pass" class="form-control">
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="admin_user">Admin Username</label>
                                <input type="text" name="admin_user" id="admin_user" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="admin_pass">Admin Password</label>
                                <input type="password" name="admin_pass" id="admin_pass" class="form-control" required>
                            </div>
                            <button type="submit" name="install" class="btn btn-primary btn-block">Install</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>