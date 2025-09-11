<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['userid']) || !isset($_SESSION['role'])) {
    header("Location: ../Auth/login.php");
    exit();
}

// Get the logged-in user's name from session
$logged_in_name = isset($_SESSION['name']) ? $_SESSION['name'] : "John Doe"; // default if session name not set
$role = $_SESSION['role']; // customer or vendor

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($role) ?> Profile</title>
    <link rel="stylesheet" href="../../Asset/Css/customer.css">
</head>
<body>

<?php include("navbar.php"); ?>

<main class="customer-main">
    <h2><?= ucfirst($role) ?> Profile</h2>

    <div class="customer-form">
        <!-- Display logged-in name from session -->
        <label>Name</label>
        <input type="text" value="<?= htmlspecialchars($logged_in_name) ?>" readonly>

        <!-- Dummy info for now -->
        <label>Email</label>
        <input type="email" value="example@example.com" readonly>

        <label>Phone</label>
        <input type="text" value="+880123456789" readonly>

        <label>Address</label>
        <textarea rows="3" readonly>123, Main Street, Dhaka, Bangladesh</textarea>
    </div>
</main>

</body>
</html>
