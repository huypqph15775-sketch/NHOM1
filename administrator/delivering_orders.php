
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?pending_orders">Đơn hàng</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Đơn hàng đang giao</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <p>
                    <a href="index.php?pending_orders" class = "btn btn-primary text-white me-3">Chờ xác nhận</a>
                    <a href="" class = "btn btn-secondary text-white me-3">Đang giao</a>
                    <a href="index.php?delivered_orders" class = "btn btn-primary text-white me-3">Đã giao</a>

                </p>
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">Đơn hàng</th>
                    <th scope="col">Tên khách hàng</th>
                    <th scope="col">SĐT</th>
                    <th scope="col">Ngày</th>
                    <th scope="col">Tổng tiền</th>
                    <th scope="col"></th>
                </tr>
                <?php
                    $get_pending_orders = "select * from customer_orders where status = 'Đang giao'";
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
                </tr>
                <?php
                    }
                ?>
                </table>
            </div>




