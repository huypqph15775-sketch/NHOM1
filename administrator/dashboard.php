<?php
// Đảm bảo session đã được khởi tạo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../administrator/includes/database.php';
require_once '../includes/auth.php';

// Kiểm tra đăng nhập admin
checkAdminLogin();

if (!isset($_SESSION['admin_id'])) {
    echo "<script>window.open('../signin.php', '_self')</script>";
    exit();
}

/* =============================
   TRUY VẤN SỐ LIỆU DASHBOARD
   ============================= */

// Đếm số sản phẩm
$select_product = "SELECT * FROM products";
$count_product = mysqli_num_rows(mysqli_query($conn, $select_product));

// Đếm số khách hàng
$select_customer = "SELECT * FROM customer";
$count_customer = mysqli_num_rows(mysqli_query($conn, $select_customer));

// Đếm danh mục / thương hiệu
$select_cartegory = "SELECT * FROM cartegory";
$count_cartegory = mysqli_num_rows(mysqli_query($conn, $select_cartegory));

// Đếm đơn hàng (đếm số mã đơn hàng order_no)
$select_orders = "SELECT DISTINCT order_no FROM customer_orders";
$count_orders = mysqli_num_rows(mysqli_query($conn, $select_orders));

// Đếm số phân quyền (roles)
$select_roles = "SELECT COUNT(*) AS total_roles FROM roles";
$result_roles = mysqli_query($conn, $select_roles);
$row_roles = mysqli_fetch_assoc($result_roles);
$count_roles = $row_roles['total_roles'] ?? 0;

// Đếm số chương trình khuyến mãi (promotions)
$select_promotions = "SELECT COUNT(*) AS total_promotions FROM promotions";
$result_promotions = mysqli_query($conn, $select_promotions);
$row_promotions = mysqli_fetch_assoc($result_promotions);
$count_discount = $row_promotions['total_promotions'] ?? 0;

// Tổng số lượng sản phẩm tồn kho (từ bảng product_img)
$select_stock = "SELECT SUM(product_quantity) AS total_stock FROM product_img";
$result_stock = mysqli_query($conn, $select_stock);
$row_stock = mysqli_fetch_assoc($result_stock);
$total_stock = $row_stock['total_stock'] ?? 0;

// Đếm số đơn hàng đã hoàn tất
$select_completed = "SELECT COUNT(DISTINCT order_no) AS total_completed 
                     FROM customer_orders 
                     WHERE status = 'Đã giao'";
$result_completed = mysqli_query($conn, $select_completed);
$row_completed = mysqli_fetch_assoc($result_completed);
$count_completed = $row_completed['total_completed'] ?? 0;

?>

<div class="row">
    <div class="col-md-12 fw-bold fs-3">Trang chủ</div>

    <div class="row mt-5">

        <!-- Sản phẩm -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#0099FF">
                <div class="card-body">
                    <i class="fas fa-phone-alt" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_product; ?> Sản phẩm</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?product_list" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Danh mục -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#00CC99">
                <div class="card-body">
                    <i class="fas fa-trademark" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_cartegory; ?> Danh mục</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?cartegory_list" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Khách hàng -->
        <div class="col-md-3 mb-3">
            <div class="card text-dark h-100" style="background:#FFFF00;">
                <div class="card-body">
                    <i class="fas fa-users" style="font-size: 90px;"></i>
                    <h5 class="card-title"><?php echo $count_customer; ?> Khách hàng</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?customer_list" class="text-dark text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#FF6600">
                <div class="card-body">
                    <i class="far fa-money-bill-alt" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_orders; ?> Đơn hàng</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?pending_orders" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Phân quyền -->
        <!-- <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#AA00FF">
                <div class="card-body">
                    <i class="fas fa-user-shield" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_roles; ?> Phân quyền</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?roles_list" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div> -->

        <!-- Khuyến mãi -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#FF33CC">
                <div class="card-body">
                    <i class="fas fa-gift" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_discount; ?> Khuyến mãi</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?discount_list" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Kho -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#0066FF">
                <div class="card-body">
                    <i class="fas fa-warehouse" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo (int)$total_stock; ?> SP tồn kho</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?stock_list" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="col-md-3 mb-3">
            <div class="card text-white h-100" style="background:#00CC00">
                <div class="card-body">
                    <i class="fas fa-chart-line" style="font-size: 90px"></i>
                    <h5 class="card-title"><?php echo $count_completed; ?> Đơn hoàn tất</h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?delivered_orders" class="text-white text-decoration-none">Xem chi tiết</a>
                    <i class="fas fa-angle-right"></i>
                </div>
            </div>
        </div>

    </div>
</div>
