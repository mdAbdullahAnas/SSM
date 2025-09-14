<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only vendor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../Auth/login.php");
    exit;
}

$vendor_id = $_SESSION['userid'];

// Get order IDs where vendor's products were sold
$stmt = $conn->prepare("
    SELECT DISTINCT o.* 
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.vendor_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i",$vendor_id);
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);

include("../DomainVendor/navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">

<div class="main-content">
    <h2>Vendor Orders</h2>

    <?php foreach($orders as $order): ?>
        <div class="order-card">
            <h3>Order #<?= $order['id'] ?> | <?= $order['status'] ?> | <?= $order['created_at'] ?></h3>
            <p><b>Customer ID:</b> <?= $order['customer_id'] ?></p>
            <p><b>Total:</b> $<?= number_format($order['total'],2) ?></p>
            <h4>Items (your products):</h4>
            <ul>
                <?php
                $stmtItem = $conn->prepare("
                    SELECT oi.*, p.name 
                    FROM order_items oi
                    JOIN products p ON oi.product_id=p.id
                    WHERE order_id=? AND p.vendor_id=?
                ");
                $stmtItem->bind_param("ii",$order['id'],$vendor_id);
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
</div>
