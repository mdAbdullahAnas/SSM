<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor'){
    header("Location: ../Auth/login.php");
    exit;
}

if(isset($_POST['mark_delivered']) && isset($_POST['order_id'])){
    $order_id = $_POST['order_id'];

    // Update the order status
    $stmt = $conn->prepare("UPDATE orders SET status='Delivered' WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    $_SESSION['success'] = "Order #$order_id marked as delivered.";
    header("Location: vendorOrders.php");
    exit;
}
?>
