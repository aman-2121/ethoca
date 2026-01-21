<?php
// config.php - Database Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'visa_application';

// Enable errors temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3 style='color:green;'>✅ DATABASE CONNECTION SUCCESSFUL!</h3>";

    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($tables) > 0) {
        echo "✅ Tables found: " . count($tables) . "<br>";
    } else {
        echo "⚠️ Database is empty. Need to import SQL file.<br>";
    }

} catch(PDOException $e) {
    die("<h3 style='color:red;'>❌ ERROR: " . $e->getMessage() . "</h3>");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Addis_Ababa');
?>
