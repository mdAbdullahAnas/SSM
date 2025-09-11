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
    <title>Review</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/customer.css">
</head>
<?php include("navbar.php"); ?>
<main class="customer-main" id="review">
    <h2>Write Product Review</h2>

    <form class="customer-form">
        <label for="productName">Product</label>
        <input type="text" id="productName" placeholder="Purchased Product Name">

        <label for="reviewText">Review</label>
        <textarea id="reviewText" placeholder="Write your review here"></textarea>

        <button class="btn">Submit Review</button>
    </form>
</main>
