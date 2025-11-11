<?php
include 'config.php';
session_start();

session_unset();
session_destroy();

// 👇 Logout ke baad ab login.php nahi, home.php par bhejna hai
header('location:home.php');
exit();
?>
