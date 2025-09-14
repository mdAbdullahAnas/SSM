<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/login.php");
    exit;
}

// Handle status update
if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si",$status,$order_id);
    $stmt->execute();
    $_SESSION['success'] = "Order #$order_id updated.";
}

$result = $conn->query("SELECT o.*, u.fullname AS customer_name FROM orders o JOIN users u ON o.customer_id=u.id ORDER BY created_at DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);

include("../DomainAdmin/navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">

<div class="main-content">
    <h2>All Orders</h2>
    <?php if(isset($_SESSION['success'])){ echo "<p style='color:green'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); } ?>

    <?php foreach($orders as $order): ?>
        <div class="order-card">
            <h3>Order #<?= $order['id'] ?> | <?= $order['status'] ?> | <?= $order['created_at'] ?></h3>
            <p><b>Customer:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><b>Total:</b> $<?= number_format($order['total'],2) ?> | <b>Discount:</b> $<?= number_format($order['discount'],2) ?></p>
            <p><b>Address:</b> <?= htmlspecialchars($order['address']) ?></p>
            <p><b>Contact:</b> <?= htmlspecialchars($order['contact_number']) ?></p>
            <p><b>Payment:</b> <?= htmlspecialchars($order['payment_method']) ?></p>

            <h4>Items:</h4>
            <ul>
                <?php
                $stmtItem = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE order_id=?");
                $stmtItem->bind_param("i",$order['id']);
                $stmtItem->execute();
                $resItem = $stmtItem->get_result();
                while($item = $resItem->fetch_assoc()):
                ?>
                    <li><?= htmlspecialchars($item['name']) ?> - <?= $item['quantity'] ?> x $<?= number_format($item['price'],2) ?></li>
                <?php endwhile; ?>
            </ul>

            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status">
                    <option value="Pending" <?= $order['status']=='Pending'?'selected':'' ?>>Pending</option>
                    <option value="Processing" <?= $order['status']=='Processing'?'selected':'' ?>>Processing</option>
                    <option value="Shipped" <?= $order['status']=='Shipped'?'selected':'' ?>>Shipped</option>
                    <option value="Delivered" <?= $order['status']=='Delivered'?'selected':'' ?>>Delivered</option>
                    <option value="Cancelled" <?= $order['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn">Update Status</button>
            </form>
            <hr>
        </div>
    <?php endforeach; ?>
</div>
