<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

$customer_id = $_SESSION['userid'];

// Fetch products purchased by customer
$stmt = $conn->prepare("SELECT DISTINCT p.id, p.name FROM products p 
                        JOIN order_items oi ON p.id = oi.product_id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE o.customer_id=?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$products = $stmt->get_result();
?>

<?php include("navbar.php"); ?>

<div class="main-content">
    <h2>Write Product Review</h2>
    <?php if ($products->num_rows > 0): ?>
        <form method="POST" action="submitReview.php">
            <label>Select Product:</label>
            <select name="product_id" required>
                <?php while($p = $products->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <label>Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>
            <br><br>
            <label>Review:</label><br>
            <textarea name="review_text" rows="4" cols="50" required></textarea>
            <br><br>
            <button type="submit" class="btn">Submit Review</button>
        </form>
    <?php else: ?>
        <p>You can only review products you have purchased. <a href="../Product/menu.php">Shop Now</a></p>
    <?php endif; ?>
</div>
