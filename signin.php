
<?php
session_start();
require_once 'includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // =========================
    // 1. THỬ ĐĂNG NHẬP CUSTOMER
    // =========================
    $sql_customer = "
        SELECT c.*, r.role_name, r.role_level
        FROM customer c
        LEFT JOIN roles r ON c.role_id = r.role_id
        WHERE c.customer_user_name = ? 
          AND c.customer_password = ? 
          AND c.account_status = 'Active'
    ";

    if ($stmt_customer = $conn->prepare($sql_customer)) {
        $stmt_customer->bind_param('ss', $username, $password);
        $stmt_customer->execute();
        $result_customer = $stmt_customer->get_result();

        if ($result_customer && $result_customer->num_rows > 0) {
            $user = $result_customer->fetch_assoc();

            // Lưu session cho customer
            $_SESSION['customer_id']   = $user['customer_id'];
            $_SESSION['customer_name'] = $user['customer_name'];
            $_SESSION['user_type']     = 'customer';

            // Thông tin role
            $_SESSION['role_id']       = $user['role_id']       ?? 1;
            $_SESSION['role_name']     = $user['role_name']     ?? 'customer';
            $_SESSION['role_level']    = (int)($user['role_level'] ?? 1);

            $_SESSION['login_time']    = time();

            // Redirect: ưu tiên ?redirect=
            if (!empty($_GET['redirect'])) {
                header('Location: ' . $_GET['redirect']);
            } else {
                header('Location: index.php');
            }
            exit();
        }
    }

    // ======================
    // 2. THỬ ĐĂNG NHẬP ADMIN
    // ======================
    $sql_admin = "
        SELECT a.*, r.role_name, r.role_level
        FROM admin a
        LEFT JOIN roles r ON a.role_id = r.role_id
        WHERE a.admin_user_name = ? 
          AND a.admin_password = ?
    ";

    if ($stmt_admin = $conn->prepare($sql_admin)) {
        $stmt_admin->bind_param('ss', $username, $password);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($result_admin && $result_admin->num_rows > 0) {
            $admin = $result_admin->fetch_assoc();

            // Lưu session cho admin
            $_SESSION['user_type']   = 'admin';
            $_SESSION['admin_id']    = $admin['admin_id'];
            $_SESSION['admin_name']  = $admin['admin_name'];

            // Từ bảng roles
            $_SESSION['role_id']     = $admin['role_id'];
            $_SESSION['role_name']   = $admin['role_name'];
            $_SESSION['role_level']  = (int)$admin['role_level'];

            // Giữ lại admin_level cũ (vd: 'Quản lý')
            $_SESSION['admin_level'] = $admin['admin_level'];

            $_SESSION['login_time']  = time();

            // Redirect: ưu tiên ?redirect=
            if (!empty($_GET['redirect'])) {
                header('Location: ' . $_GET['redirect']);
            } else {
                header('Location: administrator/index.php?dashboard');
            }
            exit();
        }
    }

    // ======================
    // 3. CẢ HAI ĐỀU THẤT BẠI
    // ======================
    $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PhoneStore</title>
    <link rel="stylesheet" href="css/signin.css">  
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>📱 PhoneStore</h1>
            <p>Đăng nhập vào hệ thống</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
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
            
            <button type="submit" class="btn-login">🔐 Đăng nhập</button>
        </form>
        
  
        
        <div class="links">
            <a href="signup.php">📝 Đăng ký tài khoản</a>
            <a href="index.php">🏠 Về trang chủ</a>
        </div>
    </div>

    <script>
        // Focus vào ô username khi trang load
        document.getElementById('username').focus();
        
        // Thêm hiệu ứng khi nhập
        const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>