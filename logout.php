<?php
// logout.php - Admin logout script
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to logout confirmation page
header('Location: logout.html');
exit;
?>
