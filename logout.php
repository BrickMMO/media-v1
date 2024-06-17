<?php
session_start();
$_SESSION = array();

session_destroy();

// Clear the cookies 
setcookie("username", "", time() - 3600, "/");
setcookie("email", "", time() - 3600, "/");

header("Location: login.php");
exit();
?>
