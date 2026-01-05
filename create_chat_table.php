<?php
// Create/Fix chat_messages table
include_once("includes/database.php");

echo "<h2>Tạo bảng chat_messages</h2>";

// Check if table exists
$check = mysqli_query($conn, "SHOW TABLES LIKE 'chat_messages'");
if(mysqli_num_rows($check) > 0){
    echo "<p style='color: green;'>✅ Bảng chat_messages đã tồn tại</p>";
    
    // Check if sender_type column exists
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM chat_messages LIKE 'sender_type'");
    if(mysqli_num_rows($col_check) == 0){
        echo "<p style='color: orange;'>Thêm cột sender_type...</p>";
        mysqli_query($conn, "ALTER TABLE chat_messages ADD COLUMN sender_type VARCHAR(50) DEFAULT 'customer' AFTER message");
    }
    
    // Check if conversation_id column exists
    $col_check2 = mysqli_query($conn, "SHOW COLUMNS FROM chat_messages LIKE 'conversation_id'");
    if(mysqli_num_rows($col_check2) == 0){
        echo "<p style='color: orange;'>Thêm cột conversation_id...</p>";
        mysqli_query($conn, "ALTER TABLE chat_messages ADD COLUMN conversation_id INT AFTER id");
        
        // Set conversation_id for existing messages
        $existing = mysqli_query($conn, "SELECT MAX(id) as max_id FROM chat_messages");
        $row = mysqli_fetch_assoc($existing);
        if($row['max_id']){
            mysqli_query($conn, "UPDATE chat_messages SET conversation_id = id WHERE conversation_id IS NULL OR conversation_id = 0");
        }
    }
    
} else {
    echo "<p style='color: orange;'>Đang tạo bảng chat_messages...</p>";
    
    $create = "CREATE TABLE chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conversation_id INT NOT NULL,
        sender_name VARCHAR(100),
        sender_email VARCHAR(100),
        message LONGTEXT,
        sender_type VARCHAR(50) DEFAULT 'customer',
        is_read INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_conversation_id (conversation_id),
        INDEX idx_sender_type (sender_type),
        INDEX idx_created_at (created_at)
    )";
    
    if(mysqli_query($conn, $create)){
        echo "<p style='color: green;'>✅ Bảng chat_messages tạo thành công!</p>";
    } else {
        echo "<p style='color: red;'>❌ Lỗi: " . mysqli_error($conn) . "</p>";
    }
}

// Check table structure
echo "<h3>Cấu trúc bảng:</h3>";
$cols = mysqli_query($conn, "SHOW COLUMNS FROM chat_messages");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while($col = mysqli_fetch_assoc($cols)){
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "<td>{$col['Null']}</td>";
    echo "<td>{$col['Key']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p style='color: green; margin-top: 20px;'><strong>✅ Hoàn thành! Bảng chat_messages đã sẵn sàng.</strong></p>";

?>
