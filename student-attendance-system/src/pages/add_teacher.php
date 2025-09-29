<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'teacher';

    $dbh = db_connect();

    try {
        $dbh->beginTransaction();

        // 1. Create the user account
        $stmt = $dbh->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $user_id = $dbh->lastInsertId();

        // 2. Create the teacher record
        $stmt = $dbh->prepare("INSERT INTO teachers (name, email, user_id) VALUES (:name, :email, :user_id)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $dbh->commit();

        header('Location: index.php?page=teachers');
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<h1 class="mb-4">Add New Teacher</h1>

<div class="card">
    <div class="card-body">
        <form action="index.php?page=add_teacher" method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <hr>
            <h5 class="mb-3">Login Credentials</h5>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Teacher</button>
            <a href="index.php?page=teachers" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>