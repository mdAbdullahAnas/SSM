<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

$customer_id = $_SESSION['userid'];

// Fetch orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/cartPayment.css">

<div class="main-content">
    <h2>My Orders</h2>
    <?php if(empty($orders)): ?>
        <p>You have no orders yet. <a href="../Product/menu.php">Shop Now</a></p>
    <?php else: ?>
        <?php foreach($orders as $order): ?>
            <div class="order-card">
                <h3>Order #<?= $order['id'] ?> | <?= $order['status'] ?> | <?= $order['created_at'] ?></h3>
                <p><b>Total:</b> $<?= number_format($order['total'],2) ?> | <b>Discount:</b> $<?= number_format($order['discount'],2) ?></p>
                <p><b>Address:</b> <?= htmlspecialchars($order['address']) ?></p>
                <p><b>Contact:</b> <?= htmlspecialchars($order['contact_number']) ?></p>
                <p><b>Payment Method:</b> <?= htmlspecialchars($order['payment_method']) ?></p>

                <h4>Items:</h4>
                <ul>
                    <?php
                    $stmtItem = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                    $stmtItem->bind_param("i", $order['id']);
                    $stmtItem->execute();
                    $resItem = $stmtItem->get_result();
                    while($item = $resItem->fetch_assoc()):
                    ?>
                        <li><?= htmlspecialchars($item['name']) ?> - <?= $item['quantity'] ?> x $<?= number_format($item['price'],2) ?></li>
                    <?php endwhile; ?>
                </ul>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
