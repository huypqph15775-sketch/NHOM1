<?php
include("includes/database.php");

if(isset($_POST['cash'])){
    $order_id = $_POST['order_id'];
    $update_order = "update customer_orders set payment_type = 'Thanh toán tiền mặt khi nhận hàng' where order_id='$order_id'";
    $run_update = mysqli_query($conn, $update_order);
    if($run_update){
        echo "
        <div class='row justify-content-center mt-3'>
            <div class='col-lg-7 col-12'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Bạn đã chọn <strong>Thanh toán tiền mặt khi nhận hàng. </strong>Chọn <a class='text-primary' href='my_orders.php?pending_orders'>lịch sử đơn hàng</a> để xem.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            </div>
        </div>
        ";
    }
}

else if(isset($_POST['card'])){
    $order_id = $_POST['order_id'];
    $update_order = "update customer_orders set payment_type = 'Cà thẻ khi nhận hàng' where order_id='$order_id'";
    $run_update = mysqli_query($conn, $update_order);
    if($run_update){
        echo "
        <div class='row justify-content-center mt-3'>
            <div class='col-lg-7 col-12'>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Bạn đã chọn <strong>Cà thẻ khi nhận hàng. </strong>Chọn <a class='text-primary' href='my_orders.php?pending_orders'>lịch sử đơn hàng</a> để xem.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            </div>
        </div>
        ";
    }
}
?>