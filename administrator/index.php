<?php
// Start output buffering so permission checks can safely redirect even after
// includes that echo HTML (prevents "Headers already sent" warnings).
// Note: long-term fix is to run permission checks before outputting HTML.
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Store | Admin</title>
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
        require_once '../includes/auth.php';
        checkAdminLogin();
        
        include("includes/nav.php");
    ?>

    <section class="">
        <div class="container-fluid mt-5 pt-3">
            <?php
                // If the logged-in user is a warehouse staff (level 2), restrict available pages
                // LV2 allowed pages: stock_list, price_history (sale), import_history. LV3 will be blocked from these.
                if (isset($user_level) && $user_level == 2) {
                  if (isset($_GET['stock_list'])) {
                    include('stock_list.php');
                  } elseif (isset($_GET['price_history'])) {
                    include('price_history.php');
                  } elseif (isset($_GET['import_history'])) {
                    include('import_price_history.php');
                  } else {
                    // Default for warehouse staff: show stock list
                    include('stock_list.php');
                  }
                } else {
              // Full admin routing for non-warehouse staff
              if(isset($_GET['dashboard'])){
                include('dashboard.php');
              }
              // product
              if(isset($_GET['product_add'])){
                // Allow named sales staff 'nvbanhang' to access product creation without full admin level
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('product_add.php');
              }
              if(isset($_GET['product_add_img'])){
                // Allow named sales staff 'nvbanhang' to upload product images
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('product_add_img.php');
              }
              if(isset($_GET['product_list'])){
                include('product_list.php');
              }
              if(isset($_GET['product_delete'])){
                // Allow sales staff 'nvbanhang' to delete product entries if needed
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('product_delete.php');
              }
              if(isset($_GET['product_edit'])){
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('product_edit.php');
              }
              if(isset($_GET['product_info'])){
                // product info is read-only; allow staff to view
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('product_info.php');
              }
              if(isset($_GET['product_price'])){
                // price adjustments are more sensitive; allow named user if necessary
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('product_price.php');
              }
              // cartegory
              if(isset($_GET['cartegory_add'])){
                checkPermission(4);
                include('cartegory_add.php');
              }
              if(isset($_GET['cartegory_list'])){
                include('cartegory_list.php');
              }
              if(isset($_GET['cartegory_delete'])){
                checkPermission(4);
                include('cartegory_delete.php');
              }
              if(isset($_GET['cartegory_hide'])){
                checkPermission(4);
                include('cartegory_hide.php');
              }
              if(isset($_GET['cartegory_unhide'])){
                checkPermission(4);
                include('cartegory_hide.php');
              }
              if(isset($_GET['cartegory_edit'])){
                checkPermission(4);
                include('cartegory_edit.php');
              }
              // color
              if(isset($_GET['color_add'])){
                // Allow sales staff 'nvbanhang*' to manage colors
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('color_add.php');
              }
              if(isset($_GET['color_list'])){
                // viewing list allowed to staff-level too
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('color_list.php');
              }
              if(isset($_GET['color_delete'])){
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
                include('color_delete.php');
              }
              if(isset($_GET['color_edit'])){
                checkPermissionOrAllowUser(4, ['nvbanhang*']);
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
                checkPermission(4);
                include('admin_add.php');
              }
              if(isset($_GET['admin_list'])){
                checkPermission(4);
                include('admin_list.php');
              }
              if(isset($_GET['admin_delete'])){
                checkPermission(4);
                include('admin_delete.php');
              }
              if(isset($_GET['admin_edit'])){
                checkPermission(4);
                include('admin_edit.php');
              }

              //order
              if(isset($_GET['pending_orders'])){
                // Allow sales staff to view/manage orders
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('pending_orders.php');
              }
              if(isset($_GET['delivering_orders'])){
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('delivering_orders.php');
              }
              if(isset($_GET['delivered_orders'])){
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('delivered_orders.php');
              }
              if(isset($_GET['packing_orders'])){
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('packing_orders.php');
              }
              if(isset($_GET['order_info'])){
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('order_info.php');
              }
              if(isset($_GET['notifications'])){
                // Allow sales staff to view notifications
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('notifications.php');
              }
              if(isset($_GET['notifications_add'])){
                // Allow sales staff to add notifications
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('notifications_add.php');
              }
              if(isset($_GET['chat_messages'])){
                // Allow sales staff to view chat messages
                checkPermissionOrAllowUser(3, ['nvbanhang*']);
                include('chat_messages.php');
              }
              // vouchers
              if(isset($_GET['voucher_list'])){
                checkPermission(4);
                include('voucher_list.php');
              }
              if(isset($_GET['voucher_add'])){
                checkPermission(4);
                include('voucher_add.php');
              }
              if(isset($_GET['voucher_edit'])){
                checkPermission(4);
                include('voucher_edit.php');
              }
              if(isset($_GET['voucher_delete'])){
                checkPermission(4);
                include('voucher_delete.php');
              }
              // reviews
              if(isset($_GET['reviews_list'])){
                include('reviews_list.php');
              }

               // mailbox (quản lý mail liên hệ)
               if(isset($_GET['mailbox'])){
                 include('mailbox.php');
               }

              // stock (allowed to lv2 and lv4+)
              if(isset($_GET['stock_list'])){
                $user_level = (int)($_SESSION['admin_level'] ?? 0);
                if ($user_level == 2 || $user_level >= 4) {
                  include('stock_list.php');
                } else {
                  $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
                  header("Location: /phonestoree/administrator/index.php");
                  exit();
                }
              }
              
              // manage price (only admin level 4+)
              if(isset($_GET['manage_price'])){
                checkPermission(4);
                include('manage_price.php');
              }
              
              // price history (accessible to lv2 and lv4+)
              if(isset($_GET['price_history'])){
                $user_level = (int)($_SESSION['admin_level'] ?? 0);
                if ($user_level == 2 || $user_level >= 4) {
                  include('price_history.php');
                } else {
                  $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
                  header("Location: /phonestoree/administrator/index.php");
                  exit();
                }
              }
              // import price history (accessible to lv2 and lv4+)
              if(isset($_GET['import_history'])){
                $user_level = (int)($_SESSION['admin_level'] ?? 0);
                if ($user_level == 2 || $user_level >= 4) {
                  include('import_price_history.php');
                } else {
                  $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
                  header("Location: /phonestoree/administrator/index.php");
                  exit();
                }
              }
              
              // statistic
              if(isset($_GET['statistic'])){
                checkPermission(3);
                include('statistic.php');
              }
              
              // news
              if(isset($_GET['news_add'])){
                // Allow sales staff to create news posts
                checkPermissionOrAllowUser(3, ['nvbanhang']);
                include('news_add.php');
              }
              if(isset($_GET['news_edit'])){
                checkPermissionOrAllowUser(3, ['nvbanhang']);
                include('news_edit.php');
              }
              if(isset($_GET['news_list'])){
                checkPermissionOrAllowUser(3, ['nvbanhang']);
                include('news_list.php');
              }
              if(isset($_GET['sales_price_history'])){
                // Sales history should be accessible to staff-level accounts; allow named 'nvbanhang' as well
                checkPermissionOrAllowUser(3, ['nvbanhang']);
                include('sales_price_history.php');
              }
              if(isset($_GET['news_comments'])){
                include('news_comments.php');
              }
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