<?php
include("includes/database.php");

// NHẬN DỮ LIỆU TỪ customer_cart.php
$total_after_discount = $_POST['total_after_discount'] ?? 0;
$voucher_code = $_POST['voucher_code'] ?? "";
$discount_value = $_POST['discount_value'] ?? 0;

// Vì bước thanh toán chỉ update payment_type nên ta phải
// giữ lại giá trị đã giảm từ bước trước
if(isset($_POST['order_id'])){
    $order_id = $_POST['order_id'];

    // Cập nhật lại total_after_discount và voucher nếu có
    $update_total = "
        UPDATE customer_orders SET
            total_after_discount = '$total_after_discount',
            discount_value = '$discount_value',
            voucher_code = '$voucher_code'
        WHERE order_id = '$order_id'
    ";
    mysqli_query($conn, $update_total);
}


// ==================== PHƯƠNG THỨC THANH TOÁN ====================

if(isset($_POST['cash'])){
    $order_id = $_POST['order_id'];

    $update_order = "
        UPDATE customer_orders 
        SET payment_type = 'Thanh toán tiền mặt khi nhận hàng'
        WHERE order_id='$order_id'
    ";
    $run_update = mysqli_query($conn, $update_order);

    if($run_update){
        echo "
        <div class='row justify-content-center mt-3'>
            <div class='col-lg-7 col-12'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Bạn đã chọn <strong>Thanh toán tiền mặt khi nhận hàng.</strong> 
                    Chọn <a class='text-primary' href='my_orders.php?pending_orders'>lịch sử đơn hàng</a> để xem.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            </div>
        </div>
        ";
    }
}

else if(isset($_POST['card'])){
    $order_id = $_POST['order_id'];

    $update_order = "
        UPDATE customer_orders 
        SET payment_type = 'Cà thẻ khi nhận hàng'
        WHERE order_id='$order_id'
    ";
    $run_update = mysqli_query($conn, $update_order);

    if($run_update){
        echo "
        <div class='row justify-content-center mt-3'>
            <div class='col-lg-7 col-12'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Bạn đã chọn <strong>Cà thẻ khi nhận hàng.</strong> 
                    Chọn <a class='text-primary' href='my_orders.php?pending_orders'>lịch sử đơn hàng</a> để xem.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            </div>
        </div>
        ";
    }
}
?>
