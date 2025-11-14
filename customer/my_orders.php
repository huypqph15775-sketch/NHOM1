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

`   <?php
    if(!isset($_SESSION['customer_id'])){
      echo "<script>window.open('../signin.php', '_self')</script>";
    }
    ?>

    <!-- section myorder -->
    <section class="container" style="min-height: 280px">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Trang chủ</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Lịch sử đơn hàng</li>
                    </ol>
                </nav>  

                <?php
                    if(isset($_SESSION['customer_id'])){
                        $customer_id = $_SESSION['customer_id'];
                        $select_pending_orders = "select * from customer_orders where customer_id = '$customer_id' and (status='Đang chờ' or status LIKE 'Không được xác nhận%')";
                        $run_pending_orders = mysqli_query($conn, $select_pending_orders);
                        $count_pending_orders = mysqli_num_rows($run_pending_orders);
                        $select_delivering_orders = "select * from customer_orders where customer_id = '$customer_id' and status='Đang giao'";
                        $run_delivering_orders = mysqli_query($conn, $select_delivering_orders);
                        $count_delivering_orders = mysqli_num_rows($run_delivering_orders);
                        $select_delivered_orders = "select * from customer_orders where customer_id = '$customer_id' and status='Đã giao'";
                        $run_delivered_orders = mysqli_query($conn, $select_delivered_orders);
                        $count_delivered_orders = mysqli_num_rows($run_delivered_orders);
                        $select_canceled_orders = "select * from customer_orders where customer_id = '$customer_id' and status='Đã hủy'";
                        $run_canceled_orders = mysqli_query($conn, $select_canceled_orders);
                        $count_canceled_orders = mysqli_num_rows($run_canceled_orders);
                    }
                ?>

                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button onclick="location.href='my_orders.php?pending_orders'" class="nav-link position-relative <?php if(isset($_GET['pending_orders'])){echo "active";} ?> px-5 me-3" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-selected="true">Chờ xác nhận
                        <?php
                            if($count_pending_orders!=0){
                                echo "
                                <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>
                                    $count_pending_orders
                                </span>
                                ";
                            }
                        ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button onclick="location.href='my_orders.php?delivering_orders'" class="nav-link position-relative <?php if(isset($_GET['delivering_orders'])){echo "active";} ?> px-5 mx-3" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-selected="false">Đang giao
                        <?php
                            if($count_delivering_orders!=0){
                                echo "
                                <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>
                                    $count_delivering_orders
                                </span>
                                ";
                            }
                        ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button onclick="location.href='my_orders.php?delivered_orders'" class="nav-link position-relative <?php if(isset($_GET['delivered_orders'])){echo "active";} ?> px-5 mx-3" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-selected="false">Đã giao
                        <?php
                            if($count_delivered_orders!=0){
                                echo "
                                <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>
                                    $count_delivered_orders
                                </span>
                                ";
                            }
                        ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button onclick="location.href='my_orders.php?canceled_orders'" class="nav-link position-relative <?php if(isset($_GET['canceled_orders'])){echo "active";} ?> px-5 mx-3" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-selected="false">Đã hủy
                        <?php
                            if($count_canceled_orders!=0){
                                echo "
                                <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>
                                    $count_canceled_orders
                                </span>
                                ";
                            }
                        ?>
                        </button>
                    </li>
                </ul>

                <?php
                    
                    if(isset($_GET['pending_orders'])){
                        if($count_pending_orders == 0){
                            include("no_order.php");
                        }
                        else{
                            include("pending_orders.php");
                        }
                       
                    }
                    if(isset($_GET['delivering_orders'])){
                        if($count_delivering_orders == 0){
                            include("no_order.php");
                        }
                        else{
                            include("delivering_orders.php");
                        }
                       
                    }
                    if(isset($_GET['delivered_orders'])){
                        if($count_delivered_orders == 0){
                            include("no_order.php");
                        }
                        else{
                            include("delivered_orders.php");
                        }
                       
                    }
                    if(isset($_GET['canceled_orders'])){
                        if($count_canceled_orders == 0){
                            include("no_order.php");
                        }
                        else{
                            include("canceled_orders.php");
                        }
                       
                    }

                ?>
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