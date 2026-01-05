<?php
include("includes/database.php");

// Kiểm tra xem user có phải admin và có đúng level (2 hoặc 4)
if(!isset($_SESSION['admin_id'])) {
  die("Bạn không có quyền truy cập!");
}
$user_level = (int)($_SESSION['admin_level'] ?? 0);
if (!($user_level === 2 || $user_level >= 4)) {
  die("Bạn không có quyền truy cập!");
}

// Lấy lịch sử giá nhập (movement_type = 'import') với hỗ trợ lọc
$where = ["sm.movement_type = 'import'"];

$from_date_raw = trim($_GET['from_date'] ?? '');
$to_date_raw = trim($_GET['to_date'] ?? '');
$product_name_raw = trim($_GET['product_name'] ?? '');
if ($from_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date_raw)) {
    $from_date = $from_date_raw . ' 00:00:00';
    $where[] = "sm.created_at >= '" . mysqli_real_escape_string($conn, $from_date) . "'";
}
if ($to_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date_raw)) {
    $to_date = $to_date_raw . ' 23:59:59';
    $where[] = "sm.created_at <= '" . mysqli_real_escape_string($conn, $to_date) . "'";
}
if ($product_name_raw !== '') {
    $pn = mysqli_real_escape_string($conn, $product_name_raw);
    $where[] = "p.product_name LIKE '%" . $pn . "%'";
}

$sql = "
  SELECT
    sm.movement_id,
    sm.product_color_img_id,
    sm.product_id,
    sm.product_color_id,
    sm.quantity,
    sm.import_price,
    sm.export_price,
    sm.notes,
    sm.created_at,
    a.admin_name,
    p.product_name,
    pc.product_color_name,
    pi.product_color_img
  FROM stock_movements sm
  LEFT JOIN admin a ON sm.created_by = a.admin_id
  LEFT JOIN product_img pi ON sm.product_color_img_id = pi.product_color_img_id
  LEFT JOIN products p ON sm.product_id = p.product_id
  LEFT JOIN product_color pc ON sm.product_color_id = pc.product_color_id
";

if (!empty($where)) {
    $sql .= "  WHERE " . implode(' AND ', $where) . "\n";
}

$sql .= "  ORDER BY sm.created_at DESC\n  LIMIT 500\n";

$result = mysqli_query($conn, $sql);
$sql_error_import = '';
if ($result === false) {
  $sql_error_import = mysqli_error($conn);
}
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">📥 Lịch sử giá nhập kho</h5>
    <div>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModalImport">🔍 Lọc</button>
    </div>
  </div>
  <div class="card-body">
    <?php if (!empty($sql_error_import)): ?>
      <div class="alert alert-danger">Lỗi truy vấn: <?php echo htmlspecialchars($sql_error_import); ?></div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th style="width: 5%">#</th>
            <th style="width: 8%">Ảnh</th>
            <th style="width: 25%">Sản phẩm</th>
            <th style="width: 10%">Màu</th>
            <th style="width: 8%">Số lượng</th>
            <th style="width: 12%">Giá nhập (VNĐ)</th>
            <th style="width: 12%">Giá xuất (VNĐ)</th>
            <th style="width: 15%">Ghi chú</th>
            <th style="width: 15%">Thời gian</th>
            <th style="width: 12%">Người thao tác</th>
          </tr>
        </thead>
        <tbody>
        <?php if($result && mysqli_num_rows($result) > 0): $i = 1; ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?php echo $i++; ?></td>
              <td class="text-center">
                <?php if(!empty($row['product_color_img'])): ?>
                  <img src="product_img/<?php echo htmlspecialchars($row['product_color_img']); ?>" width="70px" style="object-fit: contain;" alt="">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($row['product_name'] ?? '-'); ?></strong>
                <br><small class="text-muted">Mã: <?php echo (int)$row['product_id']; ?></small>
              </td>
              <td class="text-center">
                <span class="badge bg-info"><?php echo htmlspecialchars($row['product_color_name'] ?? '-'); ?></span>
              </td>
              <td class="text-center"><?php echo (int)$row['quantity']; ?></td>
              <td class="text-end"><?php echo $row['import_price'] > 0 ? number_format($row['import_price']) : '<span class="text-muted">-</span>'; ?></td>
              <td class="text-end"><?php echo $row['export_price'] > 0 ? number_format($row['export_price']) : '<span class="text-muted">-</span>'; ?></td>
              <td>
                <small><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></small>
              </td>
              <td class="text-center"><small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small></td>
              <td class="text-center"><small><?php echo htmlspecialchars($row['admin_name'] ?? '-'); ?></small></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center text-muted py-4">Chưa có lịch sử nhập kho.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Lọc -->
<div class="modal fade" id="filterModalImport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lọc lịch sử nhập kho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="GET" action="">
        <input type="hidden" name="import_history" value="1">
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
          <a href="index.php?import_history" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
      </form>
    </div>
  </div>
</div>
