<?php
include_once("includes/database.php");

echo "<h2>Debug Chat Messages</h2>";

// Check if table exists
$check = mysqli_query($conn, "SHOW TABLES LIKE 'chat_messages'");
if(mysqli_num_rows($check) == 0){
    echo "<p style='color: red;'>❌ Bảng chat_messages KHÔNG tồn tại!</p>";
    echo "<p>Vui lòng chạy file: <a href='create_chat_table.php'>create_chat_table.php</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Bảng chat_messages tồn tại</p>";

// Check table structure
echo "<h3>Cấu trúc bảng:</h3>";
$cols = mysqli_query($conn, "SHOW COLUMNS FROM chat_messages");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while($col = mysqli_fetch_assoc($cols)){
    echo "<tr>";
    echo "<td>{$col['Field']}</td>";
    echo "<td>{$col['Type']}</td>";
    echo "</tr>";
}
echo "</table>";

// Count messages
$count = mysqli_query($conn, "SELECT COUNT(*) as total FROM chat_messages");
$row = mysqli_fetch_assoc($count);
echo "<p><strong>Tổng số tin nhắn: {$row['total']}</strong></p>";

// Show latest messages
echo "<h3>10 tin nhắn gần đây nhất:</h3>";
$latest = mysqli_query($conn, "SELECT * FROM chat_messages ORDER BY created_at DESC LIMIT 10");
if(mysqli_num_rows($latest) > 0){
    echo "<table border='1' cellpadding='10' style='width: 100%;'>";
    echo "<tr><th>ID</th><th>Conversation</th><th>Name</th><th>Email</th><th>Message</th><th>Type</th><th>Time</th></tr>";
    while($msg = mysqli_fetch_assoc($latest)){
        $message_text = substr($msg['message'], 0, 50);
        echo "<tr>";
        echo "<td>{$msg['id']}</td>";
        echo "<td>{$msg['conversation_id']}</td>";
        echo "<td>{$msg['sender_name']}</td>";
        echo "<td>{$msg['sender_email']}</td>";
        echo "<td>{$message_text}...</td>";
        echo "<td>{$msg['sender_type']}</td>";
        echo "<td>{$msg['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Chưa có tin nhắn nào</p>";
}

// Test insert
echo "<h3>Test Insert:</h3>";
$test_insert = "INSERT INTO chat_messages (conversation_id, sender_name, sender_email, message, sender_type, is_read, created_at) 
                VALUES (999999, 'Test User', 'test@example.com', 'Test message', 'customer', 0, NOW())";
if(mysqli_query($conn, $test_insert)){
    echo "<p style='color: green;'>✅ Insert test thành công!</p>";
} else {
    echo "<p style='color: red;'>❌ Insert test thất bại: " . mysqli_error($conn) . "</p>";
}

// Check again
$count2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM chat_messages");
$row2 = mysqli_fetch_assoc($count2);
echo "<p><strong>Số tin nhắn sau test: {$row2['total']}</strong></p>";

?>
