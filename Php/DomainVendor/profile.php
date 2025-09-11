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
    <title>Vendor Profile</title>
    <link rel="stylesheet" href="../../Asset/Css/vendor.css">
</head>
<?php include("navbar.php"); ?>
<main class="vendor-main" id="profile">
    <h2>Vendor Profile</h2>
    <form class="vendor-form">
        <label for="vendorName">Name</label>
        <input type="text" id="vendorName" value="<?php echo $_SESSION['userid']; ?>">

        <label for="vendorEmail">Email</label>
        <input type="email" id="vendorEmail" value="vendor@example.com">

        <label for="vendorPassword">Password</label>
        <input type="password" placeholder="********">

        <button type="submit" class="btn">Update Profile</button>
    </form>
</main>
