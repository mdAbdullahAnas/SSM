<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="vendor-navbar">
    <h1 class="logo">SuperShop Vendor Panel</h1>
    <nav class="vendor-nav">
        <a href="../Product/menu.php">Home</a>
        <a href="manageProduct.php">Manage</a>
        <a href="productDelivery.php">Delivery</a>
        <a href="responseCustomerReview.php">Customer Reviews</a>
        <a href="../Domain/profile.php">Profile</a>
         
        <a href="../Auth/logout.php">Logout</a>
    </nav>
</header>
