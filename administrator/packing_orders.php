
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?pending_orders">Đơn hàng</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Đơn chờ đóng gói</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <p>
                    <a href="index.php?pending_orders" class = "btn btn-primary text-white me-3">Chờ xác nhận</a>
                    <a href="" class = "btn btn-warning text-white me-3">Chờ đóng gói</a>
                    <a href="index.php?delivering_orders" class = "btn btn-primary text-white me-3">Đang giao</a>
                    <a href="index.php?delivered_orders" class = "btn btn-primary text-white me-3">Đã giao</a>

                </p>
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">Đơn hàng</th>
                    <th scope="col">Tên khách hàng</th>
                    <th scope="col">SĐT</th>
                    <th scope="col">Ngày</th>
                    <th scope="col">Tổng tiền</th>
                    <th scope="col" colspan=2></th>
                </tr>
                <?php
                    // show orders that are waiting for packing (e.g., paid via MoMo)
                    $get_packing_orders = "select * from customer_orders where status LIKE 'Đang chờ đóng gói%' ORDER BY order_id DESC";
                    $run_packing_orders = mysqli_query($conn, $get_packing_orders);
                    while($row_packing_orders = mysqli_fetch_array($run_packing_orders)){
                        $order_id = $row_packing_orders['order_id'];
                        $customer_id = $row_packing_orders['customer_id'];
                        $order_date = $row_packing_orders['order_date'];
                        $total_price = $row_packing_orders['total_price'];
                        $total_price_format = currency_format($total_price);
                        $get_customer = "select * from customer where customer_id = '$customer_id'";
                        $run_customer = mysqli_query($conn, $get_customer);
                        $row_customer = mysqli_fetch_array($run_customer);
                        $customer_name = $row_customer['customer_name'];
                        $customer_phone = $row_customer['customer_phone'];
                    
                ?>
                <tr>
                    <th scope="row">#<?php echo $order_id; ?></th>
                    <td><?php echo $customer_name ?></td>
                    <td><?php echo $customer_phone ?></td>
                    <td><?php echo $order_date ?></td>
                    <td class="text-danger"><?php echo $total_price_format ?></td>
                    <td class="text-center"><a href="index.php?order_info=<?php echo $order_id; ?>" class="btn btn-info text-white">Xem</td>
                    <form action="" method="post">
                        <td class="text-center"><button name="submit_order" class="btn btn-primary text-white">Xác nhận</button>
                        <input type="hidden" name="order_id" value="<?= $order_id; ?>"></td>
                    </form>
                </tr>
                <?php
                    }
                ?>
            <!-- no cancel actions for packing orders: admins should only confirm packing -->
                </table>
            </div>


<?php
    // Reuse the same POST handlers as pending_orders.php (they update status or insert notifications)
    if(isset($_POST['submit_order'])){
        $order_id = $_POST['order_id'];
        $select_order_product = "select * from customer_order_products where order_id = '$order_id'";
        $run_order_product = mysqli_query($conn, $select_order_product);
        $count = 0;
        $count1 = 0;
        $count2 = 0;
        while($row_order_product = mysqli_fetch_array($run_order_product)){
            $product_id = $row_order_product['product_id'];
            $color = $row_order_product['color'];
            $quantity_buy = $row_order_product['quantity'];
            $get_color = "select * from product_color where product_color_name = '$color'";
            $run_get_color = mysqli_query($conn, $get_color);
            $row_get_color = mysqli_fetch_array($run_get_color);
            $product_color_id = $row_get_color['product_color_id'];
            $select_quantity = "select * from product_img where product_id = '$product_id' and product_color_id = '$product_color_id'";
            $run_quantity = mysqli_query($conn, $select_quantity);
            $row_quantity = mysqli_fetch_array($run_quantity);
            $product_quantity = $row_quantity['product_quantity'];
            $product_status = $row_quantity['product_status'];
            if($product_status=="Ngừng bán"){
                $count2++;
            }
            else if($product_quantity == 0){
                $count1++;
            }
            else if($product_quantity < $quantity_buy){
                $count++;
            }
            else{
                $quantity_new = $product_quantity-$quantity_buy;
                $update_quantity = "update product_img set product_quantity = '$quantity_new' where product_id = '$product_id' and product_color_id = '$product_color_id'";
                $run_update_quantity = mysqli_query($conn, $update_quantity);
            }
        }
        if($count2!=0){
            echo "<script>alert('Có sản phẩm trong đơn hàng đã ngừng bán')</script>";
        }
        else if($count1!=0){
            echo "<script>alert('Có sản phẩm trong đơn hàng đã hết hàng')</script>";
        }
        else if($count!=0){
            echo "<script>alert('Có sản phẩm trong đơn hàng không đủ số lượng yêu cầu')</script>";
        }
        else{
            // confirm the order and move to delivering
            $update_order = "update customer_orders set status='Đang giao' where order_id = '$order_id'";
            $run_update = mysqli_query($conn, $update_order);
            if($run_update){
                // ensure tracking_code column exists
                $check = mysqli_query($conn, "SHOW COLUMNS FROM `customer_orders` LIKE 'tracking_code'");
                if(mysqli_num_rows($check) == 0){
                    @mysqli_query($conn, "ALTER TABLE `customer_orders` ADD COLUMN `tracking_code` VARCHAR(255) DEFAULT NULL AFTER status");
                }
                // generate a tracking code (auto)
                $tracking_code = 'VN'.time().$order_id;
                $tracking_code_sql = "UPDATE customer_orders SET tracking_code = '".mysqli_real_escape_string($conn, $tracking_code)."' WHERE order_id = '$order_id'";
                @mysqli_query($conn, $tracking_code_sql);
                // notify customer about shipment and tracking code
                $get_customer = "select customer_id from customer_orders where order_id = '$order_id'";
                $run_get_customer = mysqli_query($conn, $get_customer);
                $row_c = mysqli_fetch_array($run_get_customer);
                $customer_id = $row_c['customer_id'];
                $msg = "Đơn hàng #$order_id đã được đóng gói và đang giao. Mã vận đơn: $tracking_code";
                $msg_esc = mysqli_real_escape_string($conn, $msg);
                $insert_notify = "insert into notifications (user_id, is_admin, type, title, message, related_id, is_read, created_at) values ('$customer_id', 0, 'order_shipped', 'Đơn hàng đang giao', '$msg_esc', '$order_id', 0, NOW())";
                @mysqli_query($conn, $insert_notify);

                echo "<script>window.open('index.php?packing_orders', '_self')</script>";
            }
        }
        
    }

    // Note: cancellation actions are intentionally disabled for packing orders
?>
