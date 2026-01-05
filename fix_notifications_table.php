<?php
// Check and fix notifications table structure
include_once("includes/database.php");

echo "<h2>Kiểm tra và sửa bảng notifications</h2>";

// 1. Check if table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if(mysqli_num_rows($result) == 0){
    echo "<p style='color: red;'><strong>❌ Bảng 'notifications' không tồn tại!</strong></p>";
    echo "<p>Đang tạo bảng...</p>";
    
    // Create notifications table
    $create_table = "CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        customer_id INT NULL,
        is_admin INT DEFAULT 0,
        type VARCHAR(100) DEFAULT 'system',
        title VARCHAR(255),
        message LONGTEXT,
        content LONGTEXT,
        related_id INT NULL,
        is_read INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_is_admin (is_admin),
        INDEX idx_type (type),
        INDEX idx_created_at (created_at)
    )";
    
    if(mysqli_query($conn, $create_table)){
        echo "<p style='color: green;'><strong>✓ Bảng notifications đã được tạo thành công!</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Lỗi tạo bảng: " . mysqli_error($conn) . "</strong></p>";
    }
} else {
    echo "<p style='color: green;'><strong>✓ Bảng 'notifications' tồn tại</strong></p>";
    
    // Check if columns exist
    $columns = [];
    $cols_result = mysqli_query($conn, "SHOW COLUMNS FROM notifications");
    while($col = mysqli_fetch_assoc($cols_result)){
        $columns[] = $col['Field'];
    }
    
    echo "<p><strong>Các cột hiện tại:</strong> " . implode(", ", $columns) . "</p>";
    
    // Check if is_admin column exists
    if(!in_array('is_admin', $columns)){
        echo "<p style='color: orange;'>Thêm cột is_admin...</p>";
        $alter = "ALTER TABLE notifications ADD COLUMN is_admin INT DEFAULT 0 AFTER user_id";
        if(mysqli_query($conn, $alter)){
            echo "<p style='color: green;'><strong>✓ Thêm cột is_admin thành công!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Lỗi: " . mysqli_error($conn) . "</strong></p>";
        }
    }
    
    // Check if type column exists
    if(!in_array('type', $columns)){
        echo "<p style='color: orange;'>Thêm cột type...</p>";
        $alter = "ALTER TABLE notifications ADD COLUMN type VARCHAR(100) DEFAULT 'system'";
        if(mysqli_query($conn, $alter)){
            echo "<p style='color: green;'><strong>✓ Thêm cột type thành công!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Lỗi: " . mysqli_error($conn) . "</strong></p>";
        }
    }
    
    // Check if title column exists
    if(!in_array('title', $columns)){
        echo "<p style='color: orange;'>Thêm cột title...</p>";
        $alter = "ALTER TABLE notifications ADD COLUMN title VARCHAR(255) AFTER type";
        if(mysqli_query($conn, $alter)){
            echo "<p style='color: green;'><strong>✓ Thêm cột title thành công!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Lỗi: " . mysqli_error($conn) . "</strong></p>";
        }
    }
    
    // Check if message column exists
    if(!in_array('message', $columns) && !in_array('content', $columns)){
        echo "<p style='color: orange;'>Thêm cột message...</p>";
        $alter = "ALTER TABLE notifications ADD COLUMN message LONGTEXT AFTER title";
        if(mysqli_query($conn, $alter)){
            echo "<p style='color: green;'><strong>✓ Thêm cột message thành công!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Lỗi: " . mysqli_error($conn) . "</strong></p>";
        }
    }
    
    // Check if created_at column exists
    if(!in_array('created_at', $columns) && !in_array('created', $columns)){
        echo "<p style='color: orange;'>Thêm cột created_at...</p>";
        $alter = "ALTER TABLE notifications ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        if(mysqli_query($conn, $alter)){
            echo "<p style='color: green;'><strong>✓ Thêm cột created_at thành công!</strong></p>";
        } else {
            echo "<p style='color: red;'><strong>❌ Lỗi: " . mysqli_error($conn) . "</strong></p>";
        }
    }
}

// Refresh and show final structure
echo "<h3>Cấu trúc bảng cuối cùng:</h3>";
$columns = mysqli_query($conn, "SHOW COLUMNS FROM notifications");
echo "<table border='1' style='margin: 10px 0; padding: 5px; width: 100%;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while($col = mysqli_fetch_assoc($columns)){
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "<td>{$col['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p style='color: green;'><strong>✓ Hoàn thành! Bảng notifications đã sẵn sàng.</strong></p>";

?>
