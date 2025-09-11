<?php
session_start();
$base_url = "/SSM/"; 

include("../Includes/header.php");
include("../Includes/navbar.php");
include("../Includes/sidebar.php");
include("../../Connection/db.php"); 

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, htmlspecialchars($_POST['fullname']));
    $username = mysqli_real_escape_string($conn, htmlspecialchars($_POST['username']));
    $email = mysqli_real_escape_string($conn, htmlspecialchars($_POST['email']));
    $phone = mysqli_real_escape_string($conn, htmlspecialchars($_POST['phone']));
    $address = mysqli_real_escape_string($conn, htmlspecialchars($_POST['address']));
    $password = mysqli_real_escape_string($conn, htmlspecialchars($_POST['password']));
    $confirm_password = mysqli_real_escape_string($conn, htmlspecialchars($_POST['confirm_password']));
    $role = $_POST['role']; // "customer" or "vendor"

    $membership = isset($_POST['membership']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['membership'])) : null;
    $experience = isset($_POST['experience']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['experience'])) : null;

    if ($password !== $confirm_password) {
        $error = "❌ Password and Confirm Password do not match!";
    } else {
        if ($role === "customer") {
            // Insert into users table
            $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $error = "❌ Username or Email already exists!";
            } else {
                $sql = "INSERT INTO users (fullname, username, email, phone, address, password) 
                        VALUES ('$fullname', '$username', '$email', '$phone', '$address', '$password')";
                if (mysqli_query($conn, $sql)) {
                    $success = "✅ Customer registered successfully. ";
                } else {
                    $error = "❌ Error: " . mysqli_error($conn);
                }
            }
        } elseif ($role === "vendor") {
            // Insert into vendors table
            $check = mysqli_query($conn, "SELECT * FROM vendors WHERE username='$username' OR email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $error = "❌ Username or Email already exists!";
            } else {
                $sql = "INSERT INTO vendors (fullname, username, email, phone, address, password, membership, experience) 
                        VALUES ('$fullname', '$username', '$email', '$phone', '$address', '$password', '$membership', '$experience')";
                if (mysqli_query($conn, $sql)) {
                    $success = "✅ Vendor registered successfully.";
                } else {
                    $error = "❌ Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>


<link rel="stylesheet" href="../../Asset/Css/auth.css">

<div class="wrf">
    <h2>Which role do you want to register?</h2>
    <label>
        <input type="radio" name="chooseRole" value="customer" checked> Customer
    </label>
    <label>
        <input type="radio" name="chooseRole" value="vendor"> Vendor
    </label>
</div>

<div class="main-content">

    <!-- Customer Registration Form -->
    <div id="customerForm" class="register-container">
        <h2>Customer Registration</h2>
        <?php 
            if(!empty($error)) echo "<p class='error'>$error</p>";
            if(!empty($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="POST" action="">
            <input type="hidden" name="role" value="customer">

            <label>Full Name:</label>
            <input type="text" name="fullname" placeholder="Full Name" required>

            <label>Username:</label>
            <input type="text" name="username" placeholder="Username" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Email" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <label>Address:</label>
            <input type="text" name="address" placeholder="Address" required>

            <label>Membership:</label>
            <select name="membership" required>
                <option value="">Select Membership</option>
                <option value="basic">Basic</option>
                <option value="premium">Premium</option>
                <option value="gold">Gold</option>
            </select>

            <label>Password:</label>
            <div style="position: relative;">
                <input type="password" id="cust_password" name="password" placeholder="Password" required style="padding-right:40px;">
                <span onclick="togglePassword('cust_password')" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
            </div>

            <label>Confirm Password:</label>
            <div style="position: relative;">
                <input type="password" id="cust_confirm_password" name="confirm_password" placeholder="Confirm Password" required style="padding-right:40px;">
                <span onclick="togglePassword('cust_confirm_password')" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
            </div>

            <button type="submit">Register as Customer</button>
        </form>
    </div>

    <!-- Vendor Registration Form -->
    <div id="vendorForm" class="register-container" style="display:none;">
        <h2>Vendor Registration</h2>
        <?php 
            if(!empty($error)) echo "<p class='error'>$error</p>";
            if(!empty($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="POST" action="">
            <input type="hidden" name="role" value="vendor">

            <label>Full Name:</label>
            <input type="text" name="fullname" placeholder="Full Name" required>

            <label>Username:</label>
            <input type="text" name="username" placeholder="Username" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Email" required>

            <label>Phone Number:</label>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <label>Address:</label>
            <input type="text" name="address" placeholder="Address" required>

            <label>Experience:</label>
            <input type="text" name="experience" placeholder="Experience" required>

            <label>Password:</label>
            <div style="position: relative;">
                <input type="password" id="vend_password" name="password" placeholder="Password" required style="padding-right:40px;">
                <span onclick="togglePassword('vend_password')" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
            </div>

            <label>Confirm Password:</label>
            <div style="position: relative;">
                <input type="password" id="vend_confirm_password" name="confirm_password" placeholder="Confirm Password" required style="padding-right:40px;">
                <span onclick="togglePassword('vend_confirm_password')" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
            </div>

            <button type="submit">Register as Vendor</button>
        </form>
    </div>

</div>

<script>
// toggle forms
document.querySelectorAll('input[name="chooseRole"]').forEach((radio) => {
    radio.addEventListener("change", function() {
        if (this.value === "customer") {
            document.getElementById("customerForm").style.display = "block";
            document.getElementById("vendorForm").style.display = "none";
        } else {
            document.getElementById("customerForm").style.display = "none";
            document.getElementById("vendorForm").style.display = "block";
        }
    });
});

// toggle password visibility
function togglePassword(id) {
    const field = document.getElementById(id);
    field.type = field.type === "password" ? "text" : "password";
}
</script>
