-- Database schema for Ethio-Canada Visa Application System
-- Run this SQL to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS visa_application;
USE visa_application;

-- Create applications table
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    father_name VARCHAR(255) NOT NULL,
    mother_name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    date_of_birth DATE NOT NULL,
    age INT NOT NULL,
    marital_status ENUM('Single', 'Married', 'Divorced', 'Widowed') NOT NULL,
    relationship_status TEXT,
    phone_number VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    postal_zip VARCHAR(20),
    national_id_front VARCHAR(255) NOT NULL,
    national_id_back VARCHAR(255) NOT NULL,
    selfie VARCHAR(255),
    declaration_accepted TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('Pending', 'Under Review', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    application_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin_users table for authentication
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@ethiocanada.com')
ON DUPLICATE KEY UPDATE username=username;

-- Create indexes for better performance
CREATE INDEX idx_status ON applications(status);
CREATE INDEX idx_application_date ON applications(application_date);
CREATE INDEX idx_full_name ON applications(full_name);
