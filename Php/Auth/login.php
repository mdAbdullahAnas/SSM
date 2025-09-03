<?php
session_start();
$base_url = "/SSM/";
include("../config/db.php");
include("../Includes/header.php");
include("../Includes/navbar.php");
include("../Includes/sidebar.php");


$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE userid='$userid' AND password=MD5('$password')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['userid'] = $user['userid'];
        header("Location: ../../index.php");
        exit();
    } else {
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
        <input type="password" name="password" placeholder="Enter Password" required>

        <button type="submit">Login</button>
    </form>
</div>
<?php  
 
