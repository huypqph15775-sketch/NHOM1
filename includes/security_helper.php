<?php
/**
 * Security Helper Functions
 * Các hàm giúp bảo vệ ứng dụng
 */

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Việt Nam)
 */
function validatePhoneVN($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 11 && preg_match('/^(0|84)[0-9]{9,10}$/', $phone);
}

/**
 * Sanitize username (alphanumeric, underscore, dash only)
 */
function sanitizeUsername($username) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
}

/**
 * Check password strength
 */
function checkPasswordStrength($password) {
    $score = 0;
    
    if (strlen($password) >= 8) $score++;
    if (strlen($password) >= 12) $score++;
    if (preg_match('/[a-z]/', $password)) $score++;
    if (preg_match('/[A-Z]/', $password)) $score++;
    if (preg_match('/[0-9]/', $password)) $score++;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
    
    // Score: 0-2 = Weak, 3-4 = Medium, 5+ = Strong
    return [
        'score' => $score,
        'strength' => $score <= 2 ? 'weak' : ($score <= 4 ? 'medium' : 'strong'),
        'message' => $score <= 2 ? 'Yếu' : ($score <= 4 ? 'Trung bình' : 'Mạnh')
    ];
}

/**
 * Upload file an toàn
 */
function safeUploadFile($file_input, $upload_dir, $allowed_types = [], $max_size = 5242880) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    // Kiểm tra directory tồn tại
    if (!is_dir($upload_dir)) {
        $result['error'] = 'Upload directory không tồn tại!';
        return $result;
    }
    
    // Kiểm tra file được upload
    if (!isset($file_input) || $file_input['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Lỗi khi upload file!';
        return $result;
    }
    
    $file_type = mime_content_type($file_input['tmp_name']);
    $file_size = $file_input['size'];
    $file_ext = strtolower(pathinfo($file_input['name'], PATHINFO_EXTENSION));
    
    // Kiểm tra type
    if (!empty($allowed_types) && !in_array($file_type, $allowed_types)) {
        $result['error'] = 'Định dạng file không được hỗ trợ!';
        return $result;
    }
    
    // Kiểm tra size
    if ($file_size > $max_size) {
        $result['error'] = 'Kích thước file vượt quá giới hạn!';
        return $result;
    }
    
    // Tạo tên file an toàn
    $filename = 'file_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file_ext;
    $filepath = $upload_dir . '/' . $filename;
    
    // Move file
    if (move_uploaded_file($file_input['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Không thể lưu file!';
    }
    
    return $result;
}

/**
 * Sanitize HTML output
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Check session security
 */
function validateSession() {
    if (!isset($_SESSION['ip_address']) || !isset($_SESSION['user_agent'])) {
        return true; // Session cũ, cho phép
    }
    
    // Kiểm tra IP
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        return false; // IP thay đổi, nghi ngờ session hijacking
    }
    
    // Kiểm tra User Agent
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        return false; // User Agent thay đổi
    }
    
    return true;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limiting
 */
function checkRateLimit($key, $max_attempts = 5, $time_window = 900) {
    $session_key = 'ratelimit_' . md5($key);
    
    if (!isset($_SESSION[$session_key])) {
        $_SESSION[$session_key] = [
            'attempts' => 0,
            'first_attempt' => time()
        ];
    }
    
    $limit = &$_SESSION[$session_key];
    
    // Reset nếu quá thời gian
    if (time() - $limit['first_attempt'] > $time_window) {
        $limit['attempts'] = 0;
        $limit['first_attempt'] = time();
    }
    
    $limit['attempts']++;
    
    return $limit['attempts'] <= $max_attempts;
}

?>
