<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="admin-header">
    <h1 class="logo">SuperShop Admin Panel</h1>
    <nav class="admin-nav">
        <a href="adminDashboard.php">Home</a>
        <a href="manage.php">Manage</a>
        <a href="discount.php">Discount</a>
        <a href="report.php">Report</a>
        <a href="approval.php">Approval</a>
        <a href="../Auth/logout.php">Logout</a>
    </nav>
</header>
