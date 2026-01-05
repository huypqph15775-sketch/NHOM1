<?php
// API to get chat history
header('Content-Type: application/json');

session_start();
include_once("includes/database.php");

// Prefer logged-in user's conversation (customer_id). For guests, use posted email.
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conversation_id = null;

    // If customer is logged in, use their customer_id as conversation_id
    if(isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])){
        $conversation_id = (int)$_SESSION['customer_id'];
    } elseif(isset($_POST['email'])){
        $email = trim($_POST['email']);
        if(!empty($email)){
            $email_esc = mysqli_real_escape_string($conn, $email);
            $conv_result = mysqli_query($conn, "SELECT conversation_id FROM chat_messages WHERE sender_email = '$email_esc' LIMIT 1");
            if($conv_result && mysqli_num_rows($conv_result) > 0){
                $row = mysqli_fetch_assoc($conv_result);
                $conversation_id = $row['conversation_id'];
            }
        }
    }

    if(!$conversation_id){
        echo json_encode(['messages' => [], 'conversation_id' => null]);
        exit;
    }

    // Get all messages for this conversation
    $conversation_id_esc = mysqli_real_escape_string($conn, (string)$conversation_id);
    $messages_query = "SELECT * FROM chat_messages WHERE conversation_id = '$conversation_id_esc' ORDER BY created_at ASC";
    $messages_result = mysqli_query($conn, $messages_query);

    $messages = [];
    while($msg = mysqli_fetch_assoc($messages_result)){
        $messages[] = [
            'sender_type' => $msg['sender_type'],
            'message' => $msg['message'],
            'created_at' => $msg['created_at']
        ];
    }

    echo json_encode(['messages' => $messages, 'conversation_id' => $conversation_id]);
} else {
    echo json_encode(['messages' => [], 'conversation_id' => null]);
}

?>
