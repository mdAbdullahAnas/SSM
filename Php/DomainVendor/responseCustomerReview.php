<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role']!=='vendor'){
    header("Location: ../Auth/login.php");
    exit;
}
$vendor_id = $_SESSION['userid'];

// Handle vendor reply submission
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reply_text'])){
    $review_id = intval($_POST['review_id']);
    $reply = trim($_POST['reply_text']);
    if(!empty($reply)){
        $stmt = $conn->prepare("UPDATE reviews SET reply=? WHERE id=? AND reply IS NULL");
        $stmt->bind_param("si", $reply, $review_id);
        $stmt->execute();
        $_SESSION['success']="✅ Reply added!";
    }
}

// Fetch reviews for vendor's products
$stmt = $conn->prepare("
    SELECT r.*, p.name as product_name, u.fullname as customer_name
    FROM reviews r
    JOIN products p ON r.product_id=p.id
    JOIN users u ON r.customer_id=u.id
    WHERE p.vendor_id=?
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$reviews = $stmt->get_result();

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/menu.css">
<div class="main-content">
    <h2>Customer Reviews for Your Products</h2>

    <?php if(isset($_SESSION['success'])) { echo "<p class='success'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>

    <?php if($reviews->num_rows > 0): ?>
        <?php while($rev = $reviews->fetch_assoc()): ?>
            <div class="review-card">
                <h3><?= htmlspecialchars($rev['product_name']) ?></h3>
                <p><b><?= htmlspecialchars($rev['customer_name']) ?>:</b> <?= str_repeat("⭐",$rev['rating']) ?></p>
                <p><?= htmlspecialchars($rev['comment']) ?></p>
                <small><?= $rev['created_at'] ?></small>

                <?php if(!empty($rev['reply'])): ?>
                    <p class="vendor-reply"><b>Your Reply:</b> <?= htmlspecialchars($rev['reply']) ?></p>
                <?php else: ?>
                    <form method="POST" class="reply-form">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <textarea name="reply_text" placeholder="Write a reply..." required></textarea>
                        <button type="submit">Reply</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>
