<?php
require_once '../includes/auth.php';
checkAdminLogin();
// Chỉ admin cấp cao (level >= 4) mới được thêm voucher
checkPermission(4);

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
    $start_date       = trim($_POST['start_date'] ?? '');
    $end_date         = trim($_POST['end_date'] ?? '');
    $status           = $_POST['status'] ?? 'active';

  // Enforce both start_date and end_date must be the current date to allow creation
  $today_date = date('Y-m-d');
  if ($start_date === '' || $start_date !== $today_date) {
    $error = "Ngày bắt đầu phải là ngày hiện tại ({$today_date}). Vui lòng chọn ngày hôm nay.";
  }
  // end_date may be today or a future date, but not a past date
  if (empty($error)) {
    if ($end_date === '') {
      $error = "Vui lòng chọn ngày kết thúc (ít nhất là ngày hôm nay).";
    } elseif ($end_date < $today_date) {
      $error = "Ngày kết thúc không thể là quá khứ. Vui lòng chọn ngày hôm nay hoặc tương lai.";
    }
  }

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
      // ensure optional column for per-customer restriction exists
      $col_check = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'allowed_customer_id'");
      if (!$col_check || mysqli_num_rows($col_check) == 0) {
        @mysqli_query($conn, "ALTER TABLE vouchers ADD COLUMN allowed_customer_id INT DEFAULT NULL");
      }

      $start_sql = $start_date ? "'$start_date'" : "NULL";
      $end_sql   = $end_date ? "'$end_date'" : "NULL";

      $allowed_customer_sql = "NULL";
      $assigned_customer_name = '';
      // if admin requests random assignment to a single customer, pick one
      if (!empty($_POST['assign_random_customer'])) {
        $res_rand = mysqli_query($conn, "SELECT customer_id, customer_name FROM customer ORDER BY RAND() LIMIT 1");
        if ($res_rand && mysqli_num_rows($res_rand) > 0) {
          $row_rand = mysqli_fetch_assoc($res_rand);
          $allowed_customer_sql = (int)$row_rand['customer_id'];
          $assigned_customer_name = mysqli_real_escape_string($conn, $row_rand['customer_name']);
        }
      }

      $sql = "
        INSERT INTO vouchers
          (code, discount_percent, discount_amount, quantity, min_order, max_discount, start_date, end_date, status, allowed_customer_id)
        VALUES
          ('$code_esc', '$discount_percent', '$discount_amount', '$quantity', '$min_order', '$max_discount', $start_sql, $end_sql, '$status', ".($allowed_customer_sql === "NULL" ? "NULL" : "'$allowed_customer_sql'").")
      ";
      if (mysqli_query($conn, $sql)) {
        $insert_id = mysqli_insert_id($conn);
        $success = "Thêm mã giảm giá thành công.";
        if ($assigned_customer_name && $allowed_customer_sql !== "NULL") {
          $success .= " Mã đã được gán ngẫu nhiên cho khách: $assigned_customer_name.";
          // Ensure notification helper is available
          if(!function_exists('add_notification')){
            // functions.php lives at ../functions/functions.php relative to this admin file
            $func_path = __DIR__ . '/../functions/functions.php';
            if(file_exists($func_path)) {
              require_once $func_path;
            } else {
              // fallback: try one level up (project root) then functions/functions.php
              $alt = __DIR__ . '/../..' . '/functions/functions.php';
              if(file_exists($alt)) require_once $alt;
            }
          }
          // send notification to the assigned customer (best-effort)
          $assigned_id = (int)$allowed_customer_sql;
          if($assigned_id > 0){
            $notif_title = 'Bạn nhận mã giảm giá dành riêng cho bạn';
            // plain-text message so it displays cleanly in customer notifications
            $end_display = $end_date ? $end_date : 'Không giới hạn';
            $notif_message = "Xin chúc mừng! Bạn nhận được mã giảm giá dành riêng cho tài khoản của bạn: " . $code_esc . ". Hạn sử dụng: " . $end_display . ". Vui lòng đăng nhập để sử dụng mã.";
            // Ensure add_notification exists before calling
            if(!function_exists('add_notification')){
              // try to include helper one more time
              $func_path = __DIR__ . '/../functions/functions.php';
              if(file_exists($func_path)) require_once $func_path;
            }
            if(function_exists('add_notification')){
              // call helper with plain text message (add_notification will escape/insert as needed)
              $ok = add_notification($assigned_id, 0, 'voucher', $notif_title, $notif_message, $insert_id);
              if(!$ok){
                // expose diagnostic to admin so they can see why notification didn't insert
                $db_err = isset($conn) ? mysqli_error($conn) : 'No DB connection';
                $success .= " (Không gửi thông báo — lỗi: ".htmlspecialchars($db_err, ENT_QUOTES, 'UTF-8').")";
                // Fallback: attempt a direct insert into notifications table to ensure delivery
                $notif_cols = [];
                $rc = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
                if($rc && mysqli_num_rows($rc) > 0) $notif_cols[] = 'user_id';
                $rc2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
                if($rc2 && mysqli_num_rows($rc2) > 0) $notif_cols[] = 'customer_id';
                // prefer user_id if available
                $target_col = in_array('user_id', $notif_cols) ? 'user_id' : (in_array('customer_id', $notif_cols) ? 'customer_id' : null);
                if($target_col){
                  $tcol = $target_col;
                  $safe_title = mysqli_real_escape_string($conn, $notif_title);
                  $safe_message = mysqli_real_escape_string($conn, $notif_message);
                  $rel = intval($insert_id);
                  $ins = "INSERT INTO notifications (`$tcol`, is_admin, `type`, `title`, `message`, related_id, is_read, created_at) VALUES ('".intval($assigned_id)."', 0, 'voucher', '".$safe_title."', '".$safe_message."', ".intval($rel).", 0, NOW())";
                  $rins = @mysqli_query($conn, $ins);
                  if($rins){
                    $success .= ' (Thông báo đã được lưu bằng phương thức thay thế)';
                  } else {
                    $success .= ' (Thử chèn thay thế thất bại: '.htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8').')';
                  }
                }
              } else {
                // verify the notification exists for this user (helps diagnose schema mismatches)
                $found = false;
                // detect recipient column (user_id or customer_id)
                $check_user_col = false;
                $rc = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
                if($rc && mysqli_num_rows($rc) > 0) $check_user_col = 'user_id';
                else {
                  $rc2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
                  if($rc2 && mysqli_num_rows($rc2) > 0) $check_user_col = 'customer_id';
                }
                if($check_user_col){
                  $ucol = $check_user_col;
                  $safe_title = mysqli_real_escape_string($conn, $notif_title);
                  // detect if related_id exists before including it in the WHERE
                  $rel_exists = false;
                  $rrel = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'related_id'");
                  if($rrel && mysqli_num_rows($rrel) > 0) $rel_exists = true;

                  if($rel_exists){
                    $q = "SELECT * FROM notifications WHERE `$ucol` = '".intval($assigned_id)."' AND (related_id = ".intval($insert_id)." OR title = '$safe_title') ORDER BY created_at DESC LIMIT 1";
                  } else {
                    // fallback: match by title only for older schemas
                    $q = "SELECT * FROM notifications WHERE `$ucol` = '".intval($assigned_id)."' AND title = '$safe_title' ORDER BY created_at DESC LIMIT 1";
                  }
                  $rq = @mysqli_query($conn, $q);
                  if($rq && mysqli_num_rows($rq) > 0){
                    $found = true;
                  }
                }

                if($found){
                  $success .= " (Thông báo đã gửi tới khách hàng)";
                } else {
                  // not found — give actionable diagnostic
                  $diag = 'Không tìm thấy bản ghi thông báo trong bảng `notifications` cho user_id=' . intval($assigned_id) . '.';
                  // include last DB error if any
                  $last = isset($conn) ? mysqli_error($conn) : '';
                  if($last) $diag .= ' Lỗi DB: '. $last;
                  // also list columns for admin visibility
                  $cols = [];
                  $cres = @mysqli_query($conn, "SHOW COLUMNS FROM notifications");
                  if($cres){ while($cc = mysqli_fetch_assoc($cres)){ $cols[] = $cc['Field']; } }
                  if(count($cols)) $diag .= ' Columns: '.implode(', ', $cols).'.';
                  $success .= ' (Thông báo có vẻ đã được gửi nhưng không tìm thấy trong DB. Chi tiết: '.htmlspecialchars($diag, ENT_QUOTES, 'UTF-8').')';
                  error_log('[voucher_add] notification verification failed for voucher_id='.$insert_id.' assigned_id='.intval($assigned_id).'. '.$diag);
                }
              }
            } else {
              $success .= ' (Không gửi thông báo — hàm add_notification không khả dụng)';
              // Try direct insert if helper is missing
              $rc = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
              $tcol = null;
              if($rc && mysqli_num_rows($rc) > 0) $tcol = 'user_id';
              else {
                $rc2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
                if($rc2 && mysqli_num_rows($rc2) > 0) $tcol = 'customer_id';
              }
              if($tcol){
                $safe_title = mysqli_real_escape_string($conn, $notif_title);
                $safe_message = mysqli_real_escape_string($conn, $notif_message);
                $ins = "INSERT INTO notifications (`$tcol`, is_admin, `type`, `title`, `message`, related_id, is_read, created_at) VALUES ('".intval($assigned_id)."', 0, 'voucher', '".$safe_title."', '".$safe_message."', ".intval($insert_id).", 0, NOW())";
                $rins = @mysqli_query($conn, $ins);
                if($rins){
                  $success .= ' (Thông báo đã được gửi bằng phương thức thay thế)';
                } else {
                  $success .= ' (Thử chèn thay thế thất bại: '.htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8').')';
                }
              }
            }
          }
        }
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

      <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="assign_random_customer" name="assign_random_customer" value="1" <?= !empty($_POST['assign_random_customer']) ? 'checked' : '' ?>>
        <label class="form-check-label" for="assign_random_customer">Gán ngẫu nhiên cho 1 khách hàng (chỉ khách này mới có thể sử dụng mã)</label>
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
          <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Ngày kết thúc</label>
          <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d')) ?>">
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Lưu mã</button>
    </form>
  </div>
</div>
