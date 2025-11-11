<?php
$host = "localhost";
$user = "root";   // your MySQL username
$pass = "";       // your MySQL password (keep blank if none)
$db   = "books";  // your database name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}
?>
