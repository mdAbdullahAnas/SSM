<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

$product_id = intval($_GET['id'] ?? 0);
if(!$product_id){ die("Product not found."); }

// ---------------------- FETCH PRODUCT WITH VENDOR INFO ----------------------
$stmt = $conn->prepare("
    SELECT p.*, v.fullname as vendor_name, v.username as vendor_username, v.id as vendor_id
    FROM products p 
    JOIN vendors v ON p.vendor_id = v.id 
    WHERE p.id = ? LIMIT 1
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if(!$product){ die("Product not found."); }

// ---------------------- CUSTOMER REVIEW SUBMISSION ----------------------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_review']) && $_SESSION['role']==='customer'){
    $customer_id = $_SESSION['userid'];
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Check if customer already reviewed this product
    $stmtCheck = $conn->prepare("SELECT id FROM reviews WHERE product_id=? AND customer_id=?");
    $stmtCheck->bind_param("ii", $product_id, $customer_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    
    if($resCheck->num_rows > 0){
        $_SESSION['error'] = "❌ You have already reviewed this product.";
    } elseif($rating>=1 && $rating<=5 && !empty($comment)){
        $stmt = $conn->prepare("
            INSERT INTO reviews (product_id, customer_id, rating, comment, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiis", $product_id, $customer_id, $rating, $comment);
        $stmt->execute();
        $_SESSION['success'] = "✅ Review submitted!";
    } else {
        $_SESSION['error'] = "❌ Please provide a rating and comment.";
    }

    header("Location: productDetails.php?id=".$product_id);
    exit;
}

// ---------------------- VENDOR REVIEW SUBMISSION ----------------------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_review']) && $_SESSION['role']==='vendor'){
    $vendor_username = $_SESSION['userid']; // using vendor username
    $product_id = intval($_POST['product_id'] ?? 0);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if($product_id <= 0){
        $_SESSION['error'] = "❌ Invalid product.";
        header("Location: productDetails.php?id=".$product_id);
        exit;
    }

    // Check if vendor already reviewed
    $stmtCheck = $conn->prepare("SELECT id FROM reviews WHERE product_id=? AND vendor_username=?");
    $stmtCheck->bind_param("is", $product_id, $vendor_username);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if($resCheck->num_rows > 0){
        $_SESSION['error'] = "❌ You have already submitted a review for this product.";
    } elseif($rating>=1 && $rating<=5 && !empty($comment)){
        $stmt = $conn->prepare("
            INSERT INTO reviews 
            (product_id, customer_id, order_id, rating, comment, reply, created_at, vendor_username) 
            VALUES (?, NULL, NULL, ?, ?, NULL, NOW(), ?)
        ");
        $stmt->bind_param("iiss", $product_id, $rating, $comment, $vendor_username);
        $stmt->execute();
        $_SESSION['success'] = "✅ Review submitted successfully!";
    } else {
        $_SESSION['error'] = "❌ Please provide a rating and comment.";
    }

    header("Location: productDetails.php?id=".$product_id);
    exit;
}

// ---------------------- VENDOR REPLY ----------------------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['submit_reply']) && $_SESSION['role']==='vendor'){
    $vendor_username = $_SESSION['userid'];
    $review_id = intval($_POST['review_id']);
    $reply = trim($_POST['reply']);

    // Only allow vendor owner to reply
    $stmtCheck = $conn->prepare("
        SELECT r.id, p.vendor_id, v.username 
        FROM reviews r 
        JOIN products p ON r.product_id=p.id 
        JOIN vendors v ON p.vendor_id=v.id 
        WHERE r.id=? 
    ");
    $stmtCheck->bind_param("i", $review_id);
    $stmtCheck->execute();
    $revCheck = $stmtCheck->get_result()->fetch_assoc();

    if($revCheck && $revCheck['username']==$vendor_username){
        $stmt = $conn->prepare("UPDATE reviews SET reply=? WHERE id=?");
        $stmt->bind_param("si", $reply, $review_id);
        $stmt->execute();
        $_SESSION['success'] = "✅ Reply submitted!";
    } else {
        $_SESSION['error'] = "❌ You cannot reply to this review.";
    }

    header("Location: productDetails.php?id=".$product_id);
    exit;
}

// ---------------------- ADMIN DELETE ----------------------
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_review']) && $_SESSION['role']==='admin'){
    $review_id = intval($_POST['review_id']);
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $_SESSION['success'] = "✅ Review deleted!";
    header("Location: productDetails.php?id=".$product_id);
    exit;
}

// ---------------------- FETCH ALL REVIEWS ----------------------
$stmt = $conn->prepare("
    SELECT r.*, u.fullname as customer_name 
    FROM reviews r 
    LEFT JOIN users u ON r.customer_id=u.id 
    WHERE r.product_id=? 
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();

// ---------------------- NAVBAR ----------------------
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'customer') include("../DomainCustomer/navbar.php");
    elseif ($_SESSION['role'] === 'vendor') include("../DomainVendor/navbar.php");
    elseif ($_SESSION['role'] === 'admin') include("../DomainAdmin/navbar.php");
}
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Product/ProductDetails.css">

<div class="product-details">
    <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="big-img">
    <div class="info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p><b>Price:</b> $<?= number_format($product['price'],2) ?></p>
        <p><b>Available:</b> <?= $product['quantity'] ?></p>
        <p><b>Vendor:</b> <?= htmlspecialchars($product['vendor_name']) ?></p>
        <p><b>Description:</b> <?= htmlspecialchars($product['description']) ?></p>
                   
    </div>
</div>

<div class="review-section">
    <h3>Customer Reviews</h3>

    <?php if(isset($_SESSION['success'])) { echo "<p class='success'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>
    <?php if(isset($_SESSION['error'])) { echo "<p class='error'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>

    <?php if($reviews->num_rows > 0): ?>
        <?php while($rev = $reviews->fetch_assoc()): ?>
            <div class="review-card">
                <p><b><?= htmlspecialchars($rev['customer_name'] ?? 'Guest') ?>:</b> <?= str_repeat("⭐", $rev['rating']) ?></p>
                <p><?= htmlspecialchars($rev['comment']) ?></p>
                <small><?= $rev['created_at'] ?></small>

                <!-- Vendor reply -->
                <?php if(!empty($rev['reply'])): ?>
                    <p class="vendor-reply"><b>Vendor Reply (<?= htmlspecialchars($rev['vendor_username'] ?? $product['vendor_username']) ?>):</b> <?= htmlspecialchars($rev['reply']) ?></p>
                <?php elseif(isset($_SESSION['role']) && $_SESSION['role']==='vendor' && $product['vendor_username']==$_SESSION['userid']): ?>
                    <form method="POST" class="reply-form">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <textarea name="reply" placeholder="Write your reply..." required></textarea>
                        <button type="submit" name="submit_reply">Reply</button>
                    </form>
                <?php endif; ?>

                <!-- Admin delete -->
                <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                    <form method="POST" onsubmit="return confirm('Delete this review?');">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <button type="submit" name="delete_review" class="btn-delete">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>

    <!-- Vendor submit review -->
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='vendor'): ?>
        <form method="POST" class="review-form">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <label>Rating (1–5):</label>
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
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    <?php endif; ?>
</div>
