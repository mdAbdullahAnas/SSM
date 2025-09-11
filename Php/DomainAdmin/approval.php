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
    <title>Product Approval</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/admin.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="admin-main" id="approval">
        <h2>Product Approval</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Vendor</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Example Product</td>
                    <td>Vendor A</td>
                    <td>Pending</td>
                    <td>
                        <button class="btn approve">Approve</button>
                        <button class="btn reject">Reject</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
