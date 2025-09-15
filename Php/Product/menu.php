<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Fetch all products with vendor name
$result = $conn->query("SELECT p.*, v.fullname as vendor_name 
                        FROM products p 
                        JOIN vendors v ON p.vendor_id=v.id 
                        ORDER BY p.name ASC");

$products = [];
if($result){
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
}

// Include navbar based on role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'customer') {
        include("../DomainCustomer/navbar.php");
    } elseif ($_SESSION['role'] === 'vendor') {
        include("../DomainVendor/navbar.php");
    } elseif ($_SESSION['role'] === 'admin') {
        include("../DomainAdmin/navbar.php");
    }
} 
?>

<link rel="stylesheet" href="/SSM/Asset/Css/layout.css">
<link rel="stylesheet" href="/SSM/Asset/Css/menu.css">

<div class="main-content">
    <h1>Our Products</h1>
    <div class="product-grid">
        <?php foreach($products as $p): ?>
            <div class="product-card">
                <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                <h3><?= $p['name'] ?></h3>
                <p class="price">$<?= number_format($p['price'],2) ?></p>
                <p>Available: <?= $p['quantity'] ?></p>
                <p>Vendor: <?= $p['vendor_name'] ?></p>

                <?php if(!isset($_SESSION['role'])): ?>
                    <a href="/SSM/Php/Auth/login.php" class="btn">Login to Buy</a>

                <?php elseif($_SESSION['role'] === 'customer'): ?>
                    <form action="/SSM/Php/DomainCustomer/addToCart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                    </form>

                    <form action="/SSM/Php/DomainCustomer/makePayment.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="buy_now" class="btn">Buy Now</button>
                    </form>

                <?php else: ?>
                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn">Edit</a>
                    <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
