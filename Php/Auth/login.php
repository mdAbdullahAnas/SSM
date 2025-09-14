<?php
session_start();
$base_url = "/SSM/";
include("../Includes/header.php");
include("../Includes/navbar.php");
include("../Includes/sidebar.php");
include("../../Connection/db.php");

$error = "";

// Default users (backup, plaintext)
$default_users = [
    "admin"    => ["password" => "ASDFGHJKL;'", "role" => "admin"],
    "vendor"   => ["password" => "ASDFGHJKL;'", "role" => "vendor"],
    "customer" => ["password" => "ASDFGHJKL;'", "role" => "customer"]
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = trim($_POST['userid']);
    $password = trim($_POST['password']);

    // Flag to check if login succeeded
    $logged_in = false;

    // 1️⃣ Check Admin table
    $stmt = $conn->prepare("SELECT ID, password FROM admin WHERE ID = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['ID'];
            $_SESSION['role'] = "admin";
            header("Location: ../DomainAdmin/adminDashboard.php");
            exit();
        } else {
            $error = "❌ Wrong password!";
            $logged_in = false;
        }
    }

    // 2️⃣ Check Users table (customers)
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['username'];
            $_SESSION['role'] = "customer";
            header("Location: ../DomainCustomer/customerDashboard.php");
            exit();
        } else {
            $error = "❌ Wrong password!";
            $logged_in = false;
        }
    }

    // 3️⃣ Check Vendors table
    $stmt = $conn->prepare("SELECT username, password FROM vendors WHERE username = ?");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['username'];
            $_SESSION['role'] = "vendor";
            header("Location: ../DomainVendor/vendorDashboard.php");
            exit();
        } else {
            $error = "❌ Wrong password!";
            $logged_in = false;
        }
    }

   
 


    // 4️⃣ Fallback: Default Users (plaintext)
    if (isset($default_users[$userid]) && $default_users[$userid]['password'] === $password) {
        $_SESSION['userid'] = $userid;
        $_SESSION['role'] = $default_users[$userid]['role'];

        if ($_SESSION['role'] === "admin") {
            header("Location: ../DomainAdmin/adminDashboard.php");
        } elseif ($_SESSION['role'] === "vendor") {
            header("Location: ../DomainVendor/vendorDashboard.php");
        } else {
            header("Location: ../DomainCustomer/customerDashboard.php");
        }
        exit();
    }

    // ❌ If nothing matched
    if ($error === "") {
        $error = "❌ Invalid User ID or Password!";
    }
}
?>

<link rel="stylesheet" href="../../Asset/Css/auth.css">
<div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST" action="">
        <label for="userid">User ID</label>
        <input type="text" name="userid" placeholder="Enter User ID" required>

        <label for="password">Password</label>
        <div style="position: relative;">
            <input type="password" id="password" name="password" placeholder="Enter Password" required style="padding-right:40px;">
            <span onclick="togglePassword()" style="position:absolute; right:10px; top:30%; transform:translateY(-50%); cursor:pointer;">
                👁️
            </span>
        </div>

        <button type="submit">Login</button>
    </form>
</div>

<script>
function togglePassword() {
    const passField = document.getElementById("password");
    passField.type = passField.type === "password" ? "text" : "password";
}
</script>
