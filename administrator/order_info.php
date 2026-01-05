
<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['order_info'])){
        $order_id = $_GET['order_info'];
        $get_order = "select * from customer_orders where order_id = '$order_id'";
        $run_get_order = mysqli_query($conn, $get_order);
        $row_get_order = mysqli_fetch_array($run_get_order);
        $total_price = $row_get_order['total_price'];
        $total_price_format = currency_format($total_price);
        $order_date = $row_get_order['order_date'];
        $status = $row_get_order['status'];
        $reciver = $row_get_order['receiver'];
        $receiver_sex = $row_get_order['receiver_sex'];
        $receiver_phone = $row_get_order['receiver_phone'];
        $delivery_location = $row_get_order['delivery_location'];
        $payment_type = $row_get_order['payment_type'];
        $tracking_code = isset($row_get_order['tracking_code']) ? $row_get_order['tracking_code'] : '';
    }
}
// Handle save tracking from admin
if(isset($_POST['save_tracking'])){
    $order_id_post = intval($_POST['order_id']);
    $code = mysqli_real_escape_string($conn, $_POST['tracking_code']);
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `customer_orders` LIKE 'tracking_code'");
    if(mysqli_num_rows($check) == 0){
        @mysqli_query($conn, "ALTER TABLE `customer_orders` ADD COLUMN `tracking_code` VARCHAR(255) DEFAULT NULL AFTER status");
    }
    $update = "UPDATE customer_orders SET tracking_code = '$code' WHERE order_id = '$order_id_post'";
    if(mysqli_query($conn, $update)){
        $get_customer = "select customer_id from customer_orders where order_id = '$order_id_post'";
        $run_get_customer = mysqli_query($conn, $get_customer);
        $row_c = mysqli_fetch_array($run_get_customer);
        $customer_id = $row_c['customer_id'];
        $msg = "Mã vận đơn cho đơn #$order_id_post: $code";
        $insert_notify = "insert into notifications (user_id, is_admin, type, title, message, related_id, is_read, created_at) values ('$customer_id', 0, 'tracking_update', 'Cập nhật mã vận đơn', '".mysqli_real_escape_string($conn,$msg)."', '$order_id_post', 0, NOW())";
        @mysqli_query($conn, $insert_notify);
        echo "<script>alert('Lưu mã vận đơn thành công'); window.location = 'index.php?order_info=$order_id_post';</script>";
        exit;
    } else {
        echo "<script>alert('Lỗi khi lưu mã vận đơn');</script>";
    }
}
?>

            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?<?php
                            if($status=="Đang chờ") echo "pending_orders";
                            if($status=="Đang giao") echo "delivering_orders";
                            if($status=="Đã giao") echo "delivered_orders";
                          ?>">Đơn hàng</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <p class="fw-bold fs-5">Đơn hàng #<?= $order_id ?></p>
                <div class="mb-2">
                    <a href="../print_order.php?order_id=<?= $order_id; ?>&type=invoice" target="_blank" class="btn btn-outline-success btn-sm me-2">In hoá đơn</a>
                    <a href="../print_order.php?order_id=<?= $order_id; ?>&type=delivery" target="_blank" class="btn btn-outline-secondary btn-sm">In phiếu giao hàng</a>
                </div>
                <p><b>Trạng thái: </b><span class="text-primary"><?= $status; ?></span></p>
                <p><b>Tổng tiền: </b><span class="text-danger"><?= $total_price_format; ?></span></p>
                <p><b>Hình thức thanh toán: </b><span class="text-success"><?= $payment_type; ?></span></p>

                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">Tên điện thoại</th>
                    <th scope="col">Ảnh</th>
                    <th scope="col">Màu</th>
                    <th scope="col">Giá</th>
                    <th scope="col">Giá sale</th>
                    <th scope="col">Số lượng</th>
                </tr>
                <?php
                    $get_order_product = "select * from customer_order_products where order_id = '$order_id'";
                    $run_order_product = mysqli_query($conn, $get_order_product);
                    while($row_order_product = mysqli_fetch_array($run_order_product)){
                        $product_id = $row_order_product['product_id'];
                        $get_product_name = "select * from products where product_id = '$product_id'";
                        $run_get_product_name = mysqli_query($conn, $get_product_name);
                        $row_get_product_name = mysqli_fetch_array($run_get_product_name);
                        $product_name = $row_get_product_name['product_name'];
                        $color = $row_order_product['color'];
                        $quantity = $row_order_product['quantity'];
                        $get_color = "select * from product_color where product_color_name='$color'";
                        $run_get_color = mysqli_query($conn, $get_color);
                        $row_get_color = mysqli_fetch_array($run_get_color);
                        $product_color_id = $row_get_color['product_color_id'];
                        $get_product = "select * from product_img where product_id = '$product_id' and product_color_id='$product_color_id'";
                        $run_get_product = mysqli_query($conn, $get_product);
                        $row_get_product = mysqli_fetch_array($run_get_product);
                        $product_price = $row_get_product['product_price'];
                        $product_price_format = currency_format($product_price);
                        $product_price_des = $row_get_product['product_price_des'];
                        $product_price_format_des = currency_format($product_price_des);
                        $product_color_img = $row_get_product['product_color_img'];
                ?>
                <tr>
                    <th scope="row"><?php echo $product_name; ?></th>
                    <td><img src="product_img/<?php echo $product_color_img ?>" alt="" style="max-width:100px" class="img-fluid"></td>
                    <td><?php echo $color ?></td>
                    <td class="text-secondary"><?php echo $product_price_format ?></td>
                    <td class="text-danger"><?php echo $product_price_format_des ?></td>
                    <td><?php echo $quantity ?></td>
                </tr>
                <?php
                    }
                ?>
                </table>
            <p class="fw-bold">Thông tin địa chỉ và người nhận hàng: </p>
            <span class="d-block">- Người nhận hàng: <?= $receiver_sex; ?> <?= $reciver; ?></span>
            <span class="d-block">- Số điện thoại: <?= $receiver_phone; ?></span>
            <span class="d-block">- Địa chỉ nhận: <?= $delivery_location; ?></span>
            
            <p class="fw-bold mt-3">Mã vận đơn:</p>
            <?php if(isset($_SESSION['admin_id'])): ?>
            <form method="post" class="mb-3">
                <div class="input-group" style="max-width:400px;">
                    <input type="text" name="tracking_code" class="form-control" value="<?= htmlspecialchars($tracking_code); ?>" placeholder="Nhập mã vận đơn">
                    <button type="submit" name="save_tracking" class="btn btn-primary">Lưu mã vận đơn</button>
                </div>
                <input type="hidden" name="order_id" value="<?= $order_id; ?>">
            </form>
            <?php else: ?>
                <p><?= htmlspecialchars($tracking_code); ?></p>
            <?php endif; ?>
            </div>

