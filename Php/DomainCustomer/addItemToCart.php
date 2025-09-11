<?php
session_start();

// Check session & role
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit();
}

 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Card</title>
    <link rel="stylesheet" href="../../Asset/Css/customer.css">
</head>
<?php include("navbar.php"); ?>
<main class="customer-main" id="cart">
    <h2>Shopping Cart</h2>
    <p>Add products to your cart here.</p>

    <div class="cart-list">
        <div class="cart-item">
            <p><b>Product:</b> Wireless Mouse</p>
            <p><b>Price:</b> $25.00</p>
            <input type="number" value="1" min="1">
            <button class="btn">Add to Cart</button>
        </div>
    </div>
</main>
