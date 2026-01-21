<?php
// update_status.php
require_once 'config.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    
    $allowedStatuses = ['Pending', 'Under Review', 'Approved', 'Rejected'];
    
    if (in_array($status, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE applications SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        echo 'OK';
    } else {
        echo 'Invalid status';
    }
} else {
    echo 'Invalid request';
}
?>
