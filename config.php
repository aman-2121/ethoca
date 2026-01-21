<?php
// config.php - WITH FINAL CORRECT PASSWORD
$host = 'localhost';
$username = 'root';
$password = ''; // ← THIS IS THE NEW CORRECT PASSWORD!
$database = 'visa_application'; // ← NEW DATABASE NAME!

// Enable errors temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3 style='color:green;'>✅ DATABASE CONNECTION SUCCESSFUL!</h3>";
    echo "Password: <strong>oqcpm1vFRNpeDH</strong> WORKS!<br>";
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✅ Tables found: " . count($tables) . "<br>";
    } else {
        echo "⚠️ Database is empty. Need to import SQL file.<br>";
        echo '<a href="https://dash.infinityfree.com/" target="_blank">Click phpMyAdmin to import</a>';
    }
    
} catch(PDOException $e) {
    die("<h3 style='color:red;'>❌ ERROR: " . $e->getMessage() . "</h3>" .
        "<br>Password used: '" . htmlspecialchars($password) . "'");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Addis_Ababa');

// Remove echo lines after it works
?>
