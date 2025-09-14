<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<style>

    .vendor-navbar {
    background-color: #444;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    justify-content: space-between;
}

.vendor-navbar .logo {
    color: #fff;
    font-size: 22px;
    font-weight: bold;
    margin: 0;
}

 
.vendor-nav a {
    color: #fff;
    text-decoration: none;
    margin-left: 15px;
    padding: 5px 12px;
    border-radius: 4px;
    transition: background 0.3s;
}

.vendor-nav a:hover {
    background-color: #666;
}
</style>
<header class="vendor-navbar">
    <h1 class="logo">SuperShop Vendor Panel</h1>
    <nav class="vendor-nav">
        <a href="../Product/menu.php">Home</a>
        <a href="manageProduct.php">Manage</a>
        <a href="productDelivery.php">Delivery</a>
        <a href="responseCustomerReview.php">Customer Reviews</a>
        <a href="orders.php">Orders</a>
        <a href="../Domain/profile.php">Profile</a>
         
        <a href="../Auth/logout.php">Logout</a>
    </nav>
</header>
