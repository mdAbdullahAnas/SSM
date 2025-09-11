<?php
session_start();
include("db.php");

$product_id = $_GET['id'] ?? 0;

// Fetch product
$stmt = $conn->prepare("SELECT p.*, u.username as vendor_name FROM products p JOIN users u ON p.vendor_id=u.id WHERE p.id=?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch reviews
$rev_stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id=u.id WHERE product_id=? ORDER BY created_at DESC");
$rev_stmt->execute([$product_id]);
$reviews = $rev_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= $product['name'] ?></h2>
<img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>">
<p>Price: $<?= $product['price'] ?></p>
<p>Available: <?= $product['quantity'] ?></p>
<p>Vendor: <?= $product['vendor_name'] ?></p>
<p><?= $product['description'] ?></p>

<h3>Reviews:</h3>
<?php foreach($reviews as $r): ?>
    <div class="review">
        <strong><?= $r['username'] ?>:</strong> <?= $r['review'] ?><br>
        <?php if($r['reply']) echo "<em>Vendor reply: ".$r['reply']."</em>"; ?>
    </div>
<?php endforeach; ?>

<?php if(isset($_SESSION['userid']) && $_SESSION['role']=='user'): ?>
    <form action="add_review.php" method="POST">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <textarea name="review" placeholder="Write your review..." required></textarea>
        <button type="submit">Submit Review</button>
    </form>
<?php else: ?>
    <a href="login.php">Login to review</a>
<?php endif; ?>
