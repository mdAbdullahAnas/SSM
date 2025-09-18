<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only allow admin/vendor
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','vendor'])){
    header("Location: ../Auth/login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])){
    $product_id = intval($_POST['id']);

    if($_SESSION['role'] === 'vendor'){
        // Vendor can delete only their own products
        $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND vendor_id=?");
        $stmt->bind_param("ii", $product_id, $_SESSION['userid']);
    } else {
        // Admin can delete any product
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $product_id);
    }

    $stmt->execute();
}

header("Location: ../Product/menu.php");
exit;
?>
