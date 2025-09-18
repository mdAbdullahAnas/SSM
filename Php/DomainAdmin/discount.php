<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// ---------------------- GENERATE COUPON ----------------------
function generateCoupon($length = 8) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for($i=0; $i<$length; $i++){
        $code .= $chars[rand(0, strlen($chars)-1)];
    }
    return $code;
}

// Handle coupon creation
if(isset($_POST['generate_coupon'])){
    $coupon_code = generateCoupon();
    $discount_type = $_POST['discount_type']; // 'percent' or 'fixed'
    $discount_value = floatval($_POST['discount_value']);

    $stmt = $conn->prepare("INSERT INTO coupons (code, type, value, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssd", $coupon_code, $discount_type, $discount_value);
    $stmt->execute();

    $_SESSION['success'] = "✅ Coupon generated: $coupon_code";
    header("Location: discount.php");
    exit;
}

// Fetch all coupons
$result = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Global Coupons</title>
<link rel="stylesheet" href="/SSM/Asset/Css/Domain/admin.css">
</head>
<body>

<h2>Global Coupons</h2>

<?php 
if(isset($_SESSION['success'])) { echo "<p class='success'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
if(isset($_SESSION['error'])) { echo "<p class='error'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); }
?>

<div class="coupon-form-container">
    <form method="POST" class="coupon-form">
        <input type="text" name="coupon_code" value="" placeholder="Auto generated" disabled>
        <select name="discount_type" required>
            <option value="percent">Percentage %</option>
            <option value="fixed">Fixed Amount $</option>
        </select>
        <input type="number" name="discount_value" placeholder="Discount value" required>
        <button type="submit" name="generate_coupon">Generate Coupon</button>
    </form>
</div>

<h3>Existing Coupons</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Type</th>
        <th>Value</th>
        <th>Created At</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['code']) ?></td>
        <td><?= ucfirst($row['type']) ?></td>
        <td><?= htmlspecialchars($row['value']) ?><?= $row['type']=='percent'?'%':'$' ?></td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
