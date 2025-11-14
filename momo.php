<?php
include("includes/database.php");
$order_id=$_GET['order_id'];
    if(isset($_GET['partnerCode'])){
        $codeoder=rand(0,9999);
        $partnerCode=$_GET['partnerCode'];
        $orderId=$_GET['orderId'];
        $amount=$_GET['amount'];
        $orderInfo=$_GET['orderInfo'];
        $orderType=$_GET['orderType'];
        $transId=$_GET['transId'];
        $payType=$_GET['payType'];
        $insert_momo = "INSERT INTO momo(partner_code, order_id, amount, order_info, order_type, trans_id, pay_type, code_cart) 
        VALUES ('".$partnerCode."','".$orderId."','".$amount."','".$orderInfo."', '".$orderType."', '".$transId."', '".$payType."','".$codeoder."')";
        $result = mysqli_query($conn, $insert_momo);
        echo $orderId;
        $update_order = "update customer_orders set payment_type = 'Đã thanh toán qua MOMO ATM' where order_id='$order_id'";
        $run_update = mysqli_query($conn, $update_order);
        header("location: index.php");
    }
?>