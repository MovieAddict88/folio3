-- Database Schema for Portfolio Website

-- Main settings for the portfolio
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_title VARCHAR(255) NOT NULL,
    tagline VARCHAR(255),
    profile_pic VARCHAR(255),
    background_image VARCHAR(255),
    cv_url VARCHAR(255),
    dark_mode BOOLEAN DEFAULT 0,
    -- Social Links
    email VARCHAR(255),
    facebook_url VARCHAR(255),
    tiktok_url VARCHAR(255),
    youtube_url VARCHAR(255),
    instagram_url VARCHAR(255)
);

-- About Me Section
CREATE TABLE about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bio TEXT,
    philosophy TEXT,
    video_embed_url VARCHAR(255)
);

-- Education Section
CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(50) NOT NULL,
    degree VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    description TEXT
);

-- Experience Section
CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year_range VARCHAR(50) NOT NULL,
    position VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    description TEXT
);

-- Skills Section
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('soft', 'hard') NOT NULL,
    name VARCHAR(100) NOT NULL,
    level INT -- e.g., percentage for a bar
);

-- Projects Section
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    image_urls JSON, -- Store multiple image paths as a JSON array
    external_link VARCHAR(255)
);

-- Testimonials Section
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote TEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    author_title VARCHAR(255),
    video_url VARCHAR(255)
);

-- Downloads Section
CREATE TABLE downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    password VARCHAR(255), -- Hashed password
    download_count INT DEFAULT 0,
    expiry_date DATETIME
);

-- Contact Form Submissions
CREATE TABLE contact_form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hashed password
    email VARCHAR(255) NOT NULL UNIQUE,
    two_factor_secret VARCHAR(255)
);