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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/admin.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="admin-main" id="profile">
        <h2>Profile Settings</h2>
        <form class="admin-form">
            <label for="adminName">Name</label>
            <input type="text" id="adminName" name="adminName" value="<?php echo $_SESSION['admin_name']; ?>">

            <label for="adminEmail">Email</label>
            <input type="email" id="adminEmail" name="adminEmail" value="admin@example.com">

            <label for="adminPassword">Password</label>
            <input type="password" id="adminPassword" name="adminPassword" placeholder="********">

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </main>
</body>
</html>
