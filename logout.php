<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the cookies
setcookie("username", "", time() - 3600, "/");
setcookie("email", "", time() - 3600, "/");

header("Location: login.php");
exit();
?>
