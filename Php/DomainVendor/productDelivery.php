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
    <title>Vendor product delivery</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/vendor.css">
</head>
<?php include("navbar.php"); ?>
<main class="vendor-main" id="delivery">
    <h2>Product Delivery Management</h2>

    <table class="delivery-table">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <tr>
            <td>101</td>
            <td>John Doe</td>
            <td>Wireless Mouse</td>
            <td>Pending</td>
            <td><button class="btn">Mark as Delivered</button></td>
        </tr>
    </table>
</main>
