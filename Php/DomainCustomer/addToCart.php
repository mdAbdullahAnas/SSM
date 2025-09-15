<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customer can add to cart
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer'){
    header("Location: ../Auth/login.php");
    exit;
}

if(isset($_POST['add_to_cart']) && isset($_POST['product_id'])){
    $product_id = (int) $_POST['product_id'];

    // Get product from database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i",$product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
        die("Product not found.");
    }

    $product = $res->fetch_assoc();

    // Initialize cart if not exists
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Add product to cart
    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1
        ];
    }

    $_SESSION['success'] = "$product[name] added to cart!";
    header("Location: ../Product/menu.php"); // redirect back
    exit;
}
?>
