<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store</title>
    <!-- favicon -->
    <link rel="icon" href="../images/phonesmart.png">
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- bootstrap icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/myaccount.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
    integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
    crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"
    integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw=="
    crossorigin="anonymous" />

</head>
<body>
    <!-- Header -->
    <?php
        include("includes/header.php");
    ?>

`   <?php
    if(!isset($_SESSION['customer_id'])){
      echo "<script>window.open('../signin.php', '_self')</script>";
    }
    ?>

    <?php
        if(isset($_GET['order_id'])){
            $order_id = $_GET['order_id'];
            $select_order = "select * from customer_orders where order_id = '$order_id'";
            $run_select_order = mysqli_query($conn, $select_order);
            $row_select_order = mysqli_fetch_array($run_select_order);
            $total_price = $row_select_order['total_price'];
            $customer_id = isset($row_select_order['customer_id']) ? $row_select_order['customer_id'] : 0;
            $total_price_format = currency_format($total_price);
            $status = $row_select_order['status'];
            $receiver = $row_select_order['receiver'];
            $receiver_sex = $row_select_order['receiver_sex'];
            $receiver_phone = $row_select_order['receiver_phone'];
            $delivery_location = $row_select_order['delivery_location'];
            $payment_type = $row_select_order['payment_type'];
            $voucher_code = $row_select_order['voucher_code'];
$tracking_code = isset($row_select_order['tracking_code']) ? $row_select_order['tracking_code'] : '';
$discount_value = $row_select_order['discount_value'];
$total_after_discount = $row_select_order['total_after_discount'];
$total_after_discount_format = currency_format($total_after_discount);
$discount_value_format = currency_format($discount_value);
        }
    ?>
    <!-- section myorder -->
    <section class="container" style="min-height: 280px">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a class="text-decoration-none" href="../index.php">Trang chủ</a></li>
                      <li class="breadcrumb-item"><a class="text-decoration-none" href="
                      <?php
                        if($status=="Đang giao") echo "my_orders.php?delivering_orders";
                        else if($status=="Đã giao") echo "my_orders.php?delivered_orders";
                        else if($status=="Đã hủy") echo "my_orders.php?canceled_orders";
                        else echo "my_orders.php?pending_orders";
                      ?>
                      ">Lịch sử đơn hàng</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
                    </ol>
                </nav> 
            </div>
            <div class="col-lg-12 bg-white p-3">  
                <div class="row mb-3">
                    <div class="col-lg-7 col-12">
                        <p class="text-uppercase fs-4" style="margin-bottom:0">Chi tiết đơn hàng #<?php echo $order_id; ?></p>
                        <p style="margin-bottom:0">Mua tại Phone Store</p>
                    </div>
                    <div class="col-lg-5 col-12 text-end">
                        <p><span class="text-secondary">Trạng thái: </span><span class="
                        <?php
                            if($status=="Đang chờ") echo "text-warning";
                            if($status=="Đang giao") echo "text-primary";
                            if($status=="Đã giao") echo "text-success";
                            if($status=="Đã hủy") echo "text-danger";
                        ?>
                        "><?php echo $status; ?></span></p>
                        <!-- Print buttons removed for customer view -->
                    </div>
                </div>
                <hr class="dropdown-divider mb-3"></hr>

                <?php
                    $select_order_product = "select * from customer_order_products where order_id = '$order_id'";
                    $run_select_order_product = mysqli_query($conn, $select_order_product);
                    while($row_select_order_product = mysqli_fetch_array($run_select_order_product)){
                        $product_id = $row_select_order_product['product_id'];
                        $color = $row_select_order_product['color'];
                        $quantity = $row_select_order_product['quantity'];
                        $get_color = "select * from product_color where product_color_name = '$color'";
                        $run_color = mysqli_query($conn, $get_color);
                        $row_color = mysqli_fetch_array($run_color);
                        $product_color_id = $row_color['product_color_id'];
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
                ?>
                <div class="row mb-3">
                    <div class="col-1 mx-auto justify-content-center product-img">
                        <a href="../shop-detail.php?product_id=<?= $product_id; ?>&color=<?= $product_color_id; ?>" target="_blank"><img src="../administrator/product_img/<?= $product_img ?>" width="200px" class="img-fluid" alt=""></a>
                    </div>
                    <div class="col-8 mx-auto ps-4">
                        <a href="../shop-detail.php?product_id=<?= $product_id; ?>&color=<?= $product_color_id; ?>" target="_blank" class="mb-4 fw-bold text-decoration-none product-name">
                            <?php echo $product_name; ?>
                        </a>
                        <p class="mt-3 text-bottom" style="font-size: 14px">
                            <span class="mb-2 text-secondary color">Màu: </span><?= $color; ?>
                            <span class="mb-2 text-secondary ms-5 color">Số lượng: </span><?= $quantity; ?>
                        </p>
                    </div>
                    <div class="col-2 text-end">
                        <b class="d-block text-danger"><?= $product_price_des_format; ?></b><strike class="d-block"><?= $product_price_format; ?></strike>
                    </div>
                </div>
                <hr class="dropdown-divider text-secondary mb-3"></hr>
                <?php
                    }
                ?>
            <div class="row mb-3">
    <div class="col-9 text-end">
        <span class="d-block">Tạm tính: </span>

        <?php if($voucher_code != "" && $discount_value > 0): ?>
            <span class="d-block text-success">Mã giảm giá (<?= $voucher_code ?>): </span>
            <span class="d-block text-danger">Giảm: </span>
            <span class="d-block fw-bold">Tổng thanh toán: </span>
        <?php else: ?>
            <span class="d-block fw-bold">Tổng tiền: </span>
        <?php endif; ?>

        <span class="d-block fw-bold">Hình thức thanh toán: </span>
    </div>

    <div class="col-3 text-end">
        <span class="d-block"><?= $total_price_format; ?></span>

        <?php if($voucher_code != "" && $discount_value > 0): ?>
            <span class="d-block text-success">-<?= $discount_value_format; ?></span>
            <span class="d-block text-danger fw-bold"><?= $total_after_discount_format; ?></span>
        <?php endif; ?>

        <?php if($voucher_code == "" || $discount_value == 0): ?>
            <span class="d-block text-danger fw-bold"><?= $total_price_format; ?></span>
        <?php endif; ?>

        <span class="d-block fw-bold text-primary"><?= $payment_type; ?></span>
    </div>
</div>

                <hr class="dropdown-divider mb-3"></hr>
                <span class="d-block fw-bold">Địa chỉ và thông tin người nhận hàng: </span>
                <ul>
                    <li><?= $receiver_sex ?><?= $receiver ?> - <?= $receiver_phone ?></li>
                    <li>Địa chỉ nhận hàng: <?= $delivery_location ?></li>
                </ul>
                <hr class="dropdown-divider my-3"></hr>
                <div class="row justify-content-center">
                    <a href="
                    <?php
                        if($status=="Đang giao") echo "my_orders.php?delivering_orders";
                        else if($status=="Đã giao") echo "my_orders.php?delivered_orders";
                        else if($status=="Đã hủy") echo "my_orders.php?canceled_orders";
                        else echo "my_orders.php?pending_orders";
                    ?>
                    " class="col-4 py-3 btn btn-primary text-white">Quay lại danh sách đơn hàng</a>
                </div>
                <?php if(!empty($tracking_code)): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="fw-bold">Mã vận đơn: <span class="text-primary"><?php echo htmlspecialchars($tracking_code); ?></span></p>
                    </div>
                </div>
                <?php endif; ?>
                                <!-- Review forms for purchased items (show only when order is delivered to customer) -->
                                <?php if(isset($_SESSION['customer_id']) && intval($_SESSION['customer_id']) === intval($customer_id) && (mb_stripos($status, 'Đã giao') !== false)): ?>
                                <div class="mt-4">
                                    <h5>Đánh giá sản phẩm</h5>
                                    <?php
                                        $get_order_items = "SELECT * FROM customer_order_products WHERE order_id = '$order_id'";
                                        $run_order_items = mysqli_query($conn, $get_order_items);
                                        while($item = mysqli_fetch_assoc($run_order_items)){
                                            $pid = intval($item['product_id']);
                                            $pcolor = mysqli_real_escape_string($conn, $item['color']);
                                            // try to get color id
                                            $get_color = "SELECT product_color_id FROM product_color WHERE product_color_name='".mysqli_real_escape_string($conn,$pcolor)."' LIMIT 1";
                                            $rc = mysqli_query($conn, $get_color);
                                            $color_id_val = 0;
                                            if($rc && mysqli_num_rows($rc)>0){ $rowc = mysqli_fetch_assoc($rc); $color_id_val = intval($rowc['product_color_id']); }
                                            // check if already reviewed for this order
                                            $chk = mysqli_query($conn, "SELECT id FROM product_reviews WHERE product_id='$pid' AND customer_id='".intval($_SESSION['customer_id'])."' AND order_id='$order_id'");
                                            if($chk === false){
                                                // query failed; avoid fatal and skip showing review form for safety
                                                error_log('product_reviews check failed: '.mysqli_error($conn));
                                            } else if(mysqli_num_rows($chk) > 0){
                                                echo "<div class='alert alert-secondary'>Bạn đã đánh giá sản phẩm #".htmlspecialchars($pid)." cho đơn này.</div>";
                                                continue;
                                            }
                                            // render small review form
                                            $get_pname = mysqli_query($conn, "SELECT product_name FROM products WHERE product_id='$pid' LIMIT 1");
                                            $pname = ($get_pname && mysqli_num_rows($get_pname)>0) ? mysqli_fetch_assoc($get_pname)['product_name'] : ('Sản phẩm #'.$pid);
                                            ?>
                                            <div class="card mb-3 p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-3"><strong><?php echo htmlspecialchars($pname); ?></strong></div>
                                                    <div class="col-9">
                                                        <form method="post">
                                                            <input type="hidden" name="rev_order_id" value="<?php echo $order_id; ?>">
                                                            <input type="hidden" name="rev_product_id" value="<?php echo $pid; ?>">
                                                            <input type="hidden" name="rev_color_id" value="<?php echo $color_id_val; ?>">
                                                            <div class="mb-2">
                                                                <label class="form-label">Đánh giá:</label>
                                                                <select name="rating" class="form-select form-select-sm" style="width:120px; display:inline-block; margin-left:8px;">
                                                                    <option value="5">5 sao</option>
                                                                    <option value="4">4 sao</option>
                                                                    <option value="3">3 sao</option>
                                                                    <option value="2">2 sao</option>
                                                                    <option value="1">1 sao</option>
                                                                </select>
                                                                <input type="text" name="title" class="form-control form-control-sm mt-2" placeholder="Tiêu đề (tùy chọn)">
                                                                <textarea name="message" class="form-control form-control-sm mt-2" placeholder="Nội dung đánh giá" rows="2"></textarea>
                                                            </div>
                                                            <button type="submit" name="submit_review" class="btn btn-primary btn-sm">Gửi đánh giá</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <?php endif; ?>
                <?php
                // Handle review submission
                if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])){
                    $rev_order_id = intval($_POST['rev_order_id']);
                    $rev_product_id = intval($_POST['rev_product_id']);
                    $rev_color_id = intval($_POST['rev_color_id']);
                    $rating = intval($_POST['rating']);
                    if($rating < 1) $rating = 1; if($rating > 5) $rating = 5;
                    $title = mysqli_real_escape_string($conn, $_POST['title']);
                    $message = mysqli_real_escape_string($conn, $_POST['message']);
                    $customer_id = $_SESSION['customer_id'] ?? 0;
                    if($customer_id){
                        // server-side validation: order belongs to customer, status is 'Đã giao', and product exists in that order
                        $order_check_sql = "SELECT * FROM customer_orders WHERE order_id='".intval($rev_order_id)."' AND customer_id='".intval($customer_id)."' LIMIT 1";
                        $order_check_q = mysqli_query($conn, $order_check_sql);
                        if(!$order_check_q || mysqli_num_rows($order_check_q) == 0){
                            echo "<script>alert('Đơn hàng không hợp lệ hoặc không thuộc về bạn.')</script>";
                        } else {
                            $ord = mysqli_fetch_assoc($order_check_q);
                            if(mb_stripos($ord['status'], 'Đã giao') === false){
                                echo "<script>alert('Bạn chỉ có thể đánh giá sau khi đã nhận hàng.')</script>";
                            } else {
                                // confirm product exists in that order
                                $exists_q = mysqli_query($conn, "SELECT * FROM customer_order_products WHERE order_id='".intval($rev_order_id)."' AND product_id='".intval($rev_product_id)."' LIMIT 1");
                                if(!$exists_q || mysqli_num_rows($exists_q) == 0){
                                    echo "<script>alert('Sản phẩm không thuộc đơn hàng.')</script>";
                                } else {
                                    // prevent duplicate review for same order item
                                    $check = mysqli_query($conn, "SELECT id FROM product_reviews WHERE product_id='".intval($rev_product_id)."' AND customer_id='".intval($customer_id)."' AND order_id='".intval($rev_order_id)."'");
                                    if($check === false){
                                        error_log('product_reviews duplicate check failed: '.mysqli_error($conn));
                                        echo "<script>alert('Lỗi hệ thống: không thể kiểm tra đánh giá. Vui lòng thử lại sau.')</script>";
                                    } else if(mysqli_num_rows($check) == 0){
                                        $ins = "INSERT INTO product_reviews (product_id, product_color_id, customer_id, order_id, rating, title, message, created_at) VALUES ('".intval($rev_product_id)."', '".intval($rev_color_id)."', '".intval($customer_id)."', '".intval($rev_order_id)."', '".intval($rating)."', '".mysqli_real_escape_string($conn, $title)."', '".mysqli_real_escape_string($conn, $message)."', NOW())";
                                        mysqli_query($conn, $ins);
                                        echo "<script>alert('Cảm ơn bạn đã đánh giá'); window.location = 'order_detail.php?order_id=".intval($rev_order_id)."';</script>";
                                        exit;
                                    } else {
                                        echo "<script>alert('Bạn đã đánh giá sản phẩm này cho đơn hàng này rồi');</script>";
                                    }
                                }
                            }
                        }
                    } else {
                        echo "<script>window.open('../signin.php','_self')</script>";
                    }
                }
                ?>
            </div>                    
        </div>

    </section>

        <!-- footer -->
        <?php
            include("../includes/footer.php");
        ?>
      
          <!-- js -->
          <script src="js/index.js"></script>
      
          <!--Jquery -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
          <!-- Owl Carousel -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
          <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script> -->
          <!-- custom JS code after importing jquery and owl -->
          <script src="js/owlcarousel.js"></script>
      </body>
      </html>