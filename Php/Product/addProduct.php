<?php
session_start();
include("db.php");

// Only vendor can access
if(!isset($_SESSION['userid']) || $_SESSION['role'] != 'vendor'){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $vendor_id = $_SESSION['userid'];

    // Handle image upload
    $target_dir = "../../Asset/Images/";
    $file_name = time() . '_' . basename($_FILES["img"]["name"]); // unique name
    $target_file = $target_dir . $file_name;

    if(move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)){
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, img, vendor_id, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $quantity, $target_file, $vendor_id, $description]);
        $success = "Product added successfully!";
    } else {
        $error = "Failed to upload image!";
    }
}
?>

<h2>Add New Product</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required><br>
    <input type="number" step="0.01" name="price" placeholder="Price" required><br>
    <input type="number" name="quantity" placeholder="Quantity" required><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <input type="file" name="img" accept="image/*" required><br>
    <button type="submit" name="submit">Add Product</button>
</form>
