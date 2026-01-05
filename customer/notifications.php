<?php
session_start();
include("../includes/database.php");
// simple check
if(!isset($_SESSION['customer_id'])){
  echo "<script>window.open('../signin.php', '_self')</script>";
  exit;
}
$customer_id = $_SESSION['customer_id'];

// mark single as read
if(isset($_POST['mark_read'])){
  $nid = intval($_POST['nid']);
  $update = "update notifications set is_read = 1 where id = '$nid' and user_id = '$customer_id'";
  mysqli_query($conn, $update);
}
// mark all as read
if(isset($_POST['mark_all'])){
  $update = "update notifications set is_read = 1 where user_id = '$customer_id'";
  mysqli_query($conn, $update);
}

$get_notifications = "select * from notifications where user_id = '$customer_id' order by created_at desc";
$run_notifications = mysqli_query($conn, $get_notifications);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Thông báo của tôi</title>
  <link rel="stylesheet" href="../css/index.css">
</head>
<body>
<?php $active = ''; include('../includes/header.php'); ?>
<div class="container mt-5 pt-4">
  <h3>Thông báo</h3>
  <form method="post">
    <button name="mark_all" class="btn btn-sm btn-primary mb-3">Đánh dấu tất cả đã đọc</button>
  </form>
  <div class="list-group">
    <?php
      // detect which user column exists (user_id or customer_id) to support older schema
      $user_col = null;
      $col_check = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
      if($col_check && mysqli_num_rows($col_check) > 0){ $user_col = 'user_id'; }
      else {
        $col_check2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
        if($col_check2 && mysqli_num_rows($col_check2) > 0){ $user_col = 'customer_id'; }
      }

      if(!$user_col){
        echo "<div class='alert alert-warning'>Không tìm thấy cột người dùng trong bảng thông báo (user_id hoặc customer_id).</div>";
      } else {
        // handle mark_read/mark_all updates using detected column
        if(isset($_POST['mark_read'])){
          $nid = intval($_POST['nid']);
          $update = "UPDATE notifications SET is_read = 1 WHERE id = '".intval($nid)."' AND `$user_col` = '".intval($customer_id)."'";
          @mysqli_query($conn, $update);
        }
        if(isset($_POST['mark_all'])){
          $update = "UPDATE notifications SET is_read = 1 WHERE `$user_col` = '".intval($customer_id)."'";
          @mysqli_query($conn, $update);
        }

        $sql = "SELECT * FROM notifications WHERE `$user_col` = '".intval($customer_id)."' ORDER BY created_at DESC";
        $run_notifications = @mysqli_query($conn, $sql);
        if(!$run_notifications){
          echo "<div class='alert alert-danger'>Lỗi truy vấn thông báo: ".htmlspecialchars(mysqli_error($conn))."</div>";
        } else {
          if(mysqli_num_rows($run_notifications) == 0){
            echo "<p>Không có thông báo.</p>";
          }
          while($row = mysqli_fetch_array($run_notifications)){
            $id = isset($row['id']) ? $row['id'] : (isset($row['notify_id']) ? $row['notify_id'] : '');
            $title = isset($row['title']) ? $row['title'] : '';
            $message = isset($row['message']) ? $row['message'] : (isset($row['content']) ? $row['content'] : '');
            $is_read = isset($row['is_read']) ? $row['is_read'] : 0;
            $created = isset($row['created_at']) ? $row['created_at'] : (isset($row['created']) ? $row['created'] : '');
            $badge = intval($is_read) ? '' : ' <span class="badge bg-danger">Mới</span>';
            echo "<div class='list-group-item mb-2'>";
            echo "<div class='d-flex w-100 justify-content-between'>";
            echo "<h5 class='mb-1'>".htmlspecialchars($title, ENT_QUOTES, 'UTF-8')." $badge</h5>";
            echo "<small>".htmlspecialchars($created, ENT_QUOTES, 'UTF-8')."</small>";
            echo "</div>";
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
      }
    ?>
  </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
