<?php
session_start();
session_destroy();

// Redirect to index.html or login page
header("Location: ../../index.php"); // Adjust path relative to logout.php
exit();
?>
