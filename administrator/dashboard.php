<?php
// Đảm bảo session đã được khởi tạo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

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

// Đếm số chương trình khuyến mãi (sử dụng bảng `vouchers`)
$select_promotions = "SELECT COUNT(*) AS total_promotions FROM vouchers WHERE status = 'active'";
$result_promotions = mysqli_query($conn, $select_promotions);
if (!$result_promotions) {
    error_log('Dashboard promotions query error: ' . mysqli_error($conn));
    $count_discount = 0;
} else {
    $row_promotions = mysqli_fetch_assoc($result_promotions);
    $count_discount = $row_promotions['total_promotions'] ?? 0;
}

// Tổng số lượng sản phẩm tồn kho (từ bảng product_img)
// Sử dụng COALESCE để trả về 0 nếu NULL, và thêm kiểm tra lỗi để dễ debug
$select_stock = "SELECT COALESCE(SUM(product_quantity), 0) AS total_stock FROM product_img";
$result_stock = mysqli_query($conn, $select_stock);
if (!$result_stock) {
    error_log('Dashboard stock query error: ' . mysqli_error($conn));
    $total_stock = 0;
} else {
    $row_stock = mysqli_fetch_assoc($result_stock);
    $total_stock = (int) ($row_stock['total_stock'] ?? 0);
}

// Đếm số đơn hàng đã hoàn tất
$select_completed = "SELECT COUNT(DISTINCT order_no) AS total_completed 
                     FROM customer_orders 
                     WHERE status = 'Đã giao'";
$result_completed = mysqli_query($conn, $select_completed);
$row_completed = mysqli_fetch_assoc($result_completed);
$count_completed = $row_completed['total_completed'] ?? 0;

// ================== DỮ LIỆU BIỂU ĐỒ ĐỘNG ==================
// Thống kê số lượng & doanh thu theo danh mục (hãng)
$chart_labels = [];
$chart_quantities = [];
$chart_revenues = [];

// Lấy dữ liệu từ các đơn hàng đã giao
$sql_chart = "SELECT c.cartegory_name AS brand,
                     SUM(cop.quantity) AS total_quantity,
                     SUM(
                        cop.quantity * 
                        IF(
                            pi.product_price_des IS NOT NULL AND pi.product_price_des > 0,
                            pi.product_price_des,
                            pi.product_price
                        )
                     ) AS total_revenue
              FROM customer_order_products AS cop
              INNER JOIN customer_orders AS co ON cop.order_id = co.order_id
              INNER JOIN products AS p ON cop.product_id = p.product_id
              INNER JOIN cartegory AS c ON p.cartegory_id = c.cartegory_id
              LEFT JOIN product_color AS pc ON pc.product_color_name = cop.color
              LEFT JOIN product_img AS pi ON pi.product_id = cop.product_id 
                                           AND pi.product_color_id = pc.product_color_id
              WHERE co.status != 'Đã hủy'
              GROUP BY c.cartegory_name
              ORDER BY total_quantity DESC";

$result_chart = mysqli_query($conn, $sql_chart);
if ($result_chart) {
    while ($row_chart = mysqli_fetch_assoc($result_chart)) {
        $chart_labels[] = $row_chart['brand'];
        $chart_quantities[] = (int) ($row_chart['total_quantity'] ?? 0);
        $chart_revenues[] = (float) ($row_chart['total_revenue'] ?? 0);
    }
}


?>

<div class="row">
    <div class="col-md-12 fw-bold fs-3">Trang chủ</div>

    <div class="row mt-5">

      

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

<div class="row mt-4">
    <!-- Biểu đồ cột: Số lượng bán ra -->
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Số lượng bán ra</h5>
                <div style="height:320px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ tròn: Doanh thu -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Doanh thu</h5>
                <div style="height:320px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu động lấy từ PHP
    const brandLabels   = <?php echo json_encode($chart_labels, JSON_UNESCAPED_UNICODE); ?>;
    const soldByBrand   = <?php echo json_encode($chart_quantities, JSON_NUMERIC_CHECK); ?>;
    const revenueByBrand = <?php echo json_encode($chart_revenues, JSON_NUMERIC_CHECK); ?>;

    // Biểu đồ cột - Số lượng bán ra
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx && brandLabels.length > 0) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: brandLabels,
                datasets: [{
                    label: 'Số lượng bán ra',
                    data: soldByBrand,
                    borderWidth: 1,
                    backgroundColor: [
                        '#60a5fa',
                        '#a3e635',
                        '#22d3ee',
                        '#c084fc',
                        '#4ade80',
                        '#fb7185'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: { legend: { display: true } }
            }
        });
    }

    // Biểu đồ tròn - Doanh thu
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx && brandLabels.length > 0) {
        new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: brandLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revenueByBrand,
                    backgroundColor: [
                        '#22d3ee',
                        '#a3e635',
                        '#fb7185',
                        '#f97316',
                        '#6366f1',
                        '#4ade80'
                    ],
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '65%'
            }
        });
    }
</script>
