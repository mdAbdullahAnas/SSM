<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../../Asset/Css/customer.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="customer-main" id="dashboard">
        <?php if (isset($_SESSION['login_success'])): ?>
            <p class="success"><?php echo $_SESSION['login_success']; ?></p>
            <?php unset($_SESSION['login_success']); ?>
        <?php endif; ?>

        <h2>Welcome, <?php echo $_SESSION['userid']; ?> 👋</h2>
        <p>Select an option from the navigation bar to explore products, view your cart, and make payments.</p>
    </main>
</body>
</html>
