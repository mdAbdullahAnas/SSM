<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

// ✅ Get correct customer id
if (!is_numeric($_SESSION['userid'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $_SESSION['userid']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $customer_id = $row['id'];
    } else {
        die("Customer not found!");
    }
} else {
    $customer_id = intval($_SESSION['userid']);
}

// ✅ Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $product_id = intval($_POST['product_id']);
    $order_id   = intval($_POST['order_id']);
    $rating     = intval($_POST['rating']);
    $comment    = trim($_POST['comment']);

    // Check if review already exists
    $stmtCheck = $conn->prepare("SELECT id FROM reviews WHERE product_id=? AND customer_id=? AND order_id=? LIMIT 1");
    $stmtCheck->bind_param("iii", $product_id, $customer_id, $order_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck->num_rows > 0) {
        $_SESSION['error'] = "❌ You have already submitted a review for this product in this order.";
    } else {
        if($rating >= 1 && $rating <= 5 && !empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO reviews (product_id, customer_id, order_id, rating, comment, created_at) 
                                    VALUES (?,?,?,?,?,NOW())");
            $stmt->bind_param("iiiis", $product_id, $customer_id, $order_id, $rating, $comment);
            $stmt->execute();
            $_SESSION['success'] = "✅ Review submitted successfully!";
        } else {
            $_SESSION['error'] = "❌ Please provide a rating (1–5) and a comment.";
        }
    }
}

// ✅ Fetch all delivered products for this customer
$stmt = $conn->prepare("
    SELECT o.id AS order_id, p.id AS product_id, p.name, p.img
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.customer_id=? AND o.status='Delivered'
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$res = $stmt->get_result();
$delivered_products = $res->fetch_all(MYSQLI_ASSOC);

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/style.css">

<div class="main-content">
    <h2>Product Reviews</h2>

    <?php if(isset($_SESSION['success'])) { echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
    <?php if(isset($_SESSION['error'])) { echo "<p style='color:red'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>

    <?php if(empty($delivered_products)): ?>
        <p>You have no delivered products to review yet.</p>
    <?php else: ?>
        <?php foreach($delivered_products as $dp): ?>
            <div class="review-card">
                <h3><?= htmlspecialchars($dp['name']) ?></h3>
                <img src="<?= htmlspecialchars($dp['img']) ?>" alt="<?= htmlspecialchars($dp['name']) ?>">

                <?php
                // Check if review already exists for this product + order
                $stmtCheck = $conn->prepare("SELECT id FROM reviews WHERE product_id=? AND customer_id=? AND order_id=? LIMIT 1");
                $stmtCheck->bind_param("iii", $dp['product_id'], $customer_id, $dp['order_id']);
                $stmtCheck->execute();
                $resCheck = $stmtCheck->get_result();
                $hasReview = $resCheck->num_rows > 0;
                ?>

                <?php if(!$hasReview): ?>
                <!-- Review Form -->
                <form method="POST" class="review-form">
                    <input type="hidden" name="product_id" value="<?= $dp['product_id'] ?>">
                    <input type="hidden" name="order_id" value="<?= $dp['order_id'] ?>">

                    <label>Rating (1-5):</label>
                    <select name="rating" required>
                        <option value="">--Select--</option>
                        <option value="1">⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="5">⭐⭐⭐⭐⭐</option>
                    </select>

                    <label>Your Review:</label>
                    <textarea name="comment" required></textarea>

                    <button type="submit" name="submit_review" class="btn">Submit Review</button>
                </form>
                <?php else: ?>
                    <p style="color:blue; font-weight:bold;">✅ You have already reviewed this product for this order.</p>
                <?php endif; ?>

                <!-- Show existing reviews -->
                <div class="review-list">
                    <h4>Customer Reviews:</h4>
                    <?php
                    $stmtRev = $conn->prepare("SELECT r.*, u.fullname 
                                               FROM reviews r 
                                               JOIN users u ON r.customer_id=u.id 
                                               WHERE r.product_id=? 
                                               ORDER BY r.created_at DESC");
                    $stmtRev->bind_param("i", $dp['product_id']);
                    $stmtRev->execute();
                    $resRev = $stmtRev->get_result();
                    if ($resRev->num_rows > 0):
                        while($rev = $resRev->fetch_assoc()):
                    ?>
                        <p>
                            <b><?= htmlspecialchars($rev['fullname']) ?>:</b> <?= str_repeat("⭐", $rev['rating']) ?><br>
                            <?= htmlspecialchars($rev['comment']) ?><br>
                            <small><?= $rev['created_at'] ?></small>
                        </p>
                    <?php endwhile; else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
