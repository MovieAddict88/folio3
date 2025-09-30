# Manual Testing Guide

This document provides a step-by-step guide for manually testing the Student Attendance System.

## 1. Environment Setup

1.  **Install a local server environment:** You will need a local server stack like XAMPP, WAMP, or MAMP that includes Apache and MySQL.
2.  **Start Apache and MySQL:** Ensure both services are running from your server's control panel.
3.  **Place the project files:** Copy the entire project folder into your server's web directory (e.g., `htdocs` for XAMPP).
4.  **Create the database:** Open phpMyAdmin and create a new, empty database named `attendance_system`.

## 2. Database and Initial Data Setup

1.  **Run the setup script:** In your web browser, navigate to `http://localhost/your_project_folder/database/setup.php`.
2.  **Verify the output:** You should see messages indicating that the database and tables were created successfully, along with the creation of a default admin and teacher user.
3.  **Check the database:** In phpMyAdmin, confirm that the `admins`, `teachers`, `students`, and `attendance` tables have been created in the `attendance_system` database. You should also see one record in the `admins` table and one in the `teachers` table.

## 3. Admin Panel Functionality

1.  **Navigate to the admin login:** Go to `http://localhost/your_project_folder/public/admin/login`.
2.  **Log in:** Use the credentials `admin` for the username and `password` for the password.
3.  **Verify dashboard access:** You should be redirected to the admin dashboard.
4.  **Manage students:**
    *   Click on the "Manage Students" link.
    *   Click the "Add Student" button.
    *   Enter a name (e.g., "Jane Smith") and a student ID (e.g., "S12345").
    *   Click "Add Student".
    *   You should be redirected back to the student list, and the new student should appear in the table with a generated QR code image.
5.  **Log out:** Click the "Logout" link in the navigation bar. You should be returned to the admin login page.

## 4. Student Attendance Scanning

1.  **Navigate to the scanning page:** Go to `http://localhost/your_project_folder/public/attendance/scan`.
2.  **Allow camera access:** Your browser will likely prompt you for permission to use your camera. Allow it.
3.  **Scan the QR code:** Use your camera to scan the QR code of the student you created in the previous step (from the "Manage Students" page).
4.  **Verify attendance:** A success message should appear on the screen, confirming that the attendance has been marked.

## 5. Teacher Dashboard Verification

1.  **Navigate to the teacher login:** Go to `http://localhost/your_project_folder/public/teacher/login`.
2.  **Log in:** Use the credentials `teacher@example.com` for the email and `password` for the password.
3.  **Verify dashboard access:** You should be redirected to the teacher's dashboard.
4.  **Check attendance records:** The table should now contain an attendance record for the student whose QR code you just scanned.
5.  **Log out:** Click the "Logout" link. You should be returned to the teacher login page.

If all these steps are completed successfully, the core functionalities of the application are working correctly.