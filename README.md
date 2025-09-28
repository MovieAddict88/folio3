# Portfolio Website

This project is a dynamic, database-driven portfolio website built with PHP and MySQL. It features a responsive design that adapts for both desktop and mobile viewing, a secure admin panel for content management, and a clean API for fetching data.

## Features

- **Responsive UI**: Desktop view with a fixed sidebar and a mobile view with a slide-in drawer menu.
- **Dynamic Content**: All portfolio sections (About Me, Education, Experience, Skills, etc.) are dynamically loaded from a MySQL database via a PHP API.
- **Admin Panel**: A secure, password-protected admin area to perform CRUD (Create, Read, Update, Delete) operations on all content.
- **Secure by Design**:
  - **SQL Injection Prevention**: Uses prepared statements for all database queries.
  - **XSS Prevention**: Implements proper output escaping using `htmlspecialchars`.
  - **Secure Authentication**: Features password hashing and verification.
- **Modern Frontend**: Built with vanilla JavaScript, using async/await for API calls and dynamic HTML rendering.
- **API Endpoints**:
  - `api/get_data.php`: Fetches all portfolio content in a single JSON response.
  - `api/contact.php`: Securely handles contact form submissions.

## Project Structure

```
/
|-- admin/
|   |-- index.php             # Admin login page
|   |-- dashboard.php         # Main admin dashboard
|   |-- manage_*.php          # CRUD management pages for each section
|   |-- login_handler.php     # Handles admin authentication
|   `-- logout.php
|-- api/
|   |-- get_data.php          # API to fetch all portfolio data
|   `-- contact.php           # API to handle contact form submissions
|-- assets/
|   |-- css/
|   |   |-- style.css         # Main stylesheet for the portfolio
|   |   `-- admin_style.css   # Stylesheet for the admin panel
|   |-- js/
|   |   `-- main.js           # Core JavaScript for the frontend
|   `-- images/               # Directory for images
|-- includes/
|   `-- db.template.php       # Template for the database connection handler
|-- .gitignore                # Specifies intentionally untracked files
|-- index.php                 # Main entry point of the portfolio
`-- schema.sql                # SQL script to set up the database schema
```

## How to Set Up

1.  **Database**:
    - Create a MySQL database (e.g., `portfolio_db`).
    - Import the `schema.sql` file to create all the necessary tables.
2.  **Configuration**:
    - Create a new file `includes/db.php` by copying `includes/db.template.php`.
    - Update the database credentials (`$servername`, `$username`, `$password`, `$dbname`) in your new `includes/db.php` file. This file is ignored by Git to keep your credentials private.
3.  **Admin User**:
    - Manually insert an admin user into the `users` table. Be sure to use a strong, hashed password (e.g., using `password_hash()` in a separate PHP script).
    ```sql
    INSERT INTO users (username, password, email) VALUES ('admin', 'YOUR_HASHED_PASSWORD', 'admin@example.com');
    ```
4.  **Content**:
    - Log in to the admin panel at `/admin` and start adding your portfolio content.

## Technologies Used

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Security**: Prepared Statements, Password Hashing, Output Escaping