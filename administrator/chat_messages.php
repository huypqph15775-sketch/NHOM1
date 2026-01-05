<?php
// Admin panel to view and reply chat messages (Messenger-like interface)
require_once __DIR__ . '/includes/nav.php';

// Handle reply submission
$success = '';
$error = '';
if(isset($_POST['send_reply'])){
    $conversation_id = intval($_POST['conversation_id']);
    $reply = trim($_POST['reply'] ?? '');
    
    if(empty($reply)){
        $error = 'Vui lòng nhập nội dung trả lời.';
    } else {
        // Get conversation info to get email
        $conv_info = mysqli_query($conn, "SELECT sender_email, sender_name FROM chat_messages WHERE conversation_id = $conversation_id LIMIT 1");
        $conv = mysqli_fetch_assoc($conv_info);
        
        $reply_esc = mysqli_real_escape_string($conn, $reply);
        $email_esc = mysqli_real_escape_string($conn, $conv['sender_email']);
        $name_esc = mysqli_real_escape_string($conn, $conv['sender_name']);
        
        $insert_reply = "INSERT INTO chat_messages (conversation_id, sender_name, sender_email, message, sender_type, is_read, created_at) 
                         VALUES ('$conversation_id', 'Admin', 'admin@smartphonestore.com', '$reply_esc', 'admin', 1, NOW())";
        if(mysqli_query($conn, $insert_reply)){
            $success = 'Trả lời tin nhắn thành công!';
        } else {
            $error = 'Lỗi: ' . mysqli_error($conn);
        }
    }
}

// Get all conversations
$conversations_query = "SELECT 
                            cm.conversation_id,
                            cm.sender_name,
                            cm.sender_email,
                            MAX(cm.created_at) as last_message_time,
                            (SELECT message FROM chat_messages WHERE conversation_id = cm.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message,
                            COUNT(CASE WHEN cm.sender_type = 'customer' AND cm.is_read = 0 THEN 1 END) as unread_count
                        FROM chat_messages cm
                        GROUP BY cm.conversation_id
                        ORDER BY MAX(cm.created_at) DESC";
$conversations_result = mysqli_query($conn, $conversations_query);

// Get selected conversation
$selected_conversation = isset($_GET['conv_id']) ? intval($_GET['conv_id']) : null;
$messages = [];
if($selected_conversation){
    $messages_query = "SELECT * FROM chat_messages WHERE conversation_id = $selected_conversation ORDER BY created_at ASC";
    $messages_result = mysqli_query($conn, $messages_query);
    $messages = [];
    while($msg = mysqli_fetch_assoc($messages_result)){
        $messages[] = $msg;
    }
    
    // Mark as read
    mysqli_query($conn, "UPDATE chat_messages SET is_read = 1 WHERE conversation_id = $selected_conversation AND sender_type = 'customer'");
}

$selected_conv_info = null;
if($selected_conversation){
    $conv_info = mysqli_query($conn, "SELECT sender_name, sender_email FROM chat_messages WHERE conversation_id = $selected_conversation LIMIT 1");
    $selected_conv_info = mysqli_fetch_assoc($conv_info);
}
?>

<style>
    /* Make chat panels and controls square on this page */
    .chat-conversations, .chat-window, .chat-empty { border-radius: 0 !important; }
    .chat-header { border-radius: 0 !important; }
    .chat-bubble { border-radius: 0 !important; }
    .chat-reply-form .form-control, .chat-reply-form .btn { border-radius: 0 !important; }
</style>

<div class="container-fluid mt-5 pt-4" style="max-width: 1200px;">
    <h3><i class="bi bi-chat-dots"></i> Quản lý Tin nhắn Chatbox</h3>
    
    <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary">← Quay lại</a>
    </div>

    <div style="display: flex; gap: 20px; height: 600px;">
        <!-- Conversation List -->
        <div class="chat-conversations" style="flex: 0 0 35%; border: 1px solid #ddd; overflow-y: auto; background: #f8f9fa;">
            <div style="padding: 15px; border-bottom: 1px solid #ddd; background: white; font-weight: bold;">
                Danh sách hội thoại
            </div>
            
            <?php if(mysqli_num_rows($conversations_result) == 0): ?>
                <div style="padding: 20px; text-align: center; color: #999;">
                    Chưa có cuộc hội thoại nào
                </div>
            <?php else: ?>
                <?php while($conv = mysqli_fetch_assoc($conversations_result)): 
                    $is_selected = ($selected_conversation == $conv['conversation_id']);
                    $unread_badge = $conv['unread_count'] > 0 ? "<span class='badge bg-danger ms-2'>{$conv['unread_count']}</span>" : '';
                ?>
                <a href="index.php?chat_messages&conv_id=<?php echo $conv['conversation_id']; ?>" style="text-decoration: none; color: inherit;">
                    <div style="padding: 15px; border-bottom: 1px solid #e0e0e0; background: <?php echo $is_selected ? '#e3f2fd' : 'white'; ?>; cursor: pointer; transition: background 0.2s;">
                        <div style="font-weight: bold; display: flex; justify-content: space-between;">
                            <span><?php echo htmlspecialchars($conv['sender_name']); ?></span>
                            <?php echo $unread_badge; ?>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)); ?>...
                        </div>
                        <div style="font-size: 11px; color: #999; margin-top: 5px;">
                            <?php echo date('H:i d/m', strtotime($conv['last_message_time'])); ?>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Chat Window -->
        <?php if($selected_conversation && $selected_conv_info): ?>
        <div class="chat-window" style="flex: 1; border: 1px solid #ddd; display: flex; flex-direction: column; background: white;">
            <!-- Chat Header -->
            <div class="chat-header" style="padding: 15px; border-bottom: 1px solid #ddd; background: #f8f9fa;">
                <div style="font-weight: bold;">
                    <?php echo htmlspecialchars($selected_conv_info['sender_name']); ?>
                </div>
                <div style="font-size: 12px; color: #666;">
                    <?php echo htmlspecialchars($selected_conv_info['sender_email']); ?>
                </div>
            </div>

            <!-- Messages -->
            <div style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($messages as $msg): 
                    $is_customer = $msg['sender_type'] === 'customer';
                    $align = $is_customer ? 'flex-end' : 'flex-start';
                    $bg_color = $is_customer ? '#007bff' : '#e9ecef';
                    $text_color = $is_customer ? 'white' : 'black';
                ?>
                <div style="display: flex; justify-content: <?php echo $align; ?>;">
                    <div class="chat-bubble" style="background: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; padding: 10px 15px; border-radius: 0; max-width: 70%; word-wrap: break-word;">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        <div style="font-size: 11px; margin-top: 5px; opacity: 0.7;">
                            <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Input -->
            <form method="post" class="chat-reply-form" style="padding: 15px; border-top: 1px solid #ddd; display: flex; gap: 10px; align-items: flex-end;">
                <input type="hidden" name="conversation_id" value="<?php echo $selected_conversation; ?>">
                <textarea name="reply" rows="3" class="form-control" placeholder="Nhập trả lời..."></textarea>
                <button type="submit" name="send_reply" class="btn btn-primary" style="height: fit-content; border-radius: 0 !important;">
                    <i class="fas fa-paper-plane"></i> Gửi
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="chat-empty" style="flex: 1; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
            <div style="text-align: center; color: #999;">
                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 20px;"></i><br>
                Chọn một cuộc hội thoại để bắt đầu
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
