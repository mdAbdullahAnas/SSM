<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../Auth/login.php");
    exit();
}
 
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor response customer</title>
    <link rel="stylesheet" href="../../Asset/Css/vendor.css">
</head>
<?php include("navbar.php"); ?>
<main class="vendor-main" id="reviews">
    <h2>Customer Reviews</h2>
    <p>Here you can respond to your customer reviews.</p>

    <div class="review-list">
        <!-- Example review -->
        <div class="review-item">
            <p><b>Customer:</b> John Doe</p>
            <p><b>Product:</b> Wireless Mouse</p>
            <p><b>Review:</b> Great product!</p>
            <textarea placeholder="Write your response"></textarea>
            <button class="btn">Respond</button>
        </div>
    </div>
</main>
