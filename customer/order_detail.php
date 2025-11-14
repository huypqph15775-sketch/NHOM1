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
            $total_price_format = currency_format($total_price);
            $status = $row_select_order['status'];
            $receiver = $row_select_order['receiver'];
            $receiver_sex = $row_select_order['receiver_sex'];
            $receiver_phone = $row_select_order['receiver_phone'];
            $delivery_location = $row_select_order['delivery_location'];
            $payment_type = $row_select_order['payment_type'];
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
                        <p style="margin-bottom:0">Mua tại SmartPhone Store</p>
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
                        <span class="d-block fw-bold">Tổng tiền: </span>
                        <span class="d-block fw-bold">Hình thức thanh toán: </span>
                    </div>
                    <div class="col-3 text-end">
                        <span class="d-block fw-bold text-danger"><?= $total_price_format; ?></span>
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
            </div>                    
        </div>

    </section>

        <!-- footer -->
        <?php
            include("includes/footer.php");
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