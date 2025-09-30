<?php
// PSR-4 Autoloader
spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'App\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/../src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Core/Router.php';

$router = new App\Core\Router();

// Define routes using fully qualified class names
$router->add('/admin/login', 'App\Controllers\AdminController', 'login');
$router->add('/admin/dashboard', 'App\Controllers\AdminController', 'dashboard');
$router->add('/admin/logout', 'App\Controllers\AdminController', 'logout');
$router->add('/admin/students', 'App\Controllers\StudentController', 'index');
$router->add('/admin/students/create', 'App\Controllers\StudentController', 'create');

// Routes for attendance
$router->add('/attendance/scan', 'App\Controllers\AttendanceController', 'scan');
$router->add('/attendance/mark', 'App\Controllers\AttendanceController', 'mark');

// Routes for teachers
$router->add('/teacher/login', 'App\Controllers\TeacherController', 'login');
$router->add('/teacher/dashboard', 'App\Controllers\TeacherController', 'dashboard');
$router->add('/teacher/logout', 'App\Controllers\TeacherController', 'logout');

// Get the current URI and dispatch the request
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$router->dispatch($uri);
?>