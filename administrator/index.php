<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store | Admin</title>
    <!-- favicon -->
    <link rel="icon" href="images/smartphone.png">
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
    <link rel="stylesheet" href="css/product_add.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
    integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
    crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"
    integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw=="
    crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.0/datatables.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body style="background:#FFFFCC">
    <!-- navbar -->
    <?php
        include("includes/nav.php");
    ?>

    <section class="">
        <div class="container-fluid mt-5 pt-3">
            <?php
              if(isset($_GET['dashboard'])){
                include('dashboard.php');
              }
              // product
              if(isset($_GET['product_add'])){
                include('product_add.php');
              }
              if(isset($_GET['product_add_img'])){
                include('product_add_img.php');
              }
              if(isset($_GET['product_list'])){
                include('product_list.php');
              }
              if(isset($_GET['product_delete'])){
                include('product_delete.php');
              }
              if(isset($_GET['product_edit'])){
                include('product_edit.php');
              }
              if(isset($_GET['product_info'])){
                include('product_info.php');
              }
              if(isset($_GET['product_price'])){
                include('product_price.php');
              }
              // cartegory
              if(isset($_GET['cartegory_add'])){
                include('cartegory_add.php');
              }
              if(isset($_GET['cartegory_list'])){
                include('cartegory_list.php');
              }
              if(isset($_GET['cartegory_delete'])){
                include('cartegory_delete.php');
              }
              if(isset($_GET['cartegory_edit'])){
                include('cartegory_edit.php');
              }
              // color
              if(isset($_GET['color_add'])){
                include('color_add.php');
              }
              if(isset($_GET['color_list'])){
                include('color_list.php');
              }
              if(isset($_GET['color_delete'])){
                include('color_delete.php');
              }
              if(isset($_GET['color_edit'])){
                include('color_edit.php');
              }
              //customer
              if(isset($_GET['customer_list'])){
                include('customer_list.php');
              }
              if(isset($_GET['customer_delete'])){
                include('customer_delete.php');
              }
              //admin
              if(isset($_GET['admin_add'])){
                include('admin_add.php');
              }
              if(isset($_GET['admin_list'])){
                include('admin_list.php');
              }
              if(isset($_GET['admin_delete'])){
                include('admin_delete.php');
              }
              if(isset($_GET['admin_edit'])){
                include('admin_edit.php');
              }

              //order
              if(isset($_GET['pending_orders'])){
                include('pending_orders.php');
              }
              if(isset($_GET['delivering_orders'])){
                include('delivering_orders.php');
              }
              if(isset($_GET['delivered_orders'])){
                include('delivered_orders.php');
              }
              if(isset($_GET['order_info'])){
                include('order_info.php');
              }

            ?>
        </div>
    </section>
<!-- js -->
<script src="js/index.js"></script>

<!--Jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.0/datatables.min.js"></script>
</body>
</html>