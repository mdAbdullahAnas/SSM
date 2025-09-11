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
    <title>Make Payment</title>
    <link rel="stylesheet" href="../../Asset/Css/customer.css">
</head>
<?php include("navbar.php"); ?>
<main class="customer-main" id="payment">
    <h2>Payment</h2>
    <form class="customer-form">
        <label for="cardNumber">Card Number</label>
        <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456">

        <label for="expiry">Expiry Date</label>
        <input type="text" id="expiry" placeholder="MM/YY">

        <label for="cvv">CVV</label>
        <input type="text" id="cvv" placeholder="123">

        <button class="btn">Pay Now</button>
    </form>
</main>
