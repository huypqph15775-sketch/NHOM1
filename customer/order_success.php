<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store</title>
    <!-- favicon -->
    <link rel="icon" href="../images/smartphone.png">
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

    <?php
    if(isset($_GET['customer_id'])&&isset($_GET['order_id'])){
        $customer_id = $_GET['customer_id'];
        $order_id = $_GET['order_id'];
        $select_customer = "select * from customer where customer_id = '$customer_id'";
        $run_customer = mysqli_query($conn, $select_customer);
        $row_customer = mysqli_fetch_array($run_customer);
        $customer_name = $row_customer['customer_name'];
        $customer_sex = $row_customer['customer_sex'];
        $select_customer_orders = "select * from customer_orders where customer_id = '$customer_id' and order_id  = '$order_id'";
        $run_customer_orders = mysqli_query($conn, $select_customer_orders);
        $row_customer_orders = mysqli_fetch_array($run_customer_orders);
            $receiver = $row_customer_orders['receiver'];
            $receiver_sex = $row_customer_orders['receiver_sex'];
            $receiver_phone = $row_customer_orders['receiver_phone'];
            $delivery_location = $row_customer_orders['delivery_location'];
            $total_price = $row_customer_orders['total_price'];
    }
    ?>

    <!-- section myorder -->
    <section class="container mt-3" style="min-height: 280px">
    <!-- section myacount -->
    <section class="container mt-3" style="min-height: 280px">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                      <li class="breadcrumb-item"><a href="#">Giỏ hàng</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Đặt hàng</li>
                    </ol>
                </nav>  
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-12">
                        <div class="card ">
                            <h5 class="card-header text-center">
                                <i class="fas fa-clipboard-check text-success fs-1"></i>
                                <span class="text-uppercase ms-3 my-3 text-success fw-bold">Đặt hàng thành công</span>
                            </h5>
                            <div class="card-body">
                                <p class="card-text">Cảm ơn <strong><?php if($customer_sex = 'Nam'){echo "Anh ".$customer_name;} else{echo "Chị ".$customer_name;} ?> </strong> đã cho Thegioididong cơ hội được phục vụ. 
                                Trước 10:00 hôm sau, nhân viên của chúng tôi sẽ <strong>gọi điện hoặc gửi tin nhắn xác nhận giao hàng</strong> cho anh</p>
                                <div class="row justify-content-center">
                                    <div class="col-11 bg-light">
                                        <div class="row mb-3 pt-2">
                                            <div class="col-7 text-uppercase">Đơn hàng #<?php echo $order_id; ?></div>
                                        </div>
                                        <p class="mb-3">- <strong>Người nhận hàng: </strong><?php echo $receiver_sex." ".$receiver.", ".$receiver_phone; ?></p>
                                        <p class="mb-3">- <?php echo "<strong>Địa chỉ nhận: </strong>$delivery_location (nhân viên sẽ xác nhận trước khi giao hàng).</p>"; ?>
                                        <p class="mb-3">- <strong>Tổng tiền: <span class="text-danger"><?php echo currency_format($total_price);?></span></strong></p>
                                    </div>
                                </div>
                                <div class="row justify-content-center mt-2">
                                    <h6 class="text-uppercase fw-bold">Chọn hình thức thanh toán:</h6>
                                    
                                        <div class="row mb-2">
                                            <div class="col-md-6 col-12">
                                                <form action="" method="post">
                                                    <button name="cash" class="btn btn-primary text-white py-3 w-100">Thanh toán tiền mặt khi nhận hàng</button>
                                                    <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">                          
                                                </form> 
                                            </div>  
                                            <div class="col-md-6 col-12">
                                            <form class="" method="POST" target="_blank" enctype="application/x-www-form-urlencoded" action="xulythanhtoanmomo_atm.php">                                                    
                                                    <input type="submit" name="momo" class="btn btn-danger text-white py-3 w-100" value="Thanh toán MOMO ATM">
                                                    <input type="hidden" name="total" value="<?php echo $total_price;?>">     
                                                    <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">                          
                                                </form> 
                                            </div>
                                        </div>                          
                                                                               
                                        </div>
                                        <!-- <div class="row mb-2">
                                            <div class="col-md-6 col-12">
                                                <a href="" class="btn btn-primary text-white py-3 w-100">
                                                    <span class="me-2">Thanh toán thẻ </span>
                                                    <img src="images/theatm.png" width="40px" alt="">
                                                </a>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <a href="" class="btn btn-primary text-white py-3 w-100">
                                                    <span class="me-2">Thanh toán thẻ </span>
                                                    <img src="images/visa.jpg" width="25px" alt="">
                                                </a>
                                            </div>
                                            </div> -->
                                    
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                     
            </div>                    
        </div>
    </section>
        <?php
            include("payment.php");
        ?>
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