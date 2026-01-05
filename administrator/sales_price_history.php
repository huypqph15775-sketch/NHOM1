<?php
include('includes/database.php');

// Ensure admin
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) die('Bạn không có quyền truy cập!');

// Query sold data: join customer_orders + customer_order_products + products
// Some installations may not have `unit_price` column in customer_order_products
// (older databases). Detect at runtime and adapt the query to avoid SQL errors.
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM customer_order_products LIKE 'unit_price'");
$has_unit_price = ($colCheck && mysqli_num_rows($colCheck) > 0);

// Build base select (adapt to presence of unit_price)
if ($has_unit_price) {
  $select_sql = "SELECT cop.order_id, co.order_date, cop.product_id, p.product_name, cop.color, cop.quantity, cop.unit_price, (cop.unit_price * cop.quantity) AS line_total, co.customer_id\n";
  $from_sql = "FROM customer_order_products cop\nINNER JOIN customer_orders co ON cop.order_id = co.order_id\nLEFT JOIN products p ON cop.product_id = p.product_id\n";
} else {
  $select_sql = "SELECT cop.order_id, co.order_date, cop.product_id, p.product_name, cop.color, cop.quantity, (SELECT pi.product_price FROM product_img pi WHERE pi.product_id = cop.product_id LIMIT 1) AS unit_price, ((SELECT pi2.product_price FROM product_img pi2 WHERE pi2.product_id = cop.product_id LIMIT 1) * cop.quantity) AS line_total, co.customer_id\n";
  $from_sql = "FROM customer_order_products cop\nINNER JOIN customer_orders co ON cop.order_id = co.order_id\nLEFT JOIN products p ON cop.product_id = p.product_id\n";
}

// Base where (only delivered/processing)
$where = ["co.status IN ('Đã giao','Đang giao')"];

// Filters from GET: from_date, to_date, product_name
$from_date_raw = trim($_GET['from_date'] ?? '');
$to_date_raw = trim($_GET['to_date'] ?? '');
$product_name_raw = trim($_GET['product_name'] ?? '');
if ($from_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date_raw)) {
  $from_date = $from_date_raw . ' 00:00:00';
  $where[] = "co.order_date >= '" . mysqli_real_escape_string($conn, $from_date) . "'";
}
if ($to_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date_raw)) {
  $to_date = $to_date_raw . ' 23:59:59';
  $where[] = "co.order_date <= '" . mysqli_real_escape_string($conn, $to_date) . "'";
}
if ($product_name_raw !== '') {
  $pn = mysqli_real_escape_string($conn, $product_name_raw);
  $where[] = "p.product_name LIKE '%" . $pn . "%'";
}

$sql = $select_sql . $from_sql;
if (!empty($where)) {
  $sql .= "WHERE " . implode(' AND ', $where) . "\n";
}
$sql .= "ORDER BY co.order_date DESC, cop.order_id DESC\nLIMIT 1000";

$res = mysqli_query($conn, $sql);
$sql_error_sales = '';
if ($res === false) {
  $sql_error_sales = mysqli_error($conn);
}
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Lịch sử giá bán (dựa trên dữ liệu bán hàng)</h5>
    <div>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModalSales">🔍 Lọc</button>
    </div>
  </div>
  <div class="card-body">
    <?php if (!empty($sql_error_sales)): ?>
      <div class="alert alert-danger">Lỗi truy vấn: <?php echo htmlspecialchars($sql_error_sales); ?></div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>#</th>
            <th>Ngày</th>
            <th>Đơn hàng</th>
            <th>Sản phẩm</th>
            <th>Màu</th>
            <th>Số lượng</th>
            <th>Giá bán (đơn vị)</th>
            <th>Tổng</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($res && mysqli_num_rows($res) > 0): $i=1; while($r = mysqli_fetch_assoc($res)): ?>
          <tr class="text-center">
            <td><?php echo $i++; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($r['order_date'])); ?></td>
            <td>#<?php echo (int)$r['order_id']; ?></td>
            <td><?php echo htmlspecialchars($r['product_name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['color'] ?? '-'); ?></td>
            <td><?php echo (int)$r['quantity']; ?></td>
            <td><?php echo $r['unit_price'] ? number_format((int)$r['unit_price'], 0, ',', '.') . ' đ' : '-'; ?></td>
            <td><?php echo $r['line_total'] ? number_format((int)$r['line_total'], 0, ',', '.') . ' đ' : '-'; ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="8" class="text-center">Chưa có dữ liệu bán hàng.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

    <!-- Modal Lọc for Sales -->
    <div class="modal fade" id="filterModalSales" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Lọc lịch sử bán</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form method="GET" action="">
            <input type="hidden" name="sales_price_history" value="1">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($_GET['from_date'] ?? ''); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($_GET['to_date'] ?? ''); ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Sản phẩm</label>
                <input type="text" name="product_name" class="form-control" placeholder="Tên sản phẩm" value="<?php echo htmlspecialchars($_GET['product_name'] ?? ''); ?>">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              <button type="submit" class="btn btn-primary">Lọc</button>
              <a href="index.php?sales_price_history" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
