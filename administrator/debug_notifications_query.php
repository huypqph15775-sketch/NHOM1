<?php
// Debug file để kiểm tra query trong notifications.php
require_once __DIR__ . '/includes/nav.php';

echo "<h2>Debug Query Notifications</h2>";

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

echo "<p><strong>has_is_admin:</strong> " . ($has_is_admin ? "TRUE" : "FALSE") . "</p>";

$has_user_col = false;
$rcu = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
if($rcu && mysqli_num_rows($rcu) > 0) $has_user_col = 'user_id';
else {
  $rcu2 = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
  if($rcu2 && mysqli_num_rows($rcu2) > 0) $has_user_col = 'customer_id';
}

echo "<p><strong>has_user_col:</strong> " . ($has_user_col ? $has_user_col : "FALSE") . "</p>";

// Build query
if($has_user_col && $has_is_admin){
  $get_notifications = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL OR is_admin = 1 ORDER BY created_at DESC";
} else if($has_user_col){
  $get_notifications = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL ORDER BY created_at DESC";
} else {
  if($has_is_admin){
    $get_notifications = "SELECT * FROM notifications WHERE is_admin = 1 ORDER BY created_at DESC";
  } else {
    $get_notifications = "SELECT * FROM notifications ORDER BY created_at DESC";
  }
}

echo "<p><strong>Query được xây dựng:</strong></p>";
echo "<pre style='background: #f0f0f0; padding: 10px; border: 1px solid #ddd;'>";
echo htmlspecialchars($get_notifications);
echo "</pre>";

$run_notifications = @mysqli_query($conn, $get_notifications);

echo "<p><strong>Kết quả query:</strong></p>";
if($run_notifications){
  $num_rows = mysqli_num_rows($run_notifications);
  echo "<p style='color: green;'>✅ Query thực thi thành công! Tìm thấy <strong>{$num_rows}</strong> kết quả</p>";
  
  if($num_rows > 0){
    echo "<table border='1' cellpadding='10' style='width: 100%;'>";
    echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>is_admin</th><th>Created At</th></tr>";
    while($row = mysqli_fetch_array($run_notifications)){
      $id = isset($row['id']) ? $row['id'] : (isset($row['notify_id']) ? $row['notify_id'] : '');
      $title = isset($row['title']) ? $row['title'] : '';
      $type = isset($row['type']) ? $row['type'] : '';
      $is_admin = isset($row['is_admin']) ? $row['is_admin'] : '';
      $created = isset($row['created_at']) ? $row['created_at'] : '';
      echo "<tr>";
      echo "<td>{$id}</td>";
      echo "<td>{$type}</td>";
      echo "<td>{$title}</td>";
      echo "<td>{$is_admin}</td>";
      echo "<td>{$created}</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "<p style='color: orange;'>⚠️ Query không trả về kết quả</p>";
  }
} else {
  echo "<p style='color: red;'>❌ Query thất bại: " . mysqli_error($conn) . "</p>";
}

// Check all notifications in database
echo "<h3>Tất cả thông báo trong database:</h3>";
$all = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
if(mysqli_num_rows($all) > 0){
  echo "<table border='1' cellpadding='10' style='width: 100%;'>";
  echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>is_admin</th><th>user_id</th><th>customer_id</th><th>Created At</th></tr>";
  while($row = mysqli_fetch_array($all)){
    $id = isset($row['id']) ? $row['id'] : 'N/A';
    $type = isset($row['type']) ? $row['type'] : 'N/A';
    $title = isset($row['title']) ? $row['title'] : 'N/A';
    $is_admin = isset($row['is_admin']) ? $row['is_admin'] : 'N/A';
    $user_id = isset($row['user_id']) ? $row['user_id'] : 'N/A';
    $customer_id = isset($row['customer_id']) ? $row['customer_id'] : 'N/A';
    $created = isset($row['created_at']) ? $row['created_at'] : 'N/A';
    echo "<tr>";
    echo "<td>{$id}</td>";
    echo "<td>{$type}</td>";
    echo "<td>{$title}</td>";
    echo "<td>{$is_admin}</td>";
    echo "<td>{$user_id}</td>";
    echo "<td>{$customer_id}</td>";
    echo "<td>{$created}</td>";
    echo "</tr>";
  }
  echo "</table>";
} else {
  echo "<p>Không có thông báo nào</p>";
}

?>
