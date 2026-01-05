<?php
echo "<h2>Kiểm tra Bảng Notifications</h2>";

include_once("includes/database.php");

// 1. Kiểm tra bảng tồn tại
echo "<p><strong>1. Kiểm tra bảng notifications tồn tại:</strong></p>";
$check = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if(mysqli_num_rows($check) > 0){
    echo "<p style='color: green;'>✅ Bảng notifications tồn tại</p>";
} else {
    echo "<p style='color: red;'>❌ Bảng notifications KHÔNG tồn tại</p>";
    echo "<p>Vui lòng chạy file: <a href='fix_notifications_table.php'>fix_notifications_table.php</a></p>";
    exit;
}

// 2. Hiển thị cấu trúc
echo "<p><strong>2. Cấu trúc bảng notifications:</strong></p>";
$cols = mysqli_query($conn, "SHOW COLUMNS FROM notifications");
while($col = mysqli_fetch_assoc($cols)){
    echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
}

// 3. Đếm thông báo
echo "<p><strong>3. Số thông báo trong bảng:</strong></p>";
$count = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications");
$row = mysqli_fetch_assoc($count);
echo "Tổng: " . $row['total'] . " thông báo<br>";

// 4. Đếm thông báo loại contact
echo "<p><strong>4. Số thông báo loại 'contact':</strong></p>";
$contact_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE type = 'contact'");
$row = mysqli_fetch_assoc($contact_count);
echo "Loại contact: " . $row['total'] . "<br>";

// 5. Hiển thị thông báo mới nhất
echo "<p><strong>5. 5 Thông báo mới nhất:</strong></p>";
$latest = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Type</th><th>Title</th><th>is_admin</th><th>Created At</th></tr>";
while($row = mysqli_fetch_assoc($latest)){
    $id = isset($row['id']) ? $row['id'] : $row['notify_id'];
    $type = isset($row['type']) ? $row['type'] : 'N/A';
    $title = isset($row['title']) ? $row['title'] : 'N/A';
    $is_admin = isset($row['is_admin']) ? $row['is_admin'] : 'N/A';
    $created = isset($row['created_at']) ? $row['created_at'] : 'N/A';
    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td>$type</td>";
    echo "<td>$title</td>";
    echo "<td>$is_admin</td>";
    echo "<td>$created</td>";
    echo "</tr>";
}
echo "</table>";

// 6. Nút test
echo "<p><strong>6. Test Gửi Thông Báo:</strong></p>";
if(isset($_POST['test_send'])){
    include_once("functions/functions.php");
    $result = add_notification(NULL, 1, 'contact', 'Test Contact - ' . date('H:i:s'), 'Đây là thư test gửi lúc ' . date('Y-m-d H:i:s'), NULL);
    if($result){
        echo "<p style='color: green;'>✅ Gửi thông báo test thành công!</p>";
        echo "<meta http-equiv='refresh' content='2'>";
    } else {
        echo "<p style='color: red;'>❌ Gửi thông báo test thất bại!</p>";
    }
}

echo "<form method='post'>";
echo "<button type='submit' name='test_send' class='btn btn-primary' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px;'>Test Gửi Thông Báo</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='administrator/index.php?notifications' target='_blank'>👉 Xem trang Admin Notifications</a></p>";
?>
