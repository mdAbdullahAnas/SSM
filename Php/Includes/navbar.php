<?php
session_start();
$base_url = "/SSM/"; 
?>
 
<link rel="stylesheet" href="../../Asset/Css/includes.css">

<nav class="navbar">
    <!-- Logo -->
    <a href="<?php echo $base_url; ?>index.php" class="logo">SSM</a>

    <div id="loginSignup">
        <!-- Right side links -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <span class="welcome-text">
                Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="<?php echo $base_url; ?>Php/Auth/logout.php" class="nav-link">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>Php/Auth/register.php" class="nav-link">Sign Up</a>
            <a href="<?php echo $base_url; ?>Php/Auth/login.php" class="nav-link">Login</a>
        <?php endif; ?>
    </div>
</nav>
