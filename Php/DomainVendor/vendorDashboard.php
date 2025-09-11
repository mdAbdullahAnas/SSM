<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../Auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/vendor.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="vendor-main" id="dashboard">
        <?php if (isset($_SESSION['login_success'])): ?>
            <p class="success"><?php echo $_SESSION['login_success']; ?></p>
            <?php unset($_SESSION['login_success']); ?>
        <?php endif; ?>

        <h2>Welcome, <?php echo $_SESSION['userid']; ?> 👋</h2>
        <a href="../Product/menu.php"></a>
        <p>Select an option from the navigation bar to manage your products and orders.</p>
    </main>
</body>
</html>
