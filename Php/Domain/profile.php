<?php
session_start();
include("../../Connection/db.php");

if (!isset($_SESSION['userid']) || !isset($_SESSION['role'])) {
    header("Location: ../Auth/login.php");
    exit();
}

$userid = $_SESSION['userid'];
$role = $_SESSION['role'];

if($role === 'admin') {
    die("Admin profile editing is not allowed here.");
}

// Fetch profile
$table = $role === 'vendor' ? 'vendors' : 'users';
$profile = null;

// Handle POST (Update / Delete)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = $_POST['password'] ?? '';
    
    $extra = "";
    if(!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $extra = ", password='$hashed'";
    }

    if(isset($_POST['update'])) {
        if($role === 'vendor') {
            $experience = mysqli_real_escape_string($conn, $_POST['experience']);
            $sql = "UPDATE vendors SET fullname='$fullname', username='$username', email='$email', phone='$phone', address='$address', experience='$experience' $extra WHERE username='$userid'";
        } else {
            $membership = mysqli_real_escape_string($conn, $_POST['membership']);
            $sql = "UPDATE users SET fullname='$fullname', username='$username', email='$email', phone='$phone', address='$address', membership='$membership' $extra WHERE username='$userid'";
        }
        if(mysqli_query($conn, $sql)) {
            $message = "✅ Profile updated successfully!";
            $userid = $username; // update session variable in case username changed
            $_SESSION['userid'] = $username;
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
    }

    if(isset($_POST['delete'])) {
        $sql = "DELETE FROM $table WHERE username='$userid'";
        if(mysqli_query($conn, $sql)) {
            session_destroy();
            header("Location: ../Auth/login.php");
            exit();
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
    }
}

// Fetch updated profile
$res = mysqli_query($conn, "SELECT * FROM $table WHERE username='$userid'");
$profile = mysqli_fetch_assoc($res);
if(!$profile) die("No profile found for user: $userid");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($role) ?> Profile</title>
    <link rel="stylesheet" href="../../Asset/Css/Domain/profile.css">
</head>
<body>

<!-- <?php 
if($role === 'vendor') include("../DomainVendor/navbar.php");
if($role === 'customer') include("../DomainCustomer/navbar.php");
?> -->

<main class="profile-container">
    <h2><?= ucfirst($role) ?> Profile</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>

    <div class="profile-card">
        <!-- Left Section -->
        <div class="profile-picture">
            <?= substr($profile['fullname'],0,1) ?>
        </div>

        <!-- Right Section - Form -->
        <div class="profile-details">
            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($profile['fullname']) ?>" required>

                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($profile['username']) ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>">

                <label>Address</label>
                <textarea name="address" rows="3"><?= htmlspecialchars($profile['address']) ?></textarea>

                <?php if($role === 'vendor'): ?>
                    <label>Experience</label>
                    <input type="text" name="experience" value="<?= htmlspecialchars($profile['experience']) ?>">
                <?php elseif($role === 'customer'): ?>
                    <label>Membership</label>
                    <input type="text" name="membership" value="<?= htmlspecialchars($profile['membership']) ?>">
                <?php endif; ?>

                <label>Password</label>
                <input type="password" name="password" placeholder="Leave blank to keep current">

                <div style="margin-top:20px;">
                    <button type="submit" name="update" class="btn">Update Profile</button>
                    <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Are you sure? This cannot be undone!')">Delete Profile</button>
                </div>
            </form>
        </div>
    </div>
</main>
</body>
</html>
