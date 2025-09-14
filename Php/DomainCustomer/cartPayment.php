<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

// Only customer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../Auth/login.php");
    exit;
}

// Validate customer ID exists
$customer_id = $_SESSION['userid'] ?? 0;
$stmtCheck = $conn->prepare("SELECT id FROM users WHERE id=?");
$stmtCheck->bind_param("i", $customer_id);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
if($resCheck->num_rows == 0){
    die("Invalid customer ID. Cannot place order.");
}

// Cart must not be empty
if (empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty. <a href='../Product/menu.php'>Shop Now</a></h2>";
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Coupon code handling
$discount = 0;
if (isset($_POST['apply_coupon']) && !empty($_POST['coupon_code'])) {
    $coupon_code = trim($_POST['coupon_code']);
    $stmtCoupon = $conn->prepare("SELECT discount FROM coupons WHERE code=? AND status='active'");
    $stmtCoupon->bind_param("s", $coupon_code);
    $stmtCoupon->execute();
    $resCoupon = $stmtCoupon->get_result();
    if ($resCoupon->num_rows > 0) {
        $row = $resCoupon->fetch_assoc();
        $discount = ($row['discount']/100) * $total;
        $_SESSION['discount'] = $discount;
        $_SESSION['coupon_code'] = $coupon_code;
        $total -= $discount;
        $_SESSION['success'] = "Coupon applied! Discount: $".number_format($discount,2);
    } else {
        $_SESSION['error'] = "Invalid coupon code.";
    }
}

// Handle payment submission
if (isset($_POST['pay_now'])) {
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $payment_method = $_POST['payment_method'];

    $card_number = $_POST['card_number'] ?? null;
    $card_expiry = $_POST['card_expiry'] ?? null;
    $card_cvv = $_POST['card_cvv'] ?? null;
    $wallet_type = $_POST['wallet_type'] ?? null;
    $wallet_phone = $_POST['wallet_phone'] ?? null;
    $coupon_code = $_SESSION['coupon_code'] ?? null;
    $discount = $_SESSION['discount'] ?? 0;

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders 
        (customer_id,total,discount,coupon_code,address,contact_number,payment_method,card_number,card_expiry,card_cvv,wallet_type,wallet_phone,status,created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?, 'Pending', NOW())");
    $stmt->bind_param("idisssssssss", $customer_id,$total,$discount,$coupon_code,$address,$contact_number,$payment_method,$card_number,$card_expiry,$card_cvv,$wallet_type,$wallet_phone);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items & reduce stock
    foreach ($_SESSION['cart'] as $item) {
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
        $stmtItem->bind_param("iiid",$order_id,$item['id'],$item['quantity'],$item['price']);
        $stmtItem->execute();

        $updateStock = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id=?");
        $updateStock->bind_param("ii",$item['quantity'],$item['id']);
        $updateStock->execute();
    }

    unset($_SESSION['cart'], $_SESSION['discount'], $_SESSION['coupon_code']);
    $_SESSION['success'] = "Order #$order_id placed successfully!";
    header("Location: cart.php");
    exit;
}

include("navbar.php");
?>

<link rel="stylesheet" href="/SSM/Asset/Css/Domain/Customer/cartPayment.css">

<div class="main-content">
    <h2>Payment & Checkout</h2>

    <?php if(isset($_SESSION['success'])): ?>
        <p class="success"><?=$_SESSION['success']; unset($_SESSION['success']);?></p>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <p class="error"><?=$_SESSION['error']; unset($_SESSION['error']);?></p>
    <?php endif; ?>

    <p>Total Amount: <b>$<?= number_format($total,2) ?></b></p>

    <form method="POST" class="coupon-form">
        <input type="text" name="coupon_code" placeholder="Enter coupon code">
        <button type="submit" name="apply_coupon" class="btn">Apply Coupon</button>
    </form>

    <form method="POST" class="checkout-form">
        <h3>Address & Contact</h3>
        <input type="text" name="address" placeholder="Enter delivery address" required><br><br>
        <input type="text" name="contact_number" placeholder="Enter contact number" required><br><br>

        <h3>Payment Method</h3>
        <label><input type="radio" name="payment_method" value="Card" required> Credit/Debit Card / Bank</label><br>
        <div id="cardFields" class="payment-fields">
            <input type="text" name="card_number" placeholder="Card Number">
            <input type="text" name="card_expiry" placeholder="MM/YY">
            <input type="text" name="card_cvv" placeholder="CVV">
        </div>

        <label><input type="radio" name="payment_method" value="Mobile Wallet"> Bkash / Rocket / Nagad</label><br>
        <div id="walletFields" class="payment-fields">
            <select name="wallet_type">
                <option value="Bkash">Bkash</option>
                <option value="Rocket">Rocket</option>
                <option value="Nagad">Nagad</option>
            </select>
            <input type="text" name="wallet_phone" placeholder="Wallet phone number">
        </div>

        <label><input type="radio" name="payment_method" value="Cash on Delivery"> Cash on Delivery</label><br><br>

        <button type="submit" name="pay_now" class="btn">Confirm & Place Order</button>
    </form>
</div>

<script>
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
const cardFields = document.getElementById('cardFields');
const walletFields = document.getElementById('walletFields');
cardFields.style.display = 'none';
walletFields.style.display = 'none';

paymentRadios.forEach(radio => {
    radio.addEventListener('change', () => {
        if(radio.value === 'Card'){
            cardFields.style.display = 'block';
            walletFields.style.display = 'none';
        } else if(radio.value === 'Mobile Wallet'){
            cardFields.style.display = 'none';
            walletFields.style.display = 'block';
        } else {
            cardFields.style.display = 'none';
            walletFields.style.display = 'none';
        }
    });
});
</script>
