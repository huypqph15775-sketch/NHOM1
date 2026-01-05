<?php
// Handle chat message from floating chatbox
header('Content-Type: application/json');

session_start();
include_once("includes/database.php");
include_once("functions/functions.php");

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])){
    $message = trim($_POST['message']);
    $sender_name = trim($_POST['sender_name'] ?? 'Khách');
    $sender_email = trim($_POST['sender_email'] ?? '');
    
    if(empty($message)){
        echo json_encode(['reply' => 'Vui lòng nhập tin nhắn!']);
        exit;
    }
    
    // Prefer logged-in customer's id as conversation_id so accounts do not share box
    $conversation_id = 0;
    if(isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])){
        $conversation_id = (int)$_SESSION['customer_id'];
        // If server can retrieve customer email, try to set sender_email for record (optional)
        if(empty($sender_email)){
            $cid = $conversation_id;
            $res = mysqli_query($conn, "SELECT customer_email FROM customer WHERE customer_id = '".intval($cid)."' LIMIT 1");
            if($res && mysqli_num_rows($res) > 0){
                $r = mysqli_fetch_assoc($res);
                $sender_email = $r['customer_email'] ?? $sender_email;
            }
        }
    } else {
        // Guest: try to find existing conversation by email
        if(!empty($sender_email)){
            $existing = mysqli_query($conn, "SELECT conversation_id FROM chat_messages WHERE sender_email = '" . mysqli_real_escape_string($conn, $sender_email) . "' LIMIT 1");
            if($existing && mysqli_num_rows($existing) > 0){
                $row = mysqli_fetch_assoc($existing);
                $conversation_id = $row['conversation_id'];
            }
        }
    }
    
    // If still no conversation found, create new one (use timestamp)
    if($conversation_id == 0){
        $conversation_id = time(); // Use timestamp as conversation ID for guests
    }
    
    // Save customer message to database
    $message_esc = mysqli_real_escape_string($conn, $message);
    $name_esc = mysqli_real_escape_string($conn, $sender_name);
    $email_esc = mysqli_real_escape_string($conn, $sender_email);
    
    $insert_msg = "INSERT INTO chat_messages (conversation_id, sender_name, sender_email, message, sender_type, is_read, created_at) 
                   VALUES ('".mysqli_real_escape_string($conn, (string)$conversation_id)."', '$name_esc', '$email_esc', '$message_esc', 'customer', 0, NOW())";
    $msg_id = null;
    if(mysqli_query($conn, $insert_msg)){
        $msg_id = mysqli_insert_id($conn);
    }
    
    // Generate bot reply based on keywords
    $reply = generateReply($message);
    
    // Save auto-reply to database
    $reply_esc = mysqli_real_escape_string($conn, $reply);
    $insert_reply = "INSERT INTO chat_messages (conversation_id, sender_name, sender_email, message, sender_type, is_read, created_at) 
                     VALUES ('".mysqli_real_escape_string($conn, (string)$conversation_id)."', 'SmartPhoneStore Bot', 'bot@smartphonestore.com', '$reply_esc', 'admin', 1, NOW())";
    mysqli_query($conn, $insert_reply);
    
    echo json_encode(['reply' => $reply, 'msg_id' => $msg_id, 'conversation_id' => $conversation_id]);
} else {
    echo json_encode(['reply' => 'Có lỗi xảy ra!']);
}

function generateReply($message) {
    $message = strtolower($message);
    
    // Greeting keywords
    if(strpos($message, 'chào') !== false || strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
        return 'Chào bạn! 👋 Chào mừng đến SmartPhoneStore - cửa hàng bán điện thoại uy tín hàng đầu Hà Nội. Chúng tôi có thể giúp gì cho bạn?';
    }
    
    // About/Website keywords
    if(strpos($message, 'về') !== false || strpos($message, 'web') !== false || strpos($message, 'website') !== false || strpos($message, 'giới thiệu') !== false) {
        return 'SmartPhoneStore là cửa hàng bán điện thoại uy tín được thành lập năm 2020. Chúng tôi chuyên cung cấp các sản phẩm điện thoại chính hãng với giá cả cạnh tranh nhất thị trường. Địa chỉ: 128A, Hồ Tùng Mậu, Mai Dịch, Cầu Giấy, Hà Nội.';
    }
    
    // Product/Shop keywords
    if(strpos($message, 'sản phẩm') !== false || strpos($message, 'điện thoại') !== false || strpos($message, 'product') !== false || strpos($message, 'phone') !== false) {
        return 'Chúng tôi cung cấp các dòng điện thoại từ các hãng nổi tiếng như Apple iPhone, Samsung Galaxy, Xiaomi, Oppo, Vivo, v.v. Tất cả đều là hàng chính hãng với bảo hành đầy đủ. Bạn có thể xem chi tiết sản phẩm trong mục "Cửa hàng"!';
    }
    
    // Contact/Address keywords
    if(strpos($message, 'liên hệ') !== false || strpos($message, 'contact') !== false || strpos($message, 'địa chỉ') !== false || strpos($message, 'address') !== false) {
        return '📍 Địa chỉ: 128A, Hồ Tùng Mậu, Mai Dịch, Cầu Giấy, Tp Hà Nội\n📞 Điện thoại: 1900.8198\n📧 Email: PhoneStore@gmail.com\n⏰ Giờ hoạt động: 7:00 - 21:00 (Hàng ngày)';
    }
    
    // Price/Payment keywords
    if(strpos($message, 'giá') !== false || strpos($message, 'price') !== false || strpos($message, 'thanh toán') !== false || strpos($message, 'payment') !== false) {
        return 'Chúng tôi hỗ trợ nhiều phương thức thanh toán:\n💳 Thanh toán trực tiếp tại cửa hàng\n🏧 Chuyển khoản ngân hàng\n📱 Thanh toán qua ví điện tử (MoMo, Zalo Pay)\n💰 Trả góp qua các hãng tài chính.\nGiá sản phẩm cạnh tranh, có chương trình khuyến mãi thường xuyên!';
    }
    
    // Delivery/Shipping keywords
    if(strpos($message, 'giao') !== false || strpos($message, 'vận chuyển') !== false || strpos($message, 'ship') !== false || strpos($message, 'delivery') !== false) {
        return '🚚 Chúng tôi cung cấp dịch vụ giao hàng miễn phí cho những đơn hàng trên 1 triệu đồng.\n📦 Giao hàng nhanh trong vòng 24 giờ tại Hà Nội\n🌍 Giao hàng toàn quốc (chi phí phí vận chuyển sẽ được tính riêng)\nBạn có thể theo dõi đơn hàng trong phần "Đơn hàng của tôi"';
    }
    
    // Warranty/Policy keywords
    if(strpos($message, 'bảo hành') !== false || strpos($message, 'warranty') !== false || strpos($message, 'đổi trả') !== false) {
        return '✅ Tất cả sản phẩm đều được bảo hành chính hãng theo quy định của nhà sản xuất\n✅ Cam kết hàng chính hãng 100%\n✅ Hỗ trợ đổi trả trong vòng 7 ngày nếu sản phẩm bị lỗi\n✅ Tư vấn miễn phí và hỗ trợ kỹ thuật sau bán hàng';
    }
    
    // Account/Login keywords
    if(strpos($message, 'tài khoản') !== false || strpos($message, 'đăng nhập') !== false || strpos($message, 'account') !== false || strpos($message, 'login') !== false) {
        return 'Bạn có thể tạo tài khoản trên website để:\n👤 Quản lý thông tin cá nhân\n📋 Theo dõi lịch sử mua hàng\n🛒 Lưu giỏ hàng\n💬 Nhận tin tức khuyến mãi\nChỉ cần nhấp vào "Đăng ký" hoặc "Đăng nhập" trên website!';
    }
    
    // Thank you/Goodbye keywords
    if(strpos($message, 'cảm ơn') !== false || strpos($message, 'thanks') !== false || strpos($message, 'bye') !== false || strpos($message, 'tạm biệt') !== false) {
        return 'Cảm ơn bạn đã liên hệ SmartPhoneStore! 😊 Nếu có thêm câu hỏi, hãy liên hệ lại bất kỳ lúc nào. Chúng tôi luôn sẵn sàng phục vụ!';
    }
    
    // Default reply with admin notification
    return 'Cảm ơn câu hỏi của bạn! 😊 Chúng tôi sẽ hỗ trợ bạn sớm. Vui lòng cung cấp thêm thông tin nếu cần thiết hoặc liên hệ qua số 1900.8198 để được hỗ trợ nhanh hơn!';
}

?>

