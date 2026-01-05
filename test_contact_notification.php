<?php
session_start();
include_once("includes/database.php");
include_once("functions/functions.php");

echo "<h2>Test Gửi Thông Báo Liên Hệ</h2>";

// Step 1: Kiểm tra kết nối database
echo "<h3>Bước 1: Kiểm tra kết nối database</h3>";
if($conn){
    echo "<p style='color: green;'>✓ Kết nối database thành công</p>";
} else {
    echo "<p style='color: red;'>❌ Kết nối database thất bại</p>";
    exit;
}

// Step 2: Kiểm tra bảng notifications
echo "<h3>Bước 2: Kiểm tra bảng notifications</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if(mysqli_num_rows($result) == 0){
    echo "<p style='color: red;'>❌ Bảng notifications không tồn tại!</p>";
    exit;
} else {
    echo "<p style='color: green;'>✓ Bảng notifications tồn tại</p>";
}

// Step 3: Hiển thị cấu trúc bảng
echo "<h3>Bước 3: Cấu trúc bảng notifications</h3>";
$cols_result = mysqli_query($conn, "SHOW COLUMNS FROM notifications");
$columns = [];
echo "<table border='1' style='margin: 10px 0;'>";
echo "<tr><th>Column</th><th>Type</th></tr>";
while($col = mysqli_fetch_assoc($cols_result)){
    $columns[] = $col['Field'];
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
}
echo "</table>";

// Step 4: Kiểm tra hàm add_notification
echo "<h3>Bước 4: Test hàm add_notification()</h3>";
if(function_exists('add_notification')){
    echo "<p style='color: green;'>✓ Hàm add_notification tồn tại</p>";
} else {
    echo "<p style='color: red;'>❌ Hàm add_notification không tồn tại!</p>";
    exit;
}

// Step 5: Gửi test notification
echo "<h3>Bước 5: Gửi test notification</h3>";
$test_result = add_notification(NULL, 1, 'contact', 'Test Tin nhắn liên hệ', 'Đây là tin nhắn test - ' . date('Y-m-d H:i:s'), NULL);

if($test_result){
    echo "<p style='color: green;'>✓ Test notification gửi thành công!</p>";
} else {
    echo "<p style='color: red;'>❌ Test notification gửi thất bại!</p>";
}

// Step 6: Kiểm tra dữ liệu trong database
echo "<h3>Bước 6: Kiểm tra dữ liệu trong database</h3>";
$query = "SELECT * FROM notifications WHERE type = 'contact' ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0){
    echo "<p style='color: green;'>✓ Tìm thấy " . mysqli_num_rows($result) . " thông báo loại 'contact'</p>";
    echo "<table border='1' style='margin: 10px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>is_admin</th><th>Message</th><th>Created</th></tr>";
    while($row = mysqli_fetch_assoc($result)){
        $id = isset($row['id']) ? $row['id'] : 'N/A';
        $type = isset($row['type']) ? $row['type'] : 'N/A';
        $title = isset($row['title']) ? $row['title'] : 'N/A';
        $is_admin = isset($row['is_admin']) ? $row['is_admin'] : 'N/A';
        $message = isset($row['message']) ? $row['message'] : (isset($row['content']) ? $row['content'] : 'N/A');
        $created = isset($row['created_at']) ? $row['created_at'] : 'N/A';
        
        // Truncate message if too long
        if(strlen($message) > 100) $message = substr($message, 0, 100) . '...';
        
        echo "<tr>";
        echo "<td>{$id}</td>";
        echo "<td>{$type}</td>";
        echo "<td>{$title}</td>";
        echo "<td>{$is_admin}</td>";
        echo "<td>{$message}</td>";
        echo "<td>{$created}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Không tìm thấy thông báo loại 'contact'</p>";
}

// Step 7: Test query từ notifications.php
echo "<h3>Bước 7: Test query từ notifications.php</h3>";

// Check if is_admin column exists
$has_is_admin = false;
$rci = mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'");
if($rci && mysqli_num_rows($rci) > 0) $has_is_admin = true;

// Check if user_id or customer_id exists
$has_user_col = false;
$rcu = mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'user_id'");
if($rcu && mysqli_num_rows($rcu) > 0) $has_user_col = 'user_id';
else {
    $rcu2 = mysqli_query($conn, "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'");
    if($rcu2 && mysqli_num_rows($rcu2) > 0) $has_user_col = 'customer_id';
}

echo "<p>has_user_col: " . ($has_user_col ? $has_user_col : 'false') . "</p>";
echo "<p>has_is_admin: " . ($has_is_admin ? 'true' : 'false') . "</p>";

// Build query like notifications.php does
if($has_user_col && $has_is_admin){
    $query_test = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL OR is_admin = 1 ORDER BY created_at DESC";
} else if($has_user_col){
    $query_test = "SELECT * FROM notifications WHERE `$has_user_col` IS NOT NULL ORDER BY created_at DESC";
} else {
    if($has_is_admin){
        $query_test = "SELECT * FROM notifications WHERE is_admin = 1 ORDER BY created_at DESC";
    } else {
        $query_test = "SELECT * FROM notifications ORDER BY created_at DESC";
    }
}

echo "<p><strong>Query được sử dụng:</strong></p>";
echo "<pre style='background: #f0f0f0; padding: 10px;'>" . htmlspecialchars($query_test) . "</pre>";

$result_test = mysqli_query($conn, $query_test);
echo "<p style='color: green;'>✓ Query thành công! Tìm thấy " . mysqli_num_rows($result_test) . " kết quả</p>";

echo "<hr style='margin: 30px 0;'>";
echo "<p><strong style='color: blue;'>Bạn có thể bây giờ truy cập trang <a href='administrator/index.php?notifications' target='_blank'>admin notifications</a> để xem thông báo.</strong></p>";

?>
