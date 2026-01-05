<?php
// admin area - simple notifications list
require_once __DIR__ . '/includes/nav.php';
// nav.php already included DB

// Detect schema variations: id vs notify_id, is_admin presence
$notif_id_col = 'id';
$cid = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'id'");
if(!($cid && mysqli_num_rows($cid) > 0)){
  $cid2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'notify_id'");
  if($cid2 && mysqli_num_rows($cid2) > 0) $notif_id_col = 'notify_id';
}

$col_check = "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'";
$run_col_check = @mysqli_query($conn, $col_check);
$has_is_admin = ($run_col_check && mysqli_num_rows($run_col_check) > 0);

// mark single as read
if(isset($_POST['mark_read'])){
  $nid = intval($_POST['nid']);
  if($has_is_admin){
    $update = "UPDATE notifications SET is_read = 1 WHERE `$notif_id_col` = '$nid' AND is_admin = 1";
  } else {
    $update = "UPDATE notifications SET is_read = 1 WHERE `$notif_id_col` = '$nid'";
  }
  @mysqli_query($conn, $update);
}
// mark all as read
if(isset($_POST['mark_all'])){
  if($has_is_admin){
    $update = "UPDATE notifications SET is_read = 1 WHERE is_admin = 1";
  } else {
    // fallback: mark all notifications as read
    $update = "UPDATE notifications SET is_read = 1";
  }
  @mysqli_query($conn, $update);
}

$get_notifications = null;
// optional date filters from GET (format: YYYY-MM-DD)
$start_date = isset($_GET['notif_start']) ? trim($_GET['notif_start']) : '';
$end_date = isset($_GET['notif_end']) ? trim($_GET['notif_end']) : '';
$date_cond = '';
// validate simple YYYY-MM-DD and build SQL conditions
if(!empty($start_date)){
  $d = DateTime::createFromFormat('Y-m-d', $start_date);
  if($d && $d->format('Y-m-d') === $start_date){
    $date_cond .= " AND created_at >= '".mysqli_real_escape_string($conn, $start_date)." 00:00:00'";
  }
}
if(!empty($end_date)){
  $d2 = DateTime::createFromFormat('Y-m-d', $end_date);
  if($d2 && $d2->format('Y-m-d') === $end_date){
    $date_cond .= " AND created_at <= '".mysqli_real_escape_string($conn, $end_date)." 23:59:59'";
  }
}
// prefer to show notifications that target customers (have user_id or customer_id)
// BUT ALSO show admin notifications (is_admin=1) like contact form submissions
$has_user_col = false;
$rcu = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
if($rcu && mysqli_num_rows($rcu) > 0) $has_user_col = 'user_id';
else {
  $rcu2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
  if($rcu2 && mysqli_num_rows($rcu2) > 0) $has_user_col = 'customer_id';
}

if($has_user_col && $has_is_admin){
  // show notifications that were sent to customers OR admin notifications (contact, etc)
  $get_notifications = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL OR is_admin = 1" . $date_cond . " ORDER BY created_at DESC";
} else if($has_user_col){
  // show notifications that were sent to customers
  $get_notifications = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL" . $date_cond . " ORDER BY created_at DESC";
} else {
  // fallback: if there's an is_admin flag, show admin notifications
  if($has_is_admin){
    $get_notifications = "SELECT * FROM notifications WHERE is_admin = 1" . $date_cond . " ORDER BY created_at DESC";
  } else {
    $get_notifications = "SELECT * FROM notifications" . $date_cond . " ORDER BY created_at DESC";
  }
}
$run_notifications = @mysqli_query($conn, $get_notifications);
?>
<div class="container mt-5 pt-4">
  <h3>Thông báo (Admin)</h3>
  <div class="mb-3 d-flex align-items-center gap-3">
    <a href="index.php?notifications_add" class="btn btn-sm btn-success">Tạo thông báo cho khách hàng</a>
    <form method="get" class="d-inline ms-2">
      <input type="hidden" name="notifications" value="1">
      <div class="input-group input-group-sm">
        <input type="date" name="notif_start" class="form-control" value="<?php echo htmlspecialchars(isset(
          
          
          
          
          
          $_GET['notif_start']) ? $_GET['notif_start'] : ''); ?>" placeholder="Từ ngày">
        <input type="date" name="notif_end" class="form-control" value="<?php echo htmlspecialchars(isset($_GET['notif_end']) ? $_GET['notif_end'] : ''); ?>" placeholder="Đến ngày">
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-outline-secondary" href="index.php?notifications">Xóa lọc</a>
      </div>
    </form>
  </div>
  <form method="post">
    <button name="mark_all" class="btn btn-sm btn-primary mb-3">Đánh dấu tất cả đã đọc</button>
  </form>
  <div class="list-group">
    <?php
      if(!$run_notifications || mysqli_num_rows($run_notifications) == 0){
        echo "<p>Không có thông báo.</p>";
      } else {
        while($row = mysqli_fetch_array($run_notifications)){
          // support multiple schema variants
          $id = isset($row['id']) ? $row['id'] : (isset($row['notify_id']) ? $row['notify_id'] : '');
          $title = isset($row['title']) ? $row['title'] : '';
          $message = isset($row['message']) ? $row['message'] : (isset($row['content']) ? $row['content'] : '');
          $is_read = isset($row['is_read']) ? intval($row['is_read']) : 0;
          $created = isset($row['created_at']) ? $row['created_at'] : (isset($row['created']) ? $row['created'] : '');
          $type = isset($row['type']) ? $row['type'] : '';
          $is_admin = isset($row['is_admin']) ? intval($row['is_admin']) : 0;
          $badge = $is_read ? '' : ' <span class="badge bg-danger">Mới</span>';
          
          // Add badge for contact notifications
          $type_badge = '';
          if($type === 'contact'){
            $type_badge = ' <span class="badge bg-info">Liên hệ</span>';
          }
          
          echo "<div class='list-group-item mb-2'>";
          echo "<div class='d-flex w-100 justify-content-between'>";
          echo "<h5 class='mb-1'>".htmlspecialchars($title, ENT_QUOTES, 'UTF-8')." $badge $type_badge</h5>";
          echo "<small>".htmlspecialchars($created, ENT_QUOTES, 'UTF-8')."</small>";
          echo "</div>";
          // show recipient if available
          if(!empty($has_user_col) && isset($row[$has_user_col]) && $row[$has_user_col]){
            $recipient_id = intval($row[$has_user_col]);
            $recipient_name = '';
            $rc = @mysqli_query($conn, "SELECT customer_name FROM customer WHERE customer_id = '$recipient_id' LIMIT 1");
            if($rc && mysqli_num_rows($rc) > 0){
              $rrow = mysqli_fetch_assoc($rc);
              $recipient_name = $rrow['customer_name'];
            }
            echo "<div class='small text-muted mb-1'>Gửi tới: ".htmlspecialchars(($recipient_name ?: 'ID '.$recipient_id), ENT_QUOTES, 'UTF-8')."</div>";
          }
          // show admin notification indicator
          if($is_admin && $type === 'contact'){
            echo "<div class='small text-muted mb-1'><i class='bi bi-envelope-exclamation'></i> Thông báo liên hệ từ khách hàng</div>";
          }
          echo "<p class='mb-1'>".nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'))."</p>";
          if(!$is_read){
            echo "<form method='post' class='d-inline'>";
            echo "<input type='hidden' name='nid' value='".htmlspecialchars($id, ENT_QUOTES, 'UTF-8')."'>";
            echo "<button name='mark_read' class='btn btn-sm btn-success'>Đánh dấu đã đọc</button>";
            echo "</form>";
          }
          echo "</div>";
        }
      }
    ?>
  </div>
</div>
