<section class="container">
                <div class="row mt-2 cart-top">
                  <div class="col-6">
                    <a href="../shop.php"><i class="fas fa-chevron-left me-2"></i>Mua thêm sản phẩm khác</a>
                  </div>
                  <p class="col-6">Giỏ hàng của bạn</p>
                </div>
                <div class="row gx-2">
                    <!-- left side -->
                    <div class="col-lg-6 col-12 mx-auto main-cart mb-lg-0 mb-5">
                      <?php
                        $total = 0;
                        if(isset($_SESSION['customer_id'])){
                          $customer_id = $_SESSION['customer_id'];
                          $select_cart = "select * from cart where customer_id = '$customer_id'";
                          $run_cart = mysqli_query($conn, $select_cart);
                          while($row_cart = mysqli_fetch_array($run_cart)){
                            $product_id = $row_cart['product_id'];
                            $color = $row_cart['color'];
                            $get_color = "select * from product_color where product_color_name = '$color'";
                            $run_color = mysqli_query($conn, $get_color);
                            $row_color = mysqli_fetch_array($run_color);
                            $product_color_id = $row_color['product_color_id'];
                            $quantity = $row_cart['quantity'];
                            $get_products = "select * from products where product_id = '$product_id'";
                            $run_products = mysqli_query($conn, $get_products);
                            while($row_products = mysqli_fetch_array($run_products)){
                              $product_name = $row_products['product_name'];
                              $get_products_img = "select * from product_img where product_id = '$product_id' and product_color_id = '$product_color_id'";
                              $run_products_img = mysqli_query($conn, $get_products_img);
                              while($row_products_img = mysqli_fetch_array($run_products_img)){
                                $product_color_img = $row_products_img['product_color_img'];
                                $product_price_des = $row_products_img['product_price_des'];
                                $product_price_des_format = currency_format($product_price_des);
                                $product_price = $row_products_img['product_price'];
                                $product_price_format = currency_format($product_price);
                          
                      ?>
                        <div class="card p-4">
                            
                            <div class="row">
                                <!-- product img -->
                                <div class="col-md-2 col-11 mx-auto justify-content-center product-img">
                                    <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank"><img src="../administrator/product_img/<?php echo $product_color_img; ?>" class="img-fluid" alt=""></a>
                                </div>
                                <!-- cart-details -->
                                <div class="col-md-10 col-11 mx-auto px-4">
                                    <div class="row">
                                        <div class="col-xxl-8 col-xl-7 col-lg-6 col-sm-6 col-12 card-title">
                                            <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank" class="mb-4 fw-bold product-name">
                                                <?php echo $product_name; ?>
                                            </a>
                                            <span class="mb-4 d-block color">Màu: <?php echo $color; ?></span>
                                        </div>
                                        <div class="col-xxl-4 col-xl-5 col-lg-6 col-sm-6 col-12">
                                            <p class="item-price">
                                              <?php
                                                if($product_price_des == $product_price){
                                                  echo "
                                                  <b class='d-block'>$product_price_des_format</b><strike class='d-block'></strike>
                                                  ";
                                                }
                                                else{
                                                  echo "
                                                  <b class='d-block'>$product_price_des_format</b><strike class='d-block'>$product_price_format</strike>
                                                  ";
                                                }
                                              ?>
                                            </p>
                                            <form action="" method="post">
                                              <input type='hidden' name='product_id' value="<?php echo $product_id; ?>" />
                                              <input type='hidden' name='color' value="<?php echo $color; ?>" />
                                              <div class="quantity">
                                                  <button class="dec qtybtn" onclick="decreaseCount(event, this)">-</button>
                                                  <input type="text" name="quantity" value="<?php echo $quantity ?>">
                                                  <button class="inc qtybtn" onclick="increaseCount(event, this)">+</button>
                                              </div>
                                              <input type="hidden" name="update_cart" value="update">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                              <form action="" method="post">
                                <input type='hidden' name='product_id' value="<?php echo $product_id; ?>" />
                                <input type='hidden' name='color' value="<?php echo $color; ?>" />
                                <input type='hidden' name='remove_cart' value="remove" />
                                <div class="mt-2">
                                    <button type="submit" class="remove-button">
                                        <i class="fas fa-trash-alt"></i>
                                        Xóa
                                    </button>
                                </div>
                              </form>
                            </div>
                        </div>

                        <?php
                              }
                            }
                          }
                        };
                        ?>

                        <?php
                          function update_cart(){
                            global $conn;
                            if(isset($_POST['update_cart']) && $_POST['update_cart']=="update"){
                              $remove_id = $_POST['product_id'];
                              $remove_color = $_POST['color'];
                              $quantity_new = $_POST['quantity'];
                              $get_color = "select * from product_color where product_color_name = '$remove_color'";
                              $run_color = mysqli_query($conn, $get_color);
                              $row_color = mysqli_fetch_array($run_color);
                              $product_color_id = $row_color['product_color_id'];
                              $select_quantity = "select * from product_img where product_id='$remove_id' and product_color_id='$product_color_id'";
                              $run_select_quantity = mysqli_query($conn, $select_quantity);
                              $row_select_quantity = mysqli_fetch_array($run_select_quantity);
                              $product_quantity = $row_select_quantity['product_quantity'];
                              if($quantity_new > $product_quantity){
                                echo "<script>alert('Số lượng sản phẩm đã vượt quá số lượng cho phép')</script>";
                              }
                              else{
                                $delete_product = "update cart set quantity = '$quantity_new' where product_id = '$remove_id' and color = '$remove_color'";
                                $run_delete = mysqli_query($conn, $delete_product);
                                if($run_delete){
                                  echo "<script>window.open('cart.php', '_self')</script>";
                                }
                              }
                            }
                          }  
                          update_cart();
                        ?>
                      <div class="card p-4 mb-4">
                        <div class="row">
                          <p class="col-6">Tạm tính (<?php echo items(); ?> sản phẩm): </p>
                          <span class="col-6 price-total"><?php echo currency_format(total_price()); ?></span>
                        </div>
                      </div>
                    </div>

                    <?php
                        function remove_cart(){
                          global $conn;
                          if(isset($_POST['remove_cart']) && $_POST['remove_cart']=="remove"){
                            $remove_id = $_POST['product_id'];
                            $remove_color = $_POST['color'];
                              $delete_product = "delete from cart where product_id = '$remove_id' and color = '$remove_color'";
                              $run_delete = mysqli_query($conn, $delete_product);
                              if($run_delete){
                                echo "<script>window.open('cart.php', '_self')</script>";
                              }
                          }
                        }            
                      remove_cart();
                    ?>


                    <?php
                      if(isset($_SESSION['customer_id'])){
                        $customer_id = $_SESSION['customer_id'];
                        $select_customer = "select * from customer where customer_id = '$customer_id'";
                        $run_customer = mysqli_query($conn, $select_customer);
                        $row_customer = mysqli_fetch_array($run_customer);
                        $customer_name = $row_customer['customer_name'];
                        $customer_sex = $row_customer['customer_sex'];
                        $customer_phone = $row_customer['customer_phone'];
                        $customer_address = $row_customer['customer_address'];
                      }
                    ?>
                    <!-- right side -->
                    <div class="col-lg-6 col-12 mx-auto mb-lg-0 mb-5 right-side">
                      <div class="card p-4">
                        <form action="" method="post">
                          <h6>Thông tin khách hàng</h6>
                          <div class="check-sex mb-2">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="receiver_sex" id="male" value="Anh" <?php if($customer_sex=='Nam'){echo "checked";} ?>>
                              <label class="form-check-label" for="male">Anh</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="receiver_sex" id="female" value="Chị" <?php if($customer_sex=='Nữ'){echo "checked";} ?>>
                              <label class="form-check-label" for="female">Chị</label>
                            </div>
                          </div>
                          <div class="row mb-3">
                            <div class="col-lg-6 col-12 mb-2">
                              <input type="text" class="form-control" placeholder="Họ tên" value="<?php echo $customer_name; ?>" name="receiver">
                            </div>
                            <div class="col-lg-6 col-12">
                              <input type="text" class="form-control" placeholder="Số điện thoại" value="<?php echo $customer_phone; ?>" name="receiver_phone">
                            </div>
                          </div>
                          <h6>Chọn cách thức nhận hàng</h6>
                          <ul class="nav nav-pills check-receive mb-2 ms-2" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <div class="form-check form-check-inline nav-link active bg-white text-dark mx-3 my-2" style="padding: 0;" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                                <input class="form-check-input" type="radio" name="choose_delivery_location" id="inlineRadio1" value="Giao tận nơi" checked>
                                <label class="form-check-label" for="inlineRadio1">Giao tận nơi</label>
                              </div>
                            </li>
                            <li class="nav-item" role="presentation">
                              <div class="form-check form-check-inline nav-link bg-white text-dark ms-4 my-2" style="padding: 0;" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                                <input class="form-check-input" type="radio" name="choose_delivery_location" id="inlineRadio2" value="Nhận tại siêu thị" >
                                <label class="form-check-label" for="inlineRadio2">Nhận tại siêu thị</label>
                              </div>
                            </li>
                          </ul>
                          <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                              <div class="card card-body m-2">
                                <p>Nhập địa chỉ nhận hàng</p>
                                <input type="text" class="form-control" placeholder="Địa chỉ nhận hàng" value="<?php echo $customer_address; ?>" name="delivery_location">
                              </div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab"></div>
                          </div>
                          <div class="form-check mt-2 mb-4">
                            <input class="form-check-input" type="checkbox" id="check1" name="call_receiver_new" value="Gọi người khác" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            <label class="form-check-label">Gọi người khác nhận hàng (nếu có)</label>
                            <div class="collapse" id="collapse2">
                              <div class="card card-body m-2">
                                <p>Thông tin người nhận</p>
                                <div class="check-sex mb-2">
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="receiver_sex_new" id="male" value="Anh">
                                    <label class="form-check-label" for="male">Anh</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="receiver_sex_new" id="female" value="Chị">
                                    <label class="form-check-label" for="female">Chị</label>
                                  </div>
                                </div>
                                <div class="row mb-3">
                                  <div class="col-lg-6 col-12 mb-2">
                                    <input type="text" class="form-control" placeholder="Họ tên" name="receiver_new">
                                  </div>
                                  <div class="col-lg-6 col-12">
                                    <input type="text" class="form-control" placeholder="Số điện thoại" name="receiver_phone_new">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- <p>
                            <button class="btn btn-white border border-2 border-warning" type="button" data-bs-toggle="collapse" data-bs-target="#coupon_code" aria-expanded="false" aria-controls="coupon_code">
                            <i class="bi bi-cash me-2"></i>Sử dụng mã giảm giá
                            </button>
                          </p>
                          <div class="collapse mb-3" id="coupon_code">
                            <div class="card card-body bg-light">
                              <div class="row">
                                <div class="col-md-9 col-12 mb-2">
                                   <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã giảm giá">
                                </div>
                                <div class="col-md-3 col-12 mb-2">
                                <button type="submit" class="btn btn-secondary" name="apply_coupon">Áp dụng</button>
                                </div>
                              </div>
                            </div>
                          </div> -->
                          
                            <?php
                            $total_price = total_price();
                            $total_price_format = currency_format($total_price);
                            echo "
                            <div class='row'>
                              <p class='col-6'>Tổng tiền: </p>
                              <span class='col-6 price-total fw-bold'>$total_price_format</span>
                              
                            </div>
                            ";
                            echo "
                                <input type='hidden' name='total_price' value='$total_price'>
                                ";
                            ?>
                                <button type="submit" class="submit-order mb-3" name = "order">
                                  <b>Đặt hàng</b>
                                </button>
                        </form>
                      </div>
                    </div>
                </div>
    </section>
    
    <?php
    if(isset($_SESSION['customer_id'])&&isset($_POST['order'])){
        $customer_id = $_SESSION['customer_id'];
        $order_no = mt_rand();
        $status = "Đang chờ";
        $customer_name = $_POST['receiver'];
        $customer_sex = $_POST['receiver_sex'];
        if(!isset($_POST['call_receiver_new'])){
            $receiver = $_POST['receiver'];
            $receiver_phone = $_POST['receiver_phone'];
            $receiver_sex = $_POST['receiver_sex'];
        }
        else{
            $receiver = $_POST['receiver_new'];
            $receiver_phone = $_POST['receiver_phone_new'];
            $receiver_sex = $_POST['receiver_sex_new'];
        }
        if($_POST['choose_delivery_location']=="Giao tận nơi"){
            $delivery_location = $_POST['delivery_location'];
        }
        else{
            $delivery_location = "Siêu thị: Nguyên Xá 3, phường Minh Khai, quận Từ Liêm, Tp Hà Nội ";
        }
        $total_price = $_POST['total_price'];
        $insert_customer_order = "insert into customer_orders
            (customer_id,  order_date, total_price, status, order_no, receiver, receiver_sex, receiver_phone, delivery_location)
            values ('$customer_id', NOW(), '$total_price', '$status', '$order_no', '$receiver', '$receiver_sex', '$receiver_phone', '$delivery_location')";
        $run_customer_order = mysqli_query($conn, $insert_customer_order);
        $select_order_id = "select * from customer_orders where customer_id = '$customer_id' and order_no='$order_no'";
        $run_order_id = mysqli_query($conn, $select_order_id);
        $row_order_id = mysqli_fetch_array($run_order_id);
        $order_id = $row_order_id['order_id'];
        // if(isset($_POST['coupon_id'])){
        //   $coupon_id = $_POST['coupon_id'];
        //   $add_used = "update coupon set coupon_used=coupon_used+1 where coupon_id='$coupon_id'";
        //   $run_used = mysqli_query($conn, $add_used);
        // }
        $select_cart = "select * from cart where customer_id = '$customer_id'";
        $run_cart = mysqli_query($conn, $select_cart);
        while($row_cart = mysqli_fetch_array($run_cart)){
            $product_id = $row_cart['product_id'];
            $color = $row_cart['color'];
            $quantity = $row_cart['quantity'];
            $insert_customer_order_product = "insert into customer_order_products
            (order_id, product_id, color, quantity)
            values ('$order_id', '$product_id', '$color', '$quantity')";
            $run_customer_order_product = mysqli_query($conn, $insert_customer_order_product);
            $delete_cart = "delete from cart where customer_id = '$customer_id'";
            $run_delete = mysqli_query($conn, $delete_cart);
        }
        if($run_delete){
            echo "<script>window.open('order_success.php?customer_id=$customer_id&order_id=$order_id', '_self')</script>";
          }
    }
    ?>
