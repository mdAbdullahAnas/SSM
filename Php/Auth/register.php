<?php
session_start();
$base_url = "/SSM/"; 
include("../config/db.php");
include("../Includes/header.php");
include("../Includes/navbar.php");
include("../Includes/sidebar.php");

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escape all inputs and handle special characters
    $fullname = mysqli_real_escape_string($conn, htmlspecialchars($_POST['fullname']));
    $username = mysqli_real_escape_string($conn, htmlspecialchars($_POST['username']));
    $email = mysqli_real_escape_string($conn, htmlspecialchars($_POST['email']));
    $phone = mysqli_real_escape_string($conn, htmlspecialchars($_POST['phone']));
    $address = mysqli_real_escape_string($conn, htmlspecialchars($_POST['address']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // customer or vendor

    // Additional fields
    $membership = isset($_POST['membership']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['membership'])) : null;
    $experience = isset($_POST['experience']) ? mysqli_real_escape_string($conn, htmlspecialchars($_POST['experience'])) : null;

    if ($password !== $confirm_password) {
        $error = "❌ Password and Confirm Password do not match!";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "❌ Username or Email already exists!";
        } else {
            $password_hashed = md5($password);

            // Insert into database
            $sql = "INSERT INTO users (fullname, username, email, phone, address, password, role, membership, experience) 
                    VALUES ('$fullname', '$username', '$email', '$phone', '$address', '$password_hashed', '$role', '$membership', '$experience')";

            if (mysqli_query($conn, $sql)) {
                $success = "✅ Registration successful. <a href='login.php'>Login here</a>";
            } else {
                $error = "❌ Error: " . mysqli_error($conn);
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
            <input type="password" name="password" placeholder="Password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <button type="submit">Register as Customer</button>
        </form>
    </div>

    <!-- Vendor Registration Form -->
    <div id="vendorForm" class="register-container" style="display:none;">
        <h2>Vendor Registration</h2>
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
            <input type="password" name="password" placeholder="Password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

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
</script>
