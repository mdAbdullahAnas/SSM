<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only vendor can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../Auth/login.php");
    exit;
}

$vendor_id = $_SESSION['userid'];

// Fetch all orders that contain at least one product
$stmt = $conn->prepare("
    SELECT DISTINCT o.* 
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$res = $stmt->get_result();
$orders = $res->fetch_all(MYSQLI_ASSOC);

include("../DomainVendor/navbar.php");
?>

<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">

<div class="main-content">
    <h2>Vendor Orders</h2>

    <?php if(empty($orders)): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['customer_id'] ?></td>
                    <td>
                        <?php
                        // Fetch all items for this order
                        $stmtItems = $conn->prepare("
                            SELECT oi.*, p.name, p.vendor_id 
                            FROM order_items oi
                            JOIN products p ON oi.product_id = p.id
                            WHERE oi.order_id=?
                        ");
                        $stmtItems->bind_param("i", $order['id']);
                        $stmtItems->execute();
                        $resItems = $stmtItems->get_result();

                        $itemsList = [];
                        while($item = $resItems->fetch_assoc()){
                            $prefix = ($item['vendor_id'] == $vendor_id) ? "(Your product) " : "";
                            $itemsList[] = $prefix . $item['name'] . " x " . $item['quantity'];
                        }

                        echo implode(", ", $itemsList);
                        ?>
                    </td>
                    <td>$<?= number_format($order['total'], 2) ?></td>
                    <td><?= $order['status'] ?></td>
                    <td>
                        <?php if($order['status'] !== 'Delivered'): ?>
                            <form method="POST" action="updateOrderStatus.php">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" name="mark_delivered" class="btn">Mark as Delivered</button>
                            </form>
                        <?php else: ?>
                            <span style="color:green;">Delivered</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
