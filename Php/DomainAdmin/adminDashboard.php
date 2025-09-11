<?php
session_start();

// Check session & role
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../Asset/Css/admin.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="admin-main" id="dashboard">
        <?php if (isset($_SESSION['login_success'])): ?>
            <p class="success"><?php echo $_SESSION['login_success']; ?></p>
            <?php unset($_SESSION['login_success']); ?>
        <?php endif; ?>

        <h2>Welcome, <?php echo $_SESSION['userid']; ?> 👋</h2>
        <p>Select an option from the navigation bar.</p>
    </main>
</body>
</html>
