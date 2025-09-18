<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

// ✅ Fix: get correct customer id
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

// Fetch all orders of this customer
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/style.css">

 

<div class="main-content">
    <h2>My Orders</h2>

    <?php if(empty($orders)): ?>
        <p>You have not purchased anything yet.</p>
    <?php else: ?>
        <?php foreach($orders as $order): ?>
            <div class="order-card">
                <h3>Order #<?= $order['id'] ?> | 
                    <span style="color:green;">Status: <?= htmlspecialchars($order['status']) ?></span>
                </h3>
                <p><b>Total:</b> $<?= number_format($order['total'],2) ?> | 
                   <b>Discount:</b> $<?= number_format($order['discount'],2) ?></p>
                <p><b>Address:</b> <?= htmlspecialchars($order['address']) ?></p>
                <p><b>Contact:</b> <?= htmlspecialchars($order['contact_number']) ?></p>
                <p><b>Payment:</b> <?= htmlspecialchars($order['payment_method']) ?></p>
                <p><b>Created:</b> <?= $order['created_at'] ?></p>

                <h4>Items:</h4>
                <ul>
                    <?php
                    $stmtItem = $conn->prepare("
                        SELECT oi.*, p.name 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id=p.id 
                        WHERE order_id=?
                    ");
                    $stmtItem->bind_param("i", $order['id']);
                    $stmtItem->execute();
                    $resItem = $stmtItem->get_result();
                    while($item = $resItem->fetch_assoc()):
                    ?>
                        <li><?= htmlspecialchars($item['name']) ?> - <?= $item['quantity'] ?> × $<?= number_format($item['price'],2) ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
