<?php
include("includes/database.php");

// Kiểm tra xem user có phải admin và có đúng level (lv2 hoặc lv4+)
if(!isset($_SESSION['admin_id'])) {
  die("Bạn không có quyền truy cập!");
}
$user_level = (int)($_SESSION['admin_level'] ?? 0);
if (!($user_level == 2 || $user_level >= 4)) {
  die("Bạn không có quyền truy cập!");
}

// Lấy lịch sử giá (an toàn với nhiều schema)
// Kiểm tra các cột tuỳ chọn trên bảng price_history và xây dựng truy vấn tương ứng
$optional_cols = [
  'old_discount_price', 'new_discount_price', 'change_reason', 'changed_by', 'product_color_id', 'product_color_img_id'
];
$existing = [];
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM price_history");
if ($colRes) {
  while ($c = mysqli_fetch_assoc($colRes)) {
    $existing[] = $c['Field'];
  }
}

$select_extra = [];
foreach ($optional_cols as $oc) {
  if (in_array($oc, $existing)) {
    $select_extra[] = "ph." . $oc;
  }
}

$select_extra_sql = '';
if (!empty($select_extra)) {
  $select_extra_sql = ",\n    " . implode(",\n    ", $select_extra);
}

// Joins based on available columns
$join_admin = in_array('changed_by', $existing);
$join_pi = in_array('product_color_img_id', $existing);
$join_pc = in_array('product_color_id', $existing);

$sql = "\n  SELECT \n    ph.id AS history_id,\n    ph.product_id,\n    ph.old_price,\n    ph.new_price,\n    ph.changed_at" . $select_extra_sql . "\n  FROM price_history ph\n  LEFT JOIN products p ON ph.product_id = p.product_id";

if ($join_admin) {
  $sql .= "\n  LEFT JOIN admin a ON ph.changed_by = a.admin_id";
}
if ($join_pi) {
  $sql .= "\n  LEFT JOIN product_img pi ON ph.product_color_img_id = pi.product_color_img_id";
}
if ($join_pc) {
  $sql .= "\n  LEFT JOIN product_color pc ON ph.product_color_id = pc.product_color_id";
}

// --- Filtering: read GET params and safely build WHERE clauses ---
$where = [];
// sanitize inputs
$from_date_raw = trim($_GET['from_date'] ?? '');
$to_date_raw = trim($_GET['to_date'] ?? '');
$product_name_raw = trim($_GET['product_name'] ?? '');

// validate date format YYYY-MM-DD
if ($from_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date_raw)) {
  $from_date = $from_date_raw . ' 00:00:00';
  $where[] = "ph.changed_at >= '" . mysqli_real_escape_string($conn, $from_date) . "'";
}
if ($to_date_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date_raw)) {
  $to_date = $to_date_raw . ' 23:59:59';
  $where[] = "ph.changed_at <= '" . mysqli_real_escape_string($conn, $to_date) . "'";
}
if ($product_name_raw !== '') {
  $pn = mysqli_real_escape_string($conn, $product_name_raw);
  // Ensure products table is joined (it is), then filter by product name
  $where[] = "p.product_name LIKE '%" . $pn . "%'";
}

if (!empty($where)) {
  $sql .= "\n  WHERE " . implode(' AND ', $where);
}

$sql .= "\n  ORDER BY ph.changed_at DESC\n  LIMIT 500\n";

$result = mysqli_query($conn, $sql);
$sql_error = '';
if ($result === false) {
  $sql_error = mysqli_error($conn);
}
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">📊 Lịch sử thay đổi giá bán</h5>
    <div>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">🔍 Lọc</button>
    </div>
  </div>
  <div class="card-body">
    <?php if (!empty($sql_error)): ?>
      <div class="alert alert-danger">Lỗi truy vấn: <?php echo htmlspecialchars($sql_error); ?></div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th style="width: 5%">#</th>
            <th style="width: 8%">Ảnh</th>
            <th style="width: 20%">Sản phẩm</th>
            <th style="width: 10%">Màu</th>
            <th style="width: 12%">Giá cũ (VNĐ)</th>
            <th style="width: 12%">Giá mới (VNĐ)</th>
            <th style="width: 12%">Giá KM cũ (VNĐ)</th>
            <th style="width: 12%">Giá KM mới (VNĐ)</th>
            <th style="width: 20%">Lý do</th>
            <th style="width: 15%">Thời gian</th>
            <th style="width: 12%">Người thay đổi</th>
          </tr>
        </thead>
        <tbody>
        <?php if($result && mysqli_num_rows($result) > 0): $i = 1; ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?php echo $i++; ?></td>
              <td class="text-center">
                <?php if(!empty($row['product_color_img'] ?? '')): ?>
                  <img src="product_img/<?php echo htmlspecialchars($row['product_color_img']); ?>" width="70px" style="object-fit: contain;" alt="">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($row['product_name'] ?? '-'); ?></strong>
                <br><small class="text-muted">Mã: <?php echo (int)($row['product_id'] ?? 0); ?></small>
              </td>
              <td class="text-center">
                <span class="badge bg-info"><?php echo htmlspecialchars($row['product_color_name'] ?? '-'); ?></span>
              </td>
              <td class="text-end">
                <?php if((int)($row['old_price'] ?? 0) > 0): ?>
                  <del class="text-muted"><?php echo number_format((int)($row['old_price'] ?? 0)); ?></del>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <strong class="text-success"><?php echo number_format((int)($row['new_price'] ?? 0)); ?></strong>
              </td>
              <td class="text-end">
                <?php if((int)($row['old_discount_price'] ?? 0) > 0): ?>
                  <del class="text-muted"><?php echo number_format((int)($row['old_discount_price'] ?? 0)); ?></del>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <?php if((int)($row['new_discount_price'] ?? 0) > 0): ?>
                  <strong class="text-info"><?php echo number_format((int)($row['new_discount_price'] ?? 0)); ?></strong>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <small><?php echo htmlspecialchars($row['change_reason'] ?? '-'); ?></small>
              </td>
              <td class="text-center">
                <small><?php echo !empty($row['changed_at']) ? date('d/m/Y H:i', strtotime($row['changed_at'])) : '-'; ?></small>
              </td>
              <td class="text-center">
                <small><?php echo htmlspecialchars($row['admin_name'] ?? '-'); ?></small>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="11" class="text-center text-muted py-4">Chưa có lịch sử thay đổi giá.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Lọc -->
<div class="modal fade" id="filterModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lọc lịch sử giá</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="GET" action="">
        <input type="hidden" name="price_history" value="1">
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
          <a href="index.php?price_history" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
      </form>
    </div>
  </div>
</div>
