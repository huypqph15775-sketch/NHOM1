<?php
session_start();
require_once '../administrator/includes/database.php';
require_once '..//includes/auth.php';

// Kiểm tra đăng nhập admin
checkAdminLogin();

// Kiểm tra quyền nếu cần
// checkPermission('Quản lý'); 
?>
<?php
      if(!isset($_SESSION['admin_id'])){
        echo "<script>window.open('signin.php', '_self')</script>";
      }
      else{
        $select_product = "select * from products";
        $run_product = mysqli_query($conn, $select_product);
        $count_product = mysqli_num_rows($run_product);
        $select_customer = "select * from customer";
        $run_customer = mysqli_query($conn, $select_customer);
        $count_customer = mysqli_num_rows($run_customer);
        $select_cartegory = "select * from cartegory";
        $run_cartegory = mysqli_query($conn, $select_cartegory);
        $count_cartegory = mysqli_num_rows($run_cartegory);
        $select_orders = "select distinct order_no from customer_orders";
        $run_orders = mysqli_query($conn, $select_orders);
        $count_orders = mysqli_num_rows($run_orders);
      }
    ?>
    <div class="row">
      <div class="col-md-12 fw-bold fs-3">Trang chủ</div>       
        <div class="row mt-5">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body" style="background: #0099FF" >
                      <span><i class="fas fa-phone-alt" style="font-size: 100px"></i></span>
                      <h5 class="card-title d-inline-block"><?php echo $count_product; ?> Điện thoại</h5>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <a href="index.php?product_list" class="text-white text-decoration-none">Xem chi tiết</a>
                      <div class="small text-while"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body" style="background: #00CC99">
                    <span><i class="fas fa-trademark" style="font-size: 100px"></i></span>
                      <h5 class="card-title d-inline-block"><?php echo $count_cartegory; ?> Thương hiệu</h5>
                      
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <a href="index.php?cartegory_list" class="text-white text-decoration-none">Xem chi tiết</a>
                      <div class="small text-while"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body" style="background:#FFFF00">
                    <span><i class="fas fa-users" style="font-size: 100px"></i></span>
                      <h5 class="card-title d-inline-block"><?php echo $count_customer; ?> Khách hàng</h5>             
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <a href="index.php?customer_list" class="text-white text-decoration-none">Xem chi tiết</a>
                      <div class="small text-while"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body" style="background:#FF6600">
                    <span><i class="far fa-money-bill-alt" style="font-size: 100px"></i></span>
                      <h5 class="card-title d-inline-block"><?php echo $count_orders; ?> Đơn hàng</h5>
                      <p class="card-text">
                      </p>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <a href="index.php?pending_orders" class="text-white text-decoration-none">Xem chi tiết</a>
                      <div class="small text-while"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>