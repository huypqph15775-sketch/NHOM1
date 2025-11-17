        <!-- <h5 class="fw-bold">Đơn hàng đã mua gần đây</h5>  
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Đây là danh sách đơn hàng bạn đã mua từ ngày <strong></strong> tới <strong></strong> </span>
                </div>   

         -->
         <table class="table">
                    <tr>
                        <th>Đơn hàng</th>
                        <th>Sản phẩm</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt mua</th>
                        <th>Trạng thái</th>
                        <th>Ngày giao</th>
                    </tr>
                    <?php
                        // $date_start = date("Y/m/d", strtotime("-1 Year +1 day"));
                        // $date_end = date("Y/m/d", strtotime("+1 day"));
                        if(isset($_SESSION['customer_id'])){
                            $customer_id = $_SESSION['customer_id'];
                            $select_customer_orders = "select * from customer_orders where customer_id = '$customer_id' and status='Đã giao' ORDER BY order_id DESC";
                            $run_customer_orders = mysqli_query($conn, $select_customer_orders);
                            while($row_customer_orders = mysqli_fetch_array($run_customer_orders)){
                                $order_id = $row_customer_orders['order_id'];
                                $order_date = strtotime($row_customer_orders['order_date']);
                                $order_date_format = date("d/m/Y", $order_date);
                                $received_date = strtotime($row_customer_orders['received_date']);
                                $received_date_format = date("d/m/Y", $received_date);
                                $status = $row_customer_orders['status'];
                                $total_price = $row_customer_orders['total_price'];
                                $total_price_format = currency_format($total_price);
                                $select_customer_order_products = "select * from customer_order_products where order_id='$order_id'";
                                $run_customer_order_products = mysqli_query($conn, $select_customer_order_products);
                                $count_customer_order_products = mysqli_num_rows($run_customer_order_products);
                                echo "
                                <tr class='bg-white'>
                                    <td class='text-primary'>#$order_id<p class='text-secondary mt-3'>($count_customer_order_products sản phẩm)</p></td>
                                    <td>
                                ";
                                while($row_customer_order_products = mysqli_fetch_array($run_customer_order_products)){
                                    $product_id = $row_customer_order_products['product_id'];
                                    $color = $row_customer_order_products['color'];
                                    $get_color = "select * from product_color where product_color_name = '$color'";
                                    $run_color = mysqli_query($conn, $get_color);
                                    $row_color = mysqli_fetch_array($run_color);
                                    $product_color_id = $row_color['product_color_id'];
                                    $quantity = $row_customer_order_products['quantity'];
                                    $select_product = "select * from products where product_id = '$product_id'";
                                    $run_product = mysqli_query($conn, $select_product);
                                    $row_product = mysqli_fetch_array($run_product);
                                    $product_name = $row_product['product_name'];
                                    $select_product_img = "select * from product_img where product_id = '$product_id' and product_color_id = '$product_color_id'";
                                    $run_product_img = mysqli_query($conn, $select_product_img);
                                    $row_product_img = mysqli_fetch_array($run_product_img);
                                        $product_img = $row_product_img['product_color_img'];
                                        $product_price_des = $row_product_img['product_price_des'];
                                        $product_price_des_format = currency_format($product_price_des);
                                        $product_price = $row_product_img['product_price'];
                                        $product_price_format = currency_format($product_price);
                                    echo "
                                        <div class='row mb-4'>
                                            <!-- product img -->
                                            <div class='col-md-2 col-11 mx-auto justify-content-center product-img'>
                                                <a href='../shop-detail.php?product_id=$product_id&color=$product_color_id' target='_blank'><img src='../administrator/product_img/$product_img' width='200px' class='img-fluid' alt=''></a>
                                            </div>
                                            <div class='col-md-10 col-11 mx-auto ps-4'>
                                                <a href='../shop-detail.php?product_id=$product_id&color=$product_color_id' target='_blank' class='mb-4 fw-bold text-decoration-none product-name'>
                                                    $product_name
                                                </a>
                                                <span class='mb-2 d-block color'>Màu: $color</span>
                                            </div>
                                        </div>
                                        
                                    ";
                                }
                                echo "
                                        </td>
                                        <td class='text-danger'>$total_price_format</td>
                                        <td>$order_date_format</td>
                                        <td class='text-success'>$status</td>
                                        <td>$received_date_format</td>
                                        <td><a href='order_detail.php?order_id=$order_id' class='btn btn-info text-white'>Xem chi tiết</a></td>
                                    </tr>
                                ";
                            }
                        }
                    ?>
                    
                </table> 
