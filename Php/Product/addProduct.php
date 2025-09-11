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
    if(isset($_FILES['img']) && $_FILES['img']['error'] === 0){
        $allowedTypes = ['jpg','jpeg','png','gif','webp'];
        $fileTmpPath = $_FILES['img']['tmp_name'];
        $fileName = $_FILES['img']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($fileExtension, $allowedTypes)){
            // Safe file name based on product name + unique id
            $safeName = preg_replace("/[^a-zA-Z0-9_-]/", "_", $name);
            $newFileName = $safeName . "_" . time() . "." . $fileExtension;
            $uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/SSM/Asset/Images/" . $newFileName;

            if(move_uploaded_file($fileTmpPath, $uploadPath)){
                // Save relative path to DB
                $imgPath = "../../Asset/Images/" . $newFileName;

                $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, img, vendor_id, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sdisis", $name, $price, $quantity, $imgPath, $vendor_id, $description);
                $stmt->execute();

                $success = "Product added successfully!";
            } else {
                $error = "Failed to upload image!";
            }
        } else {
            $error = "Invalid file type! Allowed: jpg, jpeg, png, gif, webp";
        }
    } else {
        $error = "No image selected or error in upload!";
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
