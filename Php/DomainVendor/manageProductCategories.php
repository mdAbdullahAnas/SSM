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
    <title>Vendor manage product</title>
    <link rel="stylesheet" href="../../Asset/Css/vendor.css">
</head>
<?php include("navbar.php"); ?>
<main class="vendor-main" id="categories">
    <h2>Manage Product Categories</h2>

    <form class="vendor-form">
        <label for="categoryName">Category Name</label>
        <input type="text" id="categoryName" name="categoryName" placeholder="New Category">
        <button type="submit" class="btn">Add Category</button>
    </form>

    <div class="category-list">
        <p><b>Existing Categories:</b></p>
        <ul>
            <li>Electronics</li>
            <li>Clothing</li>
            <li>Accessories</li>
        </ul>
    </div>
</main>
