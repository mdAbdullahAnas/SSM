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
    <title>Report Generation</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/admin.css">
</head>
<body>
    <?php include("navbar.php"); ?>

    <main class="admin-main" id="report">
        <h2>Generate Reports</h2>
        <form class="admin-form">
            <label for="reportType">Report Type</label>
            <select id="reportType" name="reportType">
                <option value="sales">Sales Report</option>
                <option value="inventory">Inventory Report</option>
            </select>

            <label for="reportDate">Select Date</label>
            <input type="date" id="reportDate" name="reportDate">

            <button type="submit" class="btn">Generate</button>
        </form>
    </main>
</body>
</html>
