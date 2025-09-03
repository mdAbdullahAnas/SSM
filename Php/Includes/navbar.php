<?php
session_start();
$base_url = "/SSM/"; // Adjust if your project folder changes
?>
<nav>
    <!-- Logo -->
    <a href="<?php echo $base_url; ?>index.php">SSM</a>

    <!-- Right side links -->
    <?php if(isset($_SESSION['user_id'])): ?>
        <span style="color:white; margin-left:auto; margin-right:10px;">
            Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
        <a href="<?php echo $base_url; ?>Php/Auth/logout.php" class="float-right">Logout</a>
    <?php else: ?>
        <a href="<?php echo $base_url; ?>Php/Auth/register.php" class="float-right">Sign Up</a>
        <a href="<?php echo $base_url; ?>Php/Auth/login.php" class="float-right">Login</a>
    <?php endif; ?>
</nav>
