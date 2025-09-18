<?php
session_start();
include($_SERVER['DOCUMENT_ROOT']."/SSM/Connection/db.php");

$total = floatval($_POST['total'] ?? 0);
$coupon_code = trim($_POST['coupon_code'] ?? '');

$response = ['success'=>false, 'message'=>'', 'total'=>$total];

if(!empty($coupon_code)){
    $stmtC = $conn->prepare("SELECT * FROM coupons WHERE code=?");
    $stmtC->bind_param("s", $coupon_code);
    $stmtC->execute();
    $resC = $stmtC->get_result();
    if($resC->num_rows>0){
        $c = $resC->fetch_assoc();
        if($c['type'] === 'percent'){
            $discount = ($total * $c['value'])/100;
        } else {
            $discount = $c['value'];
        }
        $total_after_discount = max(0, $total - $discount);

        // Save coupon in session
        $_SESSION['coupon_code'] = $coupon_code;
        $_SESSION['coupon_discount'] = $discount;

        $response['success'] = true;
        $response['message'] = "✅ Coupon applied! Discount: $".number_format($discount,2)." New total: $".number_format($total_after_discount,2);
        $response['total'] = number_format($total_after_discount,2);
    } else {
        $response['message'] = "❌ Invalid coupon code!";
    }
} else {
    $response['message'] = "❌ Please enter a coupon code.";
}

echo json_encode($response);
