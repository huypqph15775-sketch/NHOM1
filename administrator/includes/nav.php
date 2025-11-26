<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Kết nối DB & các hàm trong thư mục administrator
include("includes/database.php");
include("functions/functions.php");

// Sử dụng hệ thống phân quyền chung
require_once __DIR__ . '/../../includes/auth.php';

// Bắt buộc phải là admin mới vào được khu administrator
checkAdminLogin();

// Lấy thông tin user hiện tại (admin đang đăng nhập)
$current_user = getCurrentUser();
?>

<!-- Hiển thị thông tin user -->
<div class="user-info">
    <?php if ($current_user): ?>
        <span>
            Xin chào, <?php echo htmlspecialchars($current_user['name']); ?>
            (
            <?php
                if (!empty($current_user['role_name'])) {
                    echo htmlspecialchars($current_user['role_name']);
                } elseif (!empty($current_user['level'])) {
                    echo htmlspecialchars($current_user['level']);
                } else {
                    echo 'Admin';
                }
            ?>
            )
        </span>
    <?php else: ?>
        <span>Xin chào, Admin</span>
    <?php endif; ?>
</div>

<!-- Ẩn menu item dựa trên phân quyền -->
<?php if ($current_user['level'] == 'Quản lý'): ?>
<li>
    <a href="admin_list.php">
        <i class="fas fa-users"></i> Quản lý admin
    </a>
</li>
<?php endif; ?>
 <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="background-color: #66CC66">
        <div class="container-fluid" >
            <!-- offcanvas trigger -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                <span class="navbar-toggler-icon" data-bs-target="#offcanvasExample"></span>
            </button>
            <!--  -->
          <a class="navbar-brand fw-bold me-auto" href="index.php?dashboard">SmartPhone Store</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            
            <form class="d-flex ms-auto my-4 my-lg-0 px-5">
                <div class="input-group">
                    <input style="background-color:lightgreen;" type="text" class="form-control" placeholder="Bạn tìm gì..." aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button style="background-color:gray;" class="btn btn-outline-secondary" type="button" id="button-addon2">
                        <i style="color:yellow;" class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav mb-2 ps-5 mb-lg-0">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user me-2"></i>
                    <?php
                            if(isset($_SESSION['admin_name'])){
                                $admin_name = $_SESSION['admin_name'];
                                echo $admin_name;
                            }
                    ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Tài khoản</a></li>
                    <li><a class="dropdown-item" href="signout.php">Đăng xuất</a></li>
                  </ul>
                  
                </li>
              </ul>
          </div>
        </div>
    </nav>

    <!-- offcanvas -->

    <div  style="overflow: hidden;" class="offcanvas offcanvas-start bg-dark text-white sidebar-nav" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel" style="background-color: #66CC66">
        <div  class="offcanvas-header" style="background-color: #888888">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">
                <img style="border-radius: 10px" src="images/smartphone.png" alt="" class="my-auto me-3">
                <span class="logo_name fw-bold fs-3">Admin</span>
            </h5>
        </div>
        <div class="offcanvas-body" style=" background-color: #888888">
            <nav class="navbar-dark">
                <ul class="navbar-nav">
                    <li>
                        <a href="index.php?dashboard" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-home"></i></span>
                            <span style="font-size:22px">Trang chủ</span>
                        </a>
                    </li>
                    
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-trademark"></i></span>
                            <span style="font-size:22px">Thương hiệu</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse2">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?cartegory_add" class="nav-link px-3">
                                            <span style="font-size:18px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?cartegory_list" class="nav-link px-3">
                                            <span style="font-size:18px">Danh sách thương hiệu</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse3" role="button" aria-expanded="false" aria-controls="collapse3">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-phone-alt"></i></span>
                            <span style="font-size:22px">Điện thoại</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse3">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?product_add" class="nav-link px-3">
                                            <span style="font-size:18px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?product_add_img" class="nav-link px-3">
                                            <span style="font-size:18px">Thêm điện thoại theo</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?product_list" class="nav-link px-3">
                                            <span style="font-size:18px">Danh sách điện thoại</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse5" role="button" aria-expanded="false" aria-controls="collapse5">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-palette"></i></span>
                            <span style="font-size:22px">Màu sắc</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse5">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?color_add" class="nav-link px-3">
                                            <span style="font-size:18px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?color_list" class="nav-link px-3">
                                            <span style="font-size:18px">Danh sách Màu</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="index.php?pending_orders" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:22px">Đơn hàng</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?customer_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-users"></i></i></span>
                            <span style="font-size:22px">Khách hàng</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse1" role="button" aria-expanded="false" aria-controls="collapse1">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="fas fa-user-cog"></i></span>
                            <span style="font-size:22px">Admin</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse1">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?admin_add" class="nav-link px-3">
                                            <span style="font-size:18px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?admin_list" class="nav-link px-3">
                                            <span style="font-size:18px">Danh sách Admin</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                     
                    </li>
                    <!-- khuyenmai -->
 
                     <li>
                        <a href="index.php?voucher_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:22px">khuyen mai</span>
                        </a>
                    </li>

                    <!-- kho -->
                     <li>
                        <a href="index.php?stock_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:22px">Kho</span>
                        </a>
                    </li><!-- thong ke -->
                     <li>
                        <a href="index.php?statistic" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:22px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:22px">thong ke</span>
                        </a>
                    </li>
                       <hr class="dropdown-divider">
                    <li>
                    <li class="me-auto">
                        <a href="signout.php" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:26px" class="me-2"><i class="fas fa-sign-out-alt"></i></span>
                            <span style="font-size:26px">Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </nav>                        
        </div>
    </div>