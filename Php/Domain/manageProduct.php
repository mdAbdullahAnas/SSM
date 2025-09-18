<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only admin/vendor can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','vendor'])) {
    header("Location: ../Auth/login.php");
    exit;
}

// Fetch all products
if($_SESSION['role'] === 'vendor'){
    // Vendor sees only their products
    $vendor_id = $_SESSION['userid'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE vendor_id=? ORDER BY created_at DESC");
    $stmt->bind_param("i",$vendor_id);
} else {
    // Admin sees all products
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
}
$stmt->execute();
$res = $stmt->get_result();
$products = $res->fetch_all(MYSQLI_ASSOC);

if (isset($_SESSION['role'])) {
     
    if ($_SESSION['role'] === 'vendor') {
        include("../DomainVendor/navbar.php");
    } elseif ($_SESSION['role'] === 'admin') {
        include("../DomainAdmin/navbar.php");
    }
} 
?>

<link rel="stylesheet" href="/SSM/Asset/Css/style.css">

<div class="main-content">
    <h2>Manage Products</h2>

    <a href="add_product.php" class="btn">Add New Product</a>
    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price ($)</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Description</th>
                <th>Review</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['price'],2) ?></td>
                        <td><?= $p['quantity'] ?></td>
                        <td><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="60"></td>
                        <td><?= htmlspecialchars($p['description']) ?></td>
                        <td><?= $p['review'] ?? 'N/A' ?></td>
                        <td><?= $p['created_at'] ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn">Edit</a>
                            <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
