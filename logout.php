<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the cookies by setting their expiration time to the past
setcookie("username", "", time() - 3600, "/");
setcookie("email", "", time() - 3600, "/");

// Redirect to the login page after logout
header("Location: login.php");
exit();
?>
