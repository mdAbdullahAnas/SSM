<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="customer-navbar">
    <h1 class="logo">SuperShop Customer Panel</h1>
    <nav class="customer-nav">
        <a href="../Product/menu.php">Home</a>
        <a href="addItemToCart.php">Cart</a>
        <a href="makePayment.php">Payment</a>
        <a href="writeProductReview.php">Write Review</a>
         <a href="../Domain/profile.php">Profile</a>
    
        <a href="../Auth/logout.php">Logout</a>
    </nav>
</header>
