<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

// Cart empty?
if (empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty. <a href='../Product/menu.php'>Shop Now</a></h2>";
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

include("navbar.php");
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domai/Customer/cartPayment.css">

<div class="main-content">
    <h2>Your Cart</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        <?php foreach($_SESSION['cart'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'],2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['price'] * $item['quantity'],2) ?></td>
            <td>
                <a href="removeFromCart.php?id=<?= $item['id'] ?>" class="btn btn-danger">Remove</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="3">Total</th>
            <th colspan="2">$<?= number_format($total,2) ?></th>
        </tr>
    </table>
    <a href="makePayment.php" class="btn">Proceed to Payment</a>
</div>
