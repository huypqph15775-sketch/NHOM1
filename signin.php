
<?php
session_start();
require_once 'includes/database.php';

// Ngăn chặn Session Fixation
// ini_set('session.use_only_cookies', 1);
// ini_set('session.use_strict_mode', 1);

$error = '';
$success = false;

// Hàm an toàn để lấy redirect URL
function getSafeRedirectUrl($redirect) {
    if (empty($redirect)) return null;
    
    $redirect = urldecode($redirect);
    // Chỉ cho phép redirect nội bộ
    if (strpos($redirect, 'http://') === 0 || strpos($redirect, 'https://') === 0 || strpos($redirect, '//') === 0) {
        return null;
    }
    return filter_var($redirect, FILTER_SANITIZE_URL);
}

// Kiểm tra login attempt (rate limiting)
function checkLoginAttempt($username) {
    $max_attempts = 5;
    $lockout_time = 15 * 60; // 15 phút
    $session_key = 'login_attempts_' . md5($username);
    
    if (!isset($_SESSION[$session_key])) {
        $_SESSION[$session_key] = [
            'attempts' => 0,
            'first_attempt' => time(),
            'locked_until' => 0
        ];
    }
    
    $attempts = &$_SESSION[$session_key];
    
    // Kiểm tra nếu đã bị khóa
    if ($attempts['locked_until'] > time()) {
        $remaining = ceil(($attempts['locked_until'] - time()) / 60);
        return "Tài khoản bị khóa tạm thời. Vui lòng thử lại sau $remaining phút.";
    }
    
    // Reset nếu quá thời gian
    if (time() - $attempts['first_attempt'] > $lockout_time) {
        $attempts['attempts'] = 0;
        $attempts['first_attempt'] = time();
    }
    
    return false;
}

// Ghi nhận login attempt thất bại
function recordFailedLoginAttempt($username) {
    $max_attempts = 5;
    $lockout_time = 15 * 60;
    $session_key = 'login_attempts_' . md5($username);
    
    if (!isset($_SESSION[$session_key])) {
        $_SESSION[$session_key] = [
            'attempts' => 0,
            'first_attempt' => time(),
            'locked_until' => 0
        ];
    }
    
    $_SESSION[$session_key]['attempts']++;
    
    if ($_SESSION[$session_key]['attempts'] >= $max_attempts) {
        $_SESSION[$session_key]['locked_until'] = time() + $lockout_time;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập tên đăng nhập và mật khẩu!';
    } else if (strlen($username) > 100 || strlen($password) > 255) {
        $error = 'Dữ liệu đầu vào không hợp lệ!';
    } else {
        // Kiểm tra rate limiting
        $lockout_msg = checkLoginAttempt($username);
        if ($lockout_msg) {
            $error = $lockout_msg;
        } else {
            $login_success = false;
            
            // =========================
            // 1. THỬ ĐĂNG NHẬP CUSTOMER
            // =========================
            $sql_customer = "
                SELECT c.*, r.role_name, r.role_level
                FROM customer c
                LEFT JOIN roles r ON c.role_id = r.role_id
                WHERE c.customer_user_name = ? 
                  AND c.account_status = 'Active'
                LIMIT 1
            ";

            if ($stmt_customer = $conn->prepare($sql_customer)) {
                $stmt_customer->bind_param('s', $username);
                $stmt_customer->execute();
                $result_customer = $stmt_customer->get_result();

                if ($result_customer && $result_customer->num_rows > 0) {
                    $user = $result_customer->fetch_assoc();
                    
                    // Xác minh mật khẩu (kiểm tra hash hoặc plain text tạm thời)
                    $password_valid = false;
                    $stored_password = (string)$user['customer_password'];
                    
                    // Cố gắng xác minh hash (nếu bắt đầu bằng $2)
                    if (strlen($stored_password) >= 60 && strpos($stored_password, '$2') === 0) {
                        $password_valid = password_verify($password, $stored_password);
                    } else {
                        // Fallback: so sánh trực tiếp (cho compatibility với dữ liệu cũ)
                        $password_valid = ($password == $stored_password || $password === $stored_password);
                    }
                    
                    if ($password_valid) {
                        // Regenerate session ID để ngăn chặn session fixation
                        session_regenerate_id(true);
                        
                        // Lưu session cho customer
                        $_SESSION['customer_id']   = (int)$user['customer_id'];
                        $_SESSION['customer_name'] = htmlspecialchars($user['customer_name'], ENT_QUOTES, 'UTF-8');
                        $_SESSION['user_type']     = 'customer';

                        // Thông tin role
                        $_SESSION['role_id']       = (int)($user['role_id'] ?? 1);
                        $_SESSION['role_name']     = $user['role_name'] ?? 'customer';
                        $_SESSION['role_level']    = (int)($user['role_level'] ?? 1);

                        $_SESSION['login_time']    = time();
                        $_SESSION['ip_address']    = $_SERVER['REMOTE_ADDR'];
                        $_SESSION['user_agent']    = $_SERVER['HTTP_USER_AGENT'];
                        
                        // Xóa login attempts khi thành công
                        unset($_SESSION['login_attempts_' . md5($username)]);
                        
                        $login_success = true;
                    }
                }
                $stmt_customer->close();
            }

            if (!$login_success) {
                // ======================
                // 2. THỬ ĐĂNG NHẬP ADMIN
                // ======================
                $sql_admin = "
                    SELECT a.*, r.role_name, r.role_level
                    FROM admin a
                    LEFT JOIN roles r ON a.role_id = r.role_id
                    WHERE a.admin_user_name = ?
                    LIMIT 1
                ";

                if ($stmt_admin = $conn->prepare($sql_admin)) {
                    $stmt_admin->bind_param('s', $username);
                    $stmt_admin->execute();
                    $result_admin = $stmt_admin->get_result();

                    if ($result_admin && $result_admin->num_rows > 0) {
                        $admin = $result_admin->fetch_assoc();
                        
                        // Xác minh mật khẩu
                        $password_valid = false;
                        $stored_password = (string)$admin['admin_password'];
                        
                        if (strlen($stored_password) >= 60 && strpos($stored_password, '$2') === 0) {
                            $password_valid = password_verify($password, $stored_password);
                        } else {
                            $password_valid = ($password == $stored_password || $password === $stored_password);
                        }
                        
                        if ($password_valid) {
                            // Regenerate session ID
                            session_regenerate_id(true);
                            
                            // Lưu session cho admin
                            $_SESSION['user_type']   = 'admin';
                            $_SESSION['admin_id']    = (int)$admin['admin_id'];
                            $_SESSION['admin_name']  = htmlspecialchars($admin['admin_name'], ENT_QUOTES, 'UTF-8');
                            // store login username too so permission helpers can match it
                            $_SESSION['admin_user_name'] = htmlspecialchars($admin['admin_user_name'], ENT_QUOTES, 'UTF-8');

                            // Từ bảng roles
                            $_SESSION['role_id']     = (int)$admin['role_id'];
                            $_SESSION['role_name']   = $admin['role_name'] ?? 'admin';
                            $_SESSION['role_level']  = (int)($admin['role_level'] ?? 4);

                            // Dùng admin_level_number (giá trị numeric)
                            $_SESSION['admin_level'] = (int)($admin['admin_level_number'] ?? 1);

                            // Defensive fallback: if admin_level is missing or lower than role_level,
                            // promote admin_level to role_level so permission checks that rely on
                            // admin_level still work for migrated/partial data.
                            if (isset($_SESSION['role_level'])) {
                                $role_lvl = (int)$_SESSION['role_level'];
                                if (empty($_SESSION['admin_level']) || $_SESSION['admin_level'] < $role_lvl) {
                                    $_SESSION['admin_level'] = $role_lvl;
                                }
                            }

                            $_SESSION['login_time']  = time();
                            $_SESSION['ip_address']  = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent']  = $_SERVER['HTTP_USER_AGENT'];
                            
                            // Xóa login attempts
                            unset($_SESSION['login_attempts_' . md5($username)]);
                            
                            $login_success = true;
                        }
                    }
                    $stmt_admin->close();
                }
            }

            if ($login_success) {
                // If there was a pending action saved in session (e.g. add_to_cart), try to perform it now
                if (isset($_SESSION['pending_action']) && is_array($_SESSION['pending_action']) && ($_SESSION['pending_action']['action'] ?? '') === 'add_to_cart' && ($_SESSION['user_type'] ?? '') === 'customer'){
                    $pa = $_SESSION['pending_action'];
                    $pid = isset($pa['product_id']) ? (int)$pa['product_id'] : 0;
                    $pqty = isset($pa['quantity']) ? max(1, (int)$pa['quantity']) : 1;
                    $pcolor = isset($pa['color']) ? $pa['color'] : '';

                    if ($pid > 0){
                        // Resolve product_color_id from product_color table if possible
                        $product_color_id = 0;
                        $get_color = "SELECT * FROM product_color WHERE product_color_name = ? LIMIT 1";
                        if ($stmtc = $conn->prepare($get_color)){
                            $stmtc->bind_param('s', $pcolor);
                            $stmtc->execute();
                            $rc = $stmtc->get_result();
                            if($rc && $rc->num_rows>0){
                                $rowc = $rc->fetch_assoc();
                                $product_color_id = (int)$rowc['product_color_id'];
                            }
                            $stmtc->close();
                        }

                        // If color not found, try any color for the product
                        if($product_color_id === 0){
                            $g = "SELECT * FROM product_img WHERE product_id = ? LIMIT 1";
                            if($s2 = $conn->prepare($g)){
                                $s2->bind_param('i', $pid);
                                $s2->execute();
                                $r2 = $s2->get_result();
                                if($r2 && $r2->num_rows>0){
                                    $row2 = $r2->fetch_assoc();
                                    $product_color_id = (int)($row2['product_color_id'] ?? 0);
                                    if(empty($pcolor)) $pcolor = $row2['product_color_name'] ?? $pcolor;
                                }
                                $s2->close();
                            }
                        }

                        // Get available quantity
                        $product_quantity = 0;
                        $select_quantity = "SELECT * FROM product_img WHERE product_id = ? AND product_color_id = ? LIMIT 1";
                        if($stmtq = $conn->prepare($select_quantity)){
                            $stmtq->bind_param('ii', $pid, $product_color_id);
                            $stmtq->execute();
                            $rq = $stmtq->get_result();
                            if($rq && $rq->num_rows>0){
                                $rowq = $rq->fetch_assoc();
                                $product_quantity = (int)($rowq['product_quantity'] ?? 0);
                            }
                            $stmtq->close();
                        }

                        // Check existing in cart
                        $exists = false;
                        $check_product = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ? AND color = ? LIMIT 1";
                        if($stch = $conn->prepare($check_product)){
                            $cust_id = (int)$_SESSION['customer_id'];
                            $stch->bind_param('iis', $cust_id, $pid, $pcolor);
                            $stch->execute();
                            $rch = $stch->get_result();
                            if($rch && $rch->num_rows>0) $exists = true;
                            $stch->close();
                        }

                        if(!$exists && $pqty <= $product_quantity){
                            $insert = "INSERT INTO cart (customer_id, product_id, color, quantity) VALUES (?, ?, ?, ?)";
                            if($stins = $conn->prepare($insert)){
                                $cust_id = (int)$_SESSION['customer_id'];
                                $stins->bind_param('iisi', $cust_id, $pid, $pcolor, $pqty);
                                $stins->execute();
                                $stins->close();
                            }
                        }
                    }

                    // clear pending action
                    unset($_SESSION['pending_action']);
                    // After performing add-to-cart, redirect user to cart page
                    header('Location: customer/cart.php');
                    exit();
                }

                // Redirect an toàn
                $redirect = getSafeRedirectUrl($_GET['redirect'] ?? '');
                
                if ($redirect) {
                    header('Location: ' . $redirect);
                } else {
                    if ($_SESSION['user_type'] === 'admin') {
                        // Role-specific quick landing pages for usernames with known prefixes
                        $login_name = strtolower($_SESSION['admin_user_name'] ?? '');
                        if (strpos($login_name, 'nvbanhang') === 0) {
                            header('Location: administrator/index.php?pending_orders');
                        } elseif (strpos($login_name, 'nvkho') === 0) {
                            header('Location: administrator/index.php?stock_list');
                        } else {
                            header('Location: administrator/index.php?dashboard');
                        }
                    } else {
                        header('Location: index.php');
                    }
                }
                exit();
            } else {
                // Ghi nhận failed attempt
                recordFailedLoginAttempt($username);
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PhoneStore</title>
    <link rel="stylesheet" href="css/auth.css">  
</head>
<body>
    
        
  
        
        <div class="auth-container">
            <div class="auth-logo">
                <h1>📱 PhoneStore</h1>
                <p>Đăng nhập vào hệ thống</p>
            </div>
            <?php if ($error): ?>
                <div class="error-msg">
                    <strong>Lỗi:</strong> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Tên đăng nhập:</label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Nhập tên đăng nhập của bạn"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Nhập mật khẩu của bạn">
                </div>
                <button type="submit" class="btn-auth">🔐 Đăng nhập</button>
            </form>
            <div class="links">
                <a href="signup.php">📝 Đăng ký tài khoản</a>
                <a href="forgot_password.php">🔑 Quên mật khẩu</a>
                <a href="index.php">🏠 Về trang chủ</a>
            </div>
        </div>