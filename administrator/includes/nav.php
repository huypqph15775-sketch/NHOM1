<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Kết nối DB & các hàm trong thư mục administrator
include("includes/database.php");
include("functions/functions.php");

// Auto-expire vouchers whose end_date has passed. This runs on admin page loads and
// moves vouchers from 'active' to 'expired' so they can't be used any more.
if (isset($conn)) {
    $today = date('Y-m-d');
    // Use a best-effort, suppressed query so missing table/columns won't break the UI
    @mysqli_query($conn, "UPDATE vouchers SET status = 'expired' WHERE status = 'active' AND end_date IS NOT NULL AND end_date <> '' AND DATE(end_date) < '$today'");
}

// Sử dụng hệ thống phân quyền chung
require_once __DIR__ . '/../../includes/auth.php';

// Bắt buộc phải là admin mới vào được khu administrator
checkAdminLogin();

// Lấy thông tin user hiện tại (admin đang đăng nhập)
$current_user = getCurrentUser();
// Normalize user/admin level: prefer explicit admin_level, fall back to role_level
$user_level = 0;
if (isset($_SESSION['admin_level'])) {
    $user_level = (int)$_SESSION['admin_level'];
} elseif (isset($_SESSION['role_level'])) {
    $user_level = (int)$_SESSION['role_level'];
} elseif (!empty($current_user['role_level'])) {
    $user_level = (int)$current_user['role_level'];
}
// Determine login_name early for UI rules (used to hide certain menu items)
$login_name = '';
if (!empty($_SESSION['admin_user_name'])) {
    $login_name = strtolower($_SESSION['admin_user_name']);
} elseif (!empty($current_user['user_name'])) {
    $login_name = strtolower($current_user['user_name']);
}
// Treat any username that starts with the known prefixes as that role
$is_nvbanhang = (strpos($login_name, 'nvbanhang') === 0);
$is_nvkho = (strpos($login_name, 'nvkho') === 0);
$hide_home = $is_nvbanhang || $is_nvkho;
?>

<!-- Hiển thị thông tin user -->



 <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="background-color: #66CC66">
        <div class="container-fluid" >
            <!-- offcanvas trigger -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                <span class="navbar-toggler-icon" data-bs-target="#offcanvasExample"></span>
            </button>
            <!--  -->
          <a class="navbar-brand fw-bold me-auto" href="index.php?dashboard">Phone Store</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            
            <!-- <form class="d-flex ms-auto my-4 my-lg-0 px-5">
                <div class="input-group">
                    <input style="background-color:lightgreen;" type="text" class="form-control" placeholder="Bạn tìm gì..." aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button style="background-color:gray;" class="btn btn-outline-secondary" type="button" id="button-addon2">
                        <i style="color:yellow;" class="fas fa-search"></i>
                    </button>
                </div>
            </form> -->

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
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
        <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasExampleLabel">
                <?php
                    // Determine a role-aware label and a small CSS class for color
                    $logo_label = 'Admin';
                    $logo_class = 'role-admin';
                    if (!empty($_SESSION['admin_user_name'])) {
                        $login_name = strtolower($_SESSION['admin_user_name']);
                        if (strpos($login_name, 'nvbanhang') === 0) {
                            $logo_label = 'Nhân Viên Bán Hàng';
                            $logo_class = 'role-sales';
                        } elseif (strpos($login_name, 'nvkho') === 0) {
                            $logo_label = 'Nhân Viên Kho';
                            $logo_class = 'role-warehouse';
                        }
                    } else {
                        if (!empty($current_user['role_name'])) {
                            $r = mb_strtolower($current_user['role_name'], 'UTF-8');
                            if (strpos($r, 'bán') !== false || strpos($r, 'bán hàng') !== false) {
                                $logo_label = 'Nhân Viên Bán Hàng';
                                $logo_class = 'role-sales';
                            } elseif (strpos($r, 'kho') !== false) {
                                $logo_label = 'Nhân Viên Kho';
                                $logo_class = 'role-warehouse';
                            }
                        }
                    }
                ?>

                <div class="sidebar-logo">
                    <img src="images/smartphone.png" alt="" class="sidebar-logo-img">
                    <span class="logo_name <?php echo htmlspecialchars($logo_class); ?> fw-bold fs-3"><?php echo htmlspecialchars($logo_label); ?></span>
                </div>
            </h5>
        </div>
    <div class="offcanvas-body">
            <nav class="navbar-dark">
                <ul class="navbar-nav">
                    <?php if (empty($hide_home) || !$hide_home): ?>
                    <li>
                        <a href="index.php?dashboard" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-home"></i></span>
                            <span style="font-size:20px">Trang chủ</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($user_level >= 4 && !$is_nvkho): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-trademark"></i></span>
                            <span style="font-size:20px">Thương hiệu</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse2">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?cartegory_add" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?cartegory_list" class="nav-link px-3">
                                            <span style="font-size:17px">Danh sách thương hiệu</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse3" role="button" aria-expanded="false" aria-controls="collapse3">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-phone-alt"></i></span>
                            <span style="font-size:20px">Điện thoại</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse3">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?product_add" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?product_add_img" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm điện thoại theo</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?product_list" class="nav-link px-3">
                                            <span style="font-size:17px">Danh sách điện thoại</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse5" role="button" aria-expanded="false" aria-controls="collapse5">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-palette"></i></span>
                            <span style="font-size:20px">Màu sắc</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse5">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?color_add" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm mới</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?color_list" class="nav-link px-3">
                                            <span style="font-size:17px">Danh sách Màu</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a href="index.php?pending_orders" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:20px">Đơn hàng</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a href="index.php?notifications" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-bell"></i></span>
                            <span style="font-size:20px">Thông báo</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a href="index.php?chat_messages" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-comments"></i></span>
                            <span style="font-size:20px">Tin nhắn Chatbox</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a href="index.php?customer_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-users"></i></i></span>
                            <span style="font-size:20px">Khách hàng</span>
                        </a>
                    </li>
                    <?php endif; ?>
                   
                    <?php if ($user_level >= 4 && !$is_nvkho): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapse1" role="button" aria-expanded="false" aria-controls="collapse1">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-user-cog"></i></span>
                            <span style="font-size:20px">Admin</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapse1">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?admin_add" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm mới admin</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?admin_list" class="nav-link px-3">
                                            <span style="font-size:17px">Danh sách Admin</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li>
                     
                    </li>
                    <!-- khuyenmai -->
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <?php if ($user_level >= 4): ?>
                        <a href="index.php?voucher_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="far fa-money-bill-alt"></i></span>
                            <span style="font-size:20px">Khuyến mãi</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>

                    <!-- Kho (chỉ lv2 và lv4 trở lên) -->
                    <li>
                        <?php if ($user_level == 2 || $user_level >= 4 || $is_nvkho): ?>
                        <a href="index.php?stock_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-boxes"></i></span>
                            <span style="font-size:20px">Kho</span>
                        </a>
                        <?php endif; ?>
                    </li>

                    <!-- Quản lý giá bán -->
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <?php if ($user_level >= 4): ?>
                        <a href="index.php?manage_price" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-tag"></i></span>
                            <span style="font-size:20px">Quản lý giá</span>
                        </a>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>

                    <!-- Lịch sử giá (gộp: thay đổi, bán ra, nhập) -->
                    <?php if (!$is_nvkho): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapsePriceHistory" role="button" aria-expanded="false" aria-controls="collapsePriceHistory">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-history"></i></span>
                            <span style="font-size:20px">Lịch sử giá</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePriceHistory">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <?php if ($user_level == 2 || $user_level >= 4): ?>
                                    <li>
                                        <a href="index.php?price_history" class="nav-link px-3">
                                            <span style="font-size:18px">Thay đổi giá (Hệ thống)</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php if ($user_level >= 3): ?>
                                    <li>
                                        <a href="index.php?sales_price_history" class="nav-link px-3">
                                            <span style="font-size:18px">Lịch sử bán (Bán ra)</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>

                                    <?php if ($user_level == 2 || $user_level >= 4): ?>
                                    <li>
                                        <a href="index.php?import_history" class="nav-link px-3">
                                            <span style="font-size:18px">Lịch sử giá nhập</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php else: ?>
                    <!-- nvkho: show a compact price-history group with only the system & import history links -->
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapsePriceHistory" role="button" aria-expanded="false" aria-controls="collapsePriceHistory">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-history"></i></span>
                            <span style="font-size:20px">Lịch sử giá</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapsePriceHistory">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?price_history" class="nav-link px-3">
                                            <span style="font-size:18px">Thay đổi giá (Hệ thống)</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?import_history" class="nav-link px-3">
                                            <span style="font-size:18px">Lịch sử giá nhập</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <!-- TIN TỨC (ẩn cho nhân viên kho level 2) -->
                    <?php if ($user_level !== 2): ?>
                    <li>
                        <a class="nav-link px-3 active sidebar-link" data-bs-toggle="collapse" href="#collapseNews" role="button" aria-expanded="false" aria-controls="collapseNews">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-newspaper"></i></span>
                            <span style="font-size:20px">Tin tức</span>
                            <span class="right-icon ms-auto"><i class="fas fa-chevron-down"></i></span>
                        </a>
                        <div class="collapse" id="collapseNews">
                            <div>
                                <ul class="navbar-nav ps-3 ">
                                    <li>
                                        <a href="index.php?news_add" class="nav-link px-3">
                                            <span style="font-size:17px">Thêm bài viết</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?news_list" class="nav-link px-3">
                                            <span style="font-size:17px">Danh sách tin tức</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?news_comments" class="nav-link px-3">
                                            <span style="font-size:17px">Quản lý bình luận</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                        <!-- Quản lý mail (chỉ admin, không phải nvkho) -->
                        <?php if ($user_level >= 3 && !$is_nvkho): ?>
                        <li>
                            <a href="index.php?mailbox" class="nav-link px-3 active">
                                <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-envelope"></i></span>
                                <span style="font-size:20px">Quản lý mail</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                     <?php if ($user_level >= 3 && !$is_nvkho): ?>
                    <li>
                        <a href="index.php?reviews_list" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-star"></i></span>
                            <span style="font-size:20px">Đánh giá</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <!-- thong ke -->
                    <li>
                        <?php if ($user_level >= 3 && !$is_nvkho): ?>
                        <a href="index.php?statistic" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-chart-bar"></i></span>
                            <span style="font-size:20px">Thống kê</span>
                        </a>
                        <?php endif; ?>
                    </li>
                       <hr class="dropdown-divider">
                    <li>
                    <li class="me-auto">
                        <a href="signout.php" class="nav-link px-3 active">
                            <span style="color:#00FFFF;font-size:20px" class="me-2"><i class="fas fa-sign-out-alt"></i></span>
                            <span style="font-size:20px">Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </nav>                        
        </div>
    </div>