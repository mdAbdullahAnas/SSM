<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','vendor'])){
    header("Location: ../Auth/login.php");
    exit;
}

if(isset($_GET['id'])){
    $product_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])){
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, quantity=?, description=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $quantity, $description, $id);
    $stmt->execute();

    header("Location: ../Product/menu.php");
    exit;
}
?>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/manage.css">

<form method="POST">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>"><br>
    Quantity: <input type="number" name="quantity" value="<?= $product['quantity'] ?>"><br>
    Description: <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>
    <button type="submit" name="update_product">Update Product</button>
</form>
