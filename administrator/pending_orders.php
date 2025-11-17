
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?pending_orders">Đơn hàng</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Đơn hàng chờ xác nhận</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <p>
                    <a href="" class = "btn btn-secondary text-white me-3">Chờ xác nhận</a>
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
                    <th scope="col" colspan=3></th>
                </tr>
                <?php
                    $get_pending_orders = "select * from customer_orders where status = 'Đang chờ'";
                    $run_pending_orders = mysqli_query($conn, $get_pending_orders);
                    while($row_pending_orders = mysqli_fetch_array($run_pending_orders)){
                        $order_id = $row_pending_orders['order_id'];
                        $customer_id = $row_pending_orders['customer_id'];
                        $order_date = $row_pending_orders['order_date'];
                        $total_price = $row_pending_orders['total_price'];
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
                        <td class="text-center"><button name="submit_order" class="btn btn-primary text-white">Xác nhận</button></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown">Hủy</button>
                                <div class="dropdown-menu">
                                    <button onclick="unsubmit_uncorrect(<?= $order_id; ?>)" class="dropdown-item" id="uncorrectinfo<?= $order_id ?>">Thông tin liên hệ không chính xác</button>
                                    <button onclick="unsubmit_stopsell(<?= $order_id; ?>)" class="dropdown-item" id="stopsell<?= $order_id ?>">Có sản phẩm đã ngừng bán</button>
                                    <button onclick="unsubmit_outofstock(<?= $order_id; ?>)" class="dropdown-item" id="outofstock<?= $order_id ?>">Có sản phẩm đã hết hàng</button>
                                    <button onclick="unsubmit_notenough(<?= $order_id; ?>)" class="dropdown-item" id="notenough<?= $order_id ?>">Có sản phẩm không đủ số lượng yêu cầu</button>
                                </div>
                            </div>
                        </td>
                        <input type="hidden" name="order_id" value="<?= $order_id; ?>">
                    </form>
                </tr>
                <?php
                    }
                ?>
<!-- <button onclick="del_Product(<?php echo $product_id; ?>)" class="btn btn-danger text-white">Hủy</button> -->
            <script>
            function unsubmit_uncorrect(id){
                var result = confirm("Bạn có chắc chắn không xác nhận đơn hàng này? ");
                if(result==true){
                    document.getElementById("uncorrectinfo"+id).name = 'uncorrectinfo';
                }
            }
            function unsubmit_stopsell(id){
                var result = confirm("Bạn có chắc chắn không xác nhận đơn hàng này? ");
                if(result==true){
                    document.getElementById("stopsell"+id).name = 'stopsell';
                }
            }
            function unsubmit_outofstock(id){
                var result = confirm("Bạn có chắc chắn không xác nhận đơn hàng này? ");
                if(result==true){
                    document.getElementById("outofstock"+id).name = 'outofstock';
                }
            }
            function unsubmit_notenough(id){
                var result = confirm("Bạn có chắc chắn không xác nhận đơn hàng này? ");
                if(result==true){
                    document.getElementById("notenough"+id).name = 'notenough';
                }
            }
            </script>
                </table>
            </div>

<?php
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
            $update_order = "update customer_orders set status='Đang giao' where order_id = '$order_id'";
            $run_update = mysqli_query($conn, $update_order);
            if($run_update){
                echo "<script>window.open('index.php?pending_orders', '_self')</script>";
            }
        }
        
    }

    if(isset($_POST['uncorrectinfo'])){
        $order_id = $_POST['order_id'];
        $update_order = "update customer_orders set status='Không được xác nhận (Lý do: Thông tin liên hệ không chính xác)' where order_id = '$order_id'";
        $run_update = mysqli_query($conn, $update_order);
        if($run_update){
            echo "<script>window.open('index.php?pending_orders', '_self')</script>";
        }
    }

    if(isset($_POST['stopsell'])){
        $order_id = $_POST['order_id'];
        $update_order = "update customer_orders set status='Không được xác nhận (Lý do: Có sản phẩm trong đơn hàng đã ngừng kinh doanh)' where order_id = '$order_id'";
        $run_update = mysqli_query($conn, $update_order);
        if($run_update){
            echo "<script>window.open('index.php?pending_orders', '_self')</script>";
        }
    }

    if(isset($_POST['outofstock'])){
        $order_id = $_POST['order_id'];
        $update_order = "update customer_orders set status='Không được xác nhận (Lý do: Có sản phẩm trong đơn hàng đã hết hàng)' where order_id = '$order_id'";
        $run_update = mysqli_query($conn, $update_order);
        if($run_update){
            echo "<script>window.open('index.php?pending_orders', '_self')</script>";
        }
    }

    if(isset($_POST['notenough'])){
        $order_id = $_POST['order_id'];
        $update_order = "update customer_orders set status='Không được xác nhận (Lý do: Có sản phẩm không đủ số lượng yêu cầu)' where order_id = '$order_id'";
        $run_update = mysqli_query($conn, $update_order);
        if($run_update){
            echo "<script>window.open('index.php?pending_orders', '_self')</script>";
        }
    }
?>


