<?php
// Start or resume session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the index page after logout
header("Location: index.php");
exit;
?>
