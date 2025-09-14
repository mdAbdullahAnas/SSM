<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    
.customer-navbar {
    background-color: #444;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    justify-content: space-between;
}

.customer-navbar .logo {
    color: #fff;
    font-size: 22px;
    font-weight: bold;
    margin: 0;
}

.customer-nav a {
    color: #fff;
    text-decoration: none;
    margin-left: 15px;
    padding: 5px 12px;
    border-radius: 4px;
    transition: background 0.3s;
}

.customer-nav a:hover {
    background-color: #666;
}

</style>
<header class="customer-navbar">
    <h1 class="logo">SuperShop Customer Panel</h1>
    <nav class="customer-nav">
        <a href="/SSM/Php/Product/menu.php">Home</a>
        <a href="/SSM/Php/DomainCustomer/cart.php">Cart</a>
        <a href="/SSM/Php/DomainCustomer/makePayment.php">Payment</a>
        <a href="/SSM/Php/DomainCustomer/writeProductReview.php">Write Review</a>
        <a href="/SSM/Php/Domain/profile.php">Profile</a>
        <a href="/SSM/Php/Auth/logout.php">Logout</a>
    </nav>
</header>
