<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'includes/database.php';
include 'functions/functions.php';

$response = ['success' => false, 'message' => 'Unknown error', 'count' => 0];

if(!isset($_SESSION['customer_id'])){
  $response['message'] = 'Không đăng nhập';
  echo json_encode($response);
  exit;
}

$customer_id = $_SESSION['customer_id'];

// ensure notifications table exists
$check_table = "SHOW TABLES LIKE 'notifications'";
$run_check_table = @mysqli_query($conn, $check_table);
if(!$run_check_table || mysqli_num_rows($run_check_table) == 0){
  $response['message'] = 'Bảng notifications không tồn tại';
  echo json_encode($response);
  exit;
}

// detect user column
$notif_user_col = false;
$run_col_check = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
if($run_col_check && mysqli_num_rows($run_col_check) > 0){
  $notif_user_col = 'user_id';
} else {
  $run_col_check2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
  if($run_col_check2 && mysqli_num_rows($run_col_check2) > 0){
    $notif_user_col = 'customer_id';
  }
}

if(!$notif_user_col){
  $response['message'] = 'Không tìm thấy cột user cho notifications';
  echo json_encode($response);
  exit;
}

// detect is_admin column for filtering (only delete user-notifications)
$has_is_admin = false;
$run_col_check_is_admin = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'");
if($run_col_check_is_admin && mysqli_num_rows($run_col_check_is_admin) > 0){
  $has_is_admin = true;
}

// detect id column name
$nid_col = 'id';
$run_col_check_id = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'id'");
if(!($run_col_check_id && mysqli_num_rows($run_col_check_id) > 0)){
  // fallback to notify_id
  $run_col_check_notify = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'notify_id'");
  if($run_col_check_notify && mysqli_num_rows($run_col_check_notify) > 0){
    $nid_col = 'notify_id';
  } else {
    // if neither exists, we cannot delete by id
    $nid_col = false;
  }
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// helper: compute unread count
function get_unread_count($conn, $user_col, $customer_id, $has_is_admin){
  $is_admin_cond = $has_is_admin ? ' AND is_admin = 0' : '';
  // check is_read existence
  $run_col_is_read = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'is_read'");
  if(!($run_col_is_read && mysqli_num_rows($run_col_is_read) > 0)){
    return 0;
  }
  $sql = "SELECT COUNT(*) AS c FROM notifications WHERE `".$user_col."` = '".intval($customer_id)."' AND is_read = 0".$is_admin_cond;
  $res = @mysqli_query($conn, $sql);
  if($res){ $row = mysqli_fetch_assoc($res); return (int)$row['c']; }
  return 0;
}

if($action === 'delete'){
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  if(!$id || !$nid_col){
    $response['message'] = 'ID không hợp lệ hoặc không tìm được cột ID';
    echo json_encode($response);
    exit;
  }

  $is_admin_cond = $has_is_admin ? ' AND is_admin = 0' : '';
  $sql = "DELETE FROM notifications WHERE `".$nid_col."` = " . $id . " AND `".$notif_user_col."` = '".intval($customer_id)."'" . $is_admin_cond;
  $run = @mysqli_query($conn, $sql);
  if($run){
    $response['success'] = true;
    $response['message'] = 'Đã xóa';
    $response['count'] = get_unread_count($conn, $notif_user_col, $customer_id, $has_is_admin);
  } else {
    $response['message'] = 'Lỗi khi xóa: ' . mysqli_error($conn);
  }

  echo json_encode($response);
  exit;
}

if($action === 'delete_all'){
  $is_admin_cond = $has_is_admin ? ' AND is_admin = 0' : '';
  $sql = "DELETE FROM notifications WHERE `".$notif_user_col."` = '".intval($customer_id)."'" . $is_admin_cond;
  $run = @mysqli_query($conn, $sql);
  if($run){
    $response['success'] = true;
    $response['message'] = 'Đã xóa tất cả thông báo';
    $response['count'] = 0;
  } else {
    $response['message'] = 'Lỗi khi xóa tất cả: ' . mysqli_error($conn);
  }

  echo json_encode($response);
  exit;
}

$response['message'] = 'Hành động không hợp lệ';
echo json_encode($response);
exit;

?>
