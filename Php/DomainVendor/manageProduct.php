<?php
session_start();
include("../../Connection/db.php");

// Only vendor access
if(!isset($_SESSION['userid']) || $_SESSION['role'] !== 'vendor'){
    header("Location: ../Auth/login.php");
    exit();
}

// ✅ Get numeric vendor ID from username stored in session
$vendor_username = $_SESSION['userid'];
$stmt = $conn->prepare("SELECT id FROM vendors WHERE username=?");
$stmt->bind_param("s", $vendor_username);
$stmt->execute();
$result = $stmt->get_result();
if($row = $result->fetch_assoc()){
    $vendor_id = $row['id']; // numeric ID for foreign key
} else {
    die("Vendor not found!");
}

$success = $error = "";

// --- Handle Add Product ---
if(isset($_POST['add_product'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    if(isset($_FILES['img']) && $_FILES['img']['error']===0){
        $allowedTypes = ['jpg','jpeg','png','gif','webp'];
        $fileTmpPath = $_FILES['img']['tmp_name'];
        $fileName = $_FILES['img']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(in_array($fileExt, $allowedTypes)){
            $safeName = preg_replace("/[^a-zA-Z0-9_-]/", "_", $name);
            $newFileName = $safeName."_".time().".".$fileExt;
            $uploadPath = $_SERVER['DOCUMENT_ROOT']."/SSM/Asset/Images/".$newFileName;

            if(move_uploaded_file($fileTmpPath, $uploadPath)){
                $imgPath = "../../Asset/Images/".$newFileName;

                $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, img, vendor_id, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sdisis",$name,$price,$quantity,$imgPath,$vendor_id,$description);

                if($stmt->execute()){
                    $success = "Product added successfully!";
                } else {
                    $error = "Database error: " . $stmt->error;
                }
            } else {
                $error = "Failed to upload image!";
            }
        } else {
            $error = "Invalid file type! Allowed: jpg, jpeg, png, gif, webp";
        }
    } else {
        $error = "No image selected!";
    }
}

// --- Handle Delete Product ---
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);

    $res = $conn->prepare("SELECT img FROM products WHERE id=? AND vendor_id=?");
    $res->bind_param("ii",$delete_id,$vendor_id);
    $res->execute();
    $result = $res->get_result();
    if($result && $result->num_rows){
        $row = $result->fetch_assoc();
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/SSM/".$row['img'])){
            unlink($_SERVER['DOCUMENT_ROOT']."/SSM/".$row['img']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND vendor_id=?");
    $stmt->bind_param("ii",$delete_id,$vendor_id);
    if($stmt->execute()){
        $success = "Product deleted successfully!";
    } else {
        $error = "Error deleting product: " . $stmt->error;
    }
}

// --- Fetch Vendor Products ---
$products = [];
$sql = "SELECT p.*, v.fullname AS vendor_name 
        FROM products p
        JOIN vendors v ON p.vendor_id = v.id
        WHERE p.vendor_id = ?
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vendor Manage Products</title>
<link rel="stylesheet" href="../../Asset/Css/Domain/manage.css">
</head>
<body>
<?php include("navbar.php"); ?>

<main class="vendor-main">
<h2>Manage Products</h2>

<?php if($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>

<!-- Add Product Form -->
<form method="POST" enctype="multipart/form-data">
<h3>Add Product</h3>
<input type="text" name="name" placeholder="Product Name" required><br>
<input type="number" step="0.01" name="price" placeholder="Price" required><br>
<input type="number" name="quantity" placeholder="Quantity" required><br>
<textarea name="description" placeholder="Description"></textarea><br>
<input type="file" name="img" accept="image/*" required><br>
<button type="submit" name="add_product">Add Product</button>
</form>

<!-- Product List -->
<h3>Your Products</h3>
<table border="1" cellpadding="5">
<thead>
<tr>
<th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Qty</th><th>Description</th><th>Vendor</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php if($products): foreach($products as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><img src="<?= $p['img'] ?>" width="80" alt="<?= $p['name'] ?>"></td>
<td><?= $p['name'] ?></td>
<td>$<?= $p['price'] ?></td>
<td><?= $p['quantity'] ?></td>
<td><?= $p['description'] ?></td>
<td><?= $p['vendor_name'] ?></td>

<td>
<a href="edit_product.php?id=<?= $p['id'] ?>" class="btn">Edit</a>
<a href="?delete_id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="8">No products found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</main>
</body>
</html>
