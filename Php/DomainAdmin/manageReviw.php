<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
    header("Location: ../Auth/login.php");
    exit;
}

// Delete review
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['success'] = "✅ Review deleted!";
    header("Location: manageReview.php");
    exit;
}

// Fetch all reviews
$stmt = $conn->prepare("
    SELECT r.*, p.name as product_name, u.fullname as customer_name
    FROM reviews r
    JOIN products p ON r.product_id=p.id
    JOIN users u ON r.customer_id=u.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$reviews = $stmt->get_result();

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/menu.css">
<div class="main-content">
    <h2>All Product Reviews</h2>

    <?php if(isset($_SESSION['success'])) { 
        echo "<p class='success'>".$_SESSION['success']."</p>"; 
        unset($_SESSION['success']); 
    } ?>

    <?php if($reviews->num_rows > 0): ?>
        <?php while($rev = $reviews->fetch_assoc()): ?>
            <div class="review-card">
                <h3><?= htmlspecialchars($rev['product_name']) ?></h3>
                <p><b><?= htmlspecialchars($rev['customer_name']) ?>:</b> <?= str_repeat("⭐",$rev['rating']) ?></p>
                <p><?= htmlspecialchars($rev['comment']) ?></p>
                <?php if(!empty($rev['reply'])): ?>
                    <p><b>Vendor Reply:</b> <?= htmlspecialchars($rev['reply']) ?></p>
                <?php endif; ?>
                <small><?= $rev['created_at'] ?></small>
                <br>
                <a href="manageReview.php?delete_id=<?= $rev['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger">Delete Review</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews found.</p>
    <?php endif; ?>
</div>
