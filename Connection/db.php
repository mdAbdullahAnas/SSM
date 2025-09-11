<?php
$host = "localhost";   // DB host
$user = "root";        // DB username (XAMPP default: root)
$pass = "";            // DB password (XAMPP default: empty)
$dbname = "supershop";       // Database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>
