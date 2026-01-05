<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'includes/database.php';

$result = ['success' => false, 'count' => 0];

if(isset($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] > 0){
  $customer_id = (int)$_SESSION['customer_id'];
  // detect user column
  $user_col = null;
  $res = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
  if($res && mysqli_num_rows($res) > 0){
    $user_col = 'user_id';
  } else {
    $res2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
    if($res2 && mysqli_num_rows($res2) > 0){
      $user_col = 'customer_id';
    }
  }

  if($user_col){
    $has_is_admin = false;
    $res3 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'");
    if($res3 && mysqli_num_rows($res3) > 0) $has_is_admin = true;
    $is_admin_condition = $has_is_admin ? " AND is_admin = 0" : "";

    $sql = "SELECT COUNT(*) as cnt FROM notifications WHERE `$user_col` = ? AND is_read = 0" . $is_admin_condition;
    if($stmt = $conn->prepare($sql)){
      $stmt->bind_param('i', $customer_id);
      $stmt->execute();
      $res = $stmt->get_result();
      if($row = $res ? $res->fetch_assoc() : null){
        $result['success'] = true;
        $result['count'] = (int)$row['cnt'];
      }
      $stmt->close();
    }
  }
}

echo json_encode($result);
exit;
