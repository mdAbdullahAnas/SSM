<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['userid'];
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review_text'];

    // Ensure customer purchased this product
    $stmtCheck = $conn->prepare("SELECT COUNT(*) as cnt FROM orders o 
                                 JOIN order_items oi ON o.id=oi.order_id
                                 WHERE o.customer_id=? AND oi.product_id=?");
    $stmtCheck->bind_param("ii", $customer_id, $product_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result()->fetch_assoc();

    if ($result['cnt'] > 0) {
        $stmt = $conn->prepare("INSERT INTO product_reviews (customer_id, product_id, rating, review_text, created_at)
                                VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $customer_id, $product_id, $rating, $review_text);
        $stmt->execute();
        $_SESSION['success'] = "Review submitted successfully!";
    } else {
        $_SESSION['error'] = "You can only review products you purchased.";
    }
    header("Location: writeProductReview.php");
    exit;
}
?>
