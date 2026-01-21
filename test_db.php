<?php
// test_db.php - DIRECT CONNECTION TEST
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Direct Database Test</h3>";

$host = 'sql303.infinityfree.com';
$user = 'if0_40927267';
$pass = 'oqcpKm1vFRNpeDH';
$db   = 'if0_40927267_visa_application';

echo "Trying to connect...<br>";

// Method 1: mysqli
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    echo "❌ mysqli Error: " . $mysqli->connect_error . "<br>";
} else {
    echo "✅ mysqli Connected!<br>";
    
    // Check tables
    $result = $mysqli->query("SHOW TABLES");
    echo "Tables found: " . $result->num_rows . "<br>";
    while($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
    
    // Check for admin table
    $result = $mysqli->query("SHOW TABLES LIKE '%admin%'");
    if ($result->num_rows > 0) {
        echo "✅ Admin table exists<br>";
        
        // Check for admin user
        $admin_result = $mysqli->query("SELECT * FROM admin LIMIT 1");
        if ($admin_result->num_rows > 0) {
            echo "✅ Admin users exist in database<br>";
            $admin = $admin_result->fetch_assoc();
            echo "First admin: " . ($admin['username'] ?? 'Unknown') . "<br>";
        } else {
            echo "❌ No admin users found in table<br>";
        }
    } else {
        echo "❌ No admin table found<br>";
        
        // List all tables
        echo "<br>All tables:<br>";
        $all_tables = $mysqli->query("SHOW TABLES");
        while($table = $all_tables->fetch_array()) {
            echo "- " . $table[0] . "<br>";
        }
    }
    
    $mysqli->close();
}

echo "<hr>";

// Method 2: PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ PDO Connection Successful!<br>";
} catch(PDOException $e) {
    echo "❌ PDO Error: " . $e->getMessage() . "<br>";
}
?>