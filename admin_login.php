<?php
// admin_login.php - Simple admin authentication
session_start();

// Simple hardcoded admin credentials (change in production!)
$admin_username = 'admin';
$admin_password = 'admin123';

// Set timezone
date_default_timezone_set('Africa/Addis_Ababa');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $admin_username && $password === $admin_password) {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');

        // Redirect to admin panel
        header('Location: view_applications.php');
        exit;
    } else {
        // Login failed
        header('Location: admin_login.html?error=1');
        exit;
    }
} else {
    // Direct access, redirect to login
    header('Location: admin_login.html');
    exit;
}
?>
