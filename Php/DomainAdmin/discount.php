<?php
session_start();

// Check session & role
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discount Management</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/admin.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="admin-main" id="discount">
        <h2>Discount Management</h2>
        <form class="admin-form">
            <label for="discountName">Discount Name</label>
            <input type="text" id="discountName" name="discountName" placeholder="e.g. Summer Sale">

            <label for="discountValue">Discount Value (%)</label>
            <input type="number" id="discountValue" name="discountValue" placeholder="10">

            <button type="submit" class="btn">Create Discount</button>
        </form>
    </main>
</body>
</html>
