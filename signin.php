<?php
session_start();
require_once 'includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Thử đăng nhập với customer trước
    $sql_customer = "SELECT * FROM customer WHERE customer_user_name = ? AND customer_password = ? AND account_status = 'Active'";
    $stmt_customer = $conn->prepare($sql_customer);
    $stmt_customer->bind_param("ss", $username, $password);
    $stmt_customer->execute();
    $result_customer = $stmt_customer->get_result();
    
    if ($result_customer->num_rows > 0) {
        // Đăng nhập thành công với customer
        $user = $result_customer->fetch_assoc();
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['customer_name'] = $user['customer_name'];
        $_SESSION['user_type'] = 'customer';
        $_SESSION['login_time'] = time();
        
        // Chuyển hướng về trang chủ hoặc trang trước đó
        if (isset($_GET['redirect'])) {
            header("Location: " . $_GET['redirect']);
        } else {
            header("Location: index.php");
        }
        exit();
    }
    
    // Nếu không phải customer, thử đăng nhập với admin
    $sql_admin = "SELECT * FROM admin WHERE admin_user_name = ? AND admin_password = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("ss", $username, $password);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();
    
    if ($result_admin->num_rows > 0) {
        // Đăng nhập thành công với admin
        $admin = $result_admin->fetch_assoc();
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['admin_name'];
        $_SESSION['admin_level'] = $admin['admin_level'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['login_time'] = time();
        
        // Chuyển hướng đến admin dashboard
        header("Location: administrator/dashboard.php");
        exit();
    }
    
    // Nếu cả hai đều không thành công
    $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PhoneStore</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #c62828;
            font-size: 14px;
        }
        
        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .demo-accounts {
            margin-top: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .demo-accounts h3 {
            color: #333;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .demo-account {
            background: white;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #e1e5e9;
            font-size: 13px;
        }
        
        .demo-account strong {
            color: #667eea;
        }
        
        .user-type-info {
            text-align: center;
            color: #666;
            font-size: 13px;
            margin-top: 15px;
            padding: 10px;
            background: #f0f4ff;
            border-radius: 6px;
        }
    </style>
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
        
        <div class="user-type-info">
            💡 <strong>Hệ thống tự động nhận diện:</strong><br>
            Cùng form đăng nhập cho cả <strong>Khách hàng</strong> và <strong>Quản trị viên</strong>
        </div>
        
        <div class="demo-accounts">
            <h3>👥 Tài khoản demo:</h3>
            <div class="demo-account">
                <strong>Khách hàng:</strong> ninh / 1234
            </div>
            <div class="demo-account">
                <strong>Quản trị viên:</strong> vuvy / 1234
            </div>
        </div>
        
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