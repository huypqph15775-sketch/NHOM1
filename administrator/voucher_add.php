<?php
include("includes/database.php");

$error = "";
$success = "";

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
        $check = mysqli_query($conn, "SELECT voucher_id FROM vouchers WHERE code='$code_esc' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = "Mã đã tồn tại, vui lòng nhập mã khác.";
        } else {
            $start_sql = $start_date ? "'$start_date'" : "NULL";
            $end_sql   = $end_date ? "'$end_date'" : "NULL";

            $sql = "
                INSERT INTO vouchers
                    (code, discount_percent, discount_amount, quantity, min_order, max_discount, start_date, end_date, status)
                VALUES
                    ('$code_esc', '$discount_percent', '$discount_amount', '$quantity', '$min_order', '$max_discount', $start_sql, $end_sql, '$status')
            ";
            if (mysqli_query($conn, $sql)) {
                $success = "Thêm mã giảm giá thành công.";
            } else {
                $error = "Lỗi khi thêm mã: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Thêm mã giảm giá</h5>
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
        <input type="text" name="code" class="form-control" required value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm (%)</label>
          <input type="number" name="discount_percent" class="form-control" min="0" max="100" value="<?= (int)($_POST['discount_percent'] ?? 0) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm (VNĐ)</label>
          <input type="number" name="discount_amount" class="form-control" min="0" value="<?= (int)($_POST['discount_amount'] ?? 0) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Số lượt sử dụng</label>
          <input type="number" name="quantity" class="form-control" min="1" value="<?= (int)($_POST['quantity'] ?? 1) ?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
          <input type="number" name="min_order" class="form-control" min="0" value="<?= (int)($_POST['min_order'] ?? 0) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Giảm tối đa (VNĐ)</label>
          <input type="number" name="max_discount" class="form-control" min="0" value="<?= (int)($_POST['max_discount'] ?? 0) ?>">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Trạng thái</label>
          <select name="status" class="form-select">
            <option value="active" <?= (($_POST['status'] ?? '')=='active'?'selected':''); ?>>Active</option>
            <option value="inactive" <?= (($_POST['status'] ?? '')=='inactive'?'selected':''); ?>>Inactive</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày bắt đầu</label>
          <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày kết thúc</label>
          <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Lưu mã</button>
    </form>
  </div>
</div>
