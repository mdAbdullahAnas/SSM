<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    
/* Header + Navbar */
.admin-header {
    background: #222;
    padding: 15px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-header .logo {
    margin: 0;
    font-size: 22px;
}

.admin-nav a {
    color: white;
    text-decoration: none;
    margin: 0 12px;
    padding: 6px 12px;
    border-radius: 5px;
    transition: background 0.3s;
}

.admin-nav a:hover {
    background: #444;
}
</style>
<header class="admin-header">
    <h1 class="logo">SuperShop Admin Panel</h1>
    <nav class="admin-nav">
        <a href="adminDashboard.php">Home</a>
        <a href="manage.php">Manage</a>
        <a href="discount.php">Discount</a>
        <a href="report.php">Report</a>
        <a href="approval.php">Approval</a>
        <a href="orders.php">Orders</a>
        <a href="../Auth/logout.php">Logout</a>
    </nav>
</header>
