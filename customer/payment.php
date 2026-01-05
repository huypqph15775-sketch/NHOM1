<?php
include("includes/database.php");

// LẤYDỮ LIỆU VÀ TÍNH LẠI DISCOUNT TỪ DATABASE (VÀ SESSION AN TOÀN)
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    die("Lỗi: Bạn chưa đăng nhập!");
}

// Lấy order_id từ POST
$order_id = $_POST['order_id'] ?? null;
if (!$order_id) {
    die("Lỗi: Đơn hàng không tồn tại!");
}

// Lấy thông tin đơn hàng
$order_check = mysqli_query($conn, "SELECT * FROM customer_orders WHERE order_id = '$order_id' AND customer_id = '$customer_id' LIMIT 1");
if (!$order_check || mysqli_num_rows($order_check) == 0) {
    die("Lỗi: Đơn hàng không hợp lệ!");
}
$order_data = mysqli_fetch_assoc($order_check);
$total_price = (int)$order_data['total_price'];

// TÍNH LẠI DISCOUNT TỪ VOUCHER (KHÔNG TIN POST)
$discount_value = 0;
$total_after_discount = $total_price;
$voucher_code = "";

// Kiểm tra voucher từ SESSION (an toàn hơn POST)
if (isset($_SESSION['applied_voucher'])) {
    $voucher_code = trim($_SESSION['applied_voucher']);
    $voucher_code_esc = mysqli_real_escape_string($conn, $voucher_code);
    $today = date('Y-m-d');
    
    // Lấy thông tin voucher từ database
    $voucher_query = mysqli_query($conn, "
        SELECT * FROM vouchers 
        WHERE code = '$voucher_code_esc' 
        AND status = 'active' 
        AND (allowed_customer_id IS NULL OR allowed_customer_id = '$customer_id')
        AND quantity > 0 
        AND (start_date IS NULL OR start_date <= '$today') 
        AND (end_date IS NULL OR end_date >= '$today') 
        LIMIT 1
    ");
    
    if ($voucher_query && mysqli_num_rows($voucher_query) > 0) {
        $voucher = mysqli_fetch_assoc($voucher_query);
        $min_order = (int)$voucher['min_order'];
        $discount_percent = (int)$voucher['discount_percent'];
        $discount_amount = (int)$voucher['discount_amount'];
        $max_discount = (int)$voucher['max_discount'];
        
        // Kiểm tra đơn hàng đạt giá trị tối thiểu
        if ($total_price >= $min_order) {
            // Tính discount dựa trên loại
            if ($discount_percent > 0) {
                $discount_value = (int)floor($total_price * $discount_percent / 100);
                if ($max_discount > 0 && $discount_value > $max_discount) {
                    $discount_value = $max_discount;
                }
            } elseif ($discount_amount > 0) {
                $discount_value = $discount_amount;
            }
            
            // Không cho discount vượt quá total
            if ($discount_value > $total_price) {
                $discount_value = $total_price;
            }
            
            $total_after_discount = $total_price - $discount_value;
        } else {
            // Đơn hàng chưa đạt min_order, xóa voucher
            unset($_SESSION['applied_voucher']);
            unset($_SESSION['applied_voucher_id']);
            unset($_SESSION['applied_discount_value']);
            unset($_SESSION['applied_total_after']);
            $voucher_code = "";
            $discount_value = 0;
            $total_after_discount = $total_price;
        }
    } else {
        // Voucher không hợp lệ, xóa khỏi session
        unset($_SESSION['applied_voucher']);
        unset($_SESSION['applied_voucher_id']);
        unset($_SESSION['applied_discount_value']);
        unset($_SESSION['applied_total_after']);
        $voucher_code = "";
        $discount_value = 0;
        $total_after_discount = $total_price;
    }
}


// ==================== PHƯƠNG THỨC THANH TOÁN ====================

if(isset($_POST['cash'])){
    $order_id = $_POST['order_id'];

    // CẬP NHẬT VỚI GIÁ ĐÃ TÍNH LẠI TỪ SERVER
    $update_order = "
        UPDATE customer_orders 
        SET payment_type = 'Thanh toán tiền mặt khi nhận hàng',
            total_after_discount = '$total_after_discount',
            discount_value = '$discount_value',
            voucher_code = '$voucher_code'
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

    // CẬP NHẬT VỚI GIÁ ĐÃ TÍNH LẠI TỪ SERVER
    $update_order = "
        UPDATE customer_orders 
        SET payment_type = 'Cà thẻ khi nhận hàng',
            total_after_discount = '$total_after_discount',
            discount_value = '$discount_value',
            voucher_code = '$voucher_code'
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
