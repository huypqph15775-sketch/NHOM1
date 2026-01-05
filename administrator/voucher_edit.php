<?php
require_once '../includes/auth.php';
checkAdminLogin();
// Chỉ admin cấp cao (level >= 4) mới được sửa voucher
checkPermission(4);

include("includes/database.php");

if (!isset($_GET['voucher_id'])) {
    echo "<script>window.open('index.php?voucher_list','_self')</script>";
    exit();
}

$voucher_id = (int)$_GET['voucher_id'];
$error = "";
$success = "";

// Lấy dữ liệu hiện tại (kèm tên khách nếu voucher được gán cho 1 khách cụ thể)
$sql_get = "SELECT v.*, c.customer_name AS allowed_customer_name, v.allowed_customer_id FROM vouchers v LEFT JOIN customer c ON v.allowed_customer_id = c.customer_id WHERE v.voucher_id = '$voucher_id' LIMIT 1";
$run_get = mysqli_query($conn, $sql_get);
if (!$run_get || mysqli_num_rows($run_get) == 0) {
    echo "<script>alert('Mã giảm giá không tồn tại');window.open('index.php?voucher_list','_self')</script>";
    exit();
}
$current = mysqli_fetch_assoc($run_get);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code             = trim($_POST['code'] ?? '');
    $discount_percent = (int)($_POST['discount_percent'] ?? 0);
    $discount_amount  = (int)($_POST['discount_amount'] ?? 0);
    $quantity         = (int)($_POST['quantity'] ?? 1);
    $min_order        = (int)($_POST['min_order'] ?? 0);
    $max_discount     = (int)($_POST['max_discount'] ?? 0);
    $start_date       = $_POST['start_date'] ?: null;
    $end_date         = $_POST['end_date'] ?: null;
    $status           = $_POST['status'] ?? 'active';

    if ($code === '') {
        $error = "Vui lòng nhập mã.";
    } elseif ($discount_percent <= 0 && $discount_amount <= 0) {
        $error = "Phải nhập giảm % hoặc giảm VNĐ > 0.";
    } else {
        $code_esc = mysqli_real_escape_string($conn, $code);

        // Kiểm tra trùng mã với voucher khác
        $check = mysqli_query($conn, "SELECT voucher_id FROM vouchers WHERE code='$code_esc' AND voucher_id <> '$voucher_id' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = "Mã đã tồn tại, vui lòng nhập mã khác.";
        } else {
            $start_sql = $start_date ? "'$start_date'" : "NULL";
            $end_sql   = $end_date ? "'$end_date'" : "NULL";

      // ensure column exists
      $col_check = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'allowed_customer_id'");
      if (!$col_check || mysqli_num_rows($col_check) == 0) {
        @mysqli_query($conn, "ALTER TABLE vouchers ADD COLUMN allowed_customer_id INT DEFAULT NULL");
      }

      $allowed_sql_frag = "";
      // clear assignment explicitly
      if (!empty($_POST['clear_assigned_customer'])) {
        $allowed_sql_frag = ", allowed_customer_id = NULL";
      }
      // assign random customer if requested
      if (!empty($_POST['assign_random_customer'])) {
        $res_rand = mysqli_query($conn, "SELECT customer_id FROM customer ORDER BY RAND() LIMIT 1");
        if ($res_rand && mysqli_num_rows($res_rand) > 0) {
          $r = mysqli_fetch_assoc($res_rand);
          $allowed_sql_frag = ", allowed_customer_id = '" . intval($r['customer_id']) . "'";
        }
      }

      $sql = "
        UPDATE vouchers SET
          code             = '$code_esc',
          discount_percent = '$discount_percent',
          discount_amount  = '$discount_amount',
          quantity         = '$quantity',
          min_order        = '$min_order',
          max_discount     = '$max_discount',
          start_date       = $start_sql,
          end_date         = $end_sql,
          status           = '$status' $allowed_sql_frag
        WHERE voucher_id = '$voucher_id'
        LIMIT 1
      ";
            if (mysqli_query($conn, $sql)) {
                $success = "Cập nhật mã giảm giá thành công.";
                // reload dữ liệu mới
                $run_get = mysqli_query($conn, "SELECT * FROM vouchers WHERE voucher_id = '$voucher_id' LIMIT 1");
                $current = mysqli_fetch_assoc($run_get);
            } else {
                $error = "Lỗi khi cập nhật: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Sửa mã giảm giá</h5>
    <a href="index.php?voucher_list" class="btn btn-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
  </div>
  <div class="card-body">
    <?php if($error): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label fw-bold">Mã giảm giá</label>
        <input type="text" name="code" class="form-control" required value="<?= htmlspecialchars($current['code']); ?>">
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm (%)</label>
          <input type="number" name="discount_percent" class="form-control" min="0" max="100" value="<?= (int)$current['discount_percent']; ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm (VNĐ)</label>
          <input type="number" name="discount_amount" class="form-control" min="0" value="<?= (int)$current['discount_amount']; ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Số lượt sử dụng</label>
          <input type="number" name="quantity" class="form-control" min="0" value="<?= (int)$current['quantity']; ?>">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Đã gán cho</label>
        <div>
          <?php if(!empty($current['allowed_customer_name'])): ?>
            <strong><?= htmlspecialchars($current['allowed_customer_name']); ?></strong>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" value="1" id="clear_assigned_customer" name="clear_assigned_customer">
              <label class="form-check-label" for="clear_assigned_customer">Bỏ gán (cho phép tất cả khách sử dụng lại)</label>
            </div>
          <?php else: ?>
            <em>Tất cả khách</em>
          <?php endif; ?>
        </div>
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" value="1" id="assign_random_customer" name="assign_random_customer">
          <label class="form-check-label" for="assign_random_customer">Gán ngẫu nhiên cho 1 khách hàng</label>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
          <input type="number" name="min_order" class="form-control" min="0" value="<?= (int)$current['min_order']; ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm tối đa (VNĐ)</label>
          <input type="number" name="max_discount" class="form-control" min="0" value="<?= (int)$current['max_discount']; ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Trạng thái</label>
          <select name="status" class="form-select">
            <option value="active" <?= ($current['status']=='active'?'selected':''); ?>>Active</option>
            <option value="inactive" <?= ($current['status']=='inactive'?'selected':''); ?>>Inactive</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày bắt đầu</label>
          <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($current['start_date']); ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày kết thúc</label>
          <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($current['end_date']); ?>">
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
  </div>
</div>
