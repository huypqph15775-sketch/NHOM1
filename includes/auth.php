<?php
// Kiểm tra đăng nhập cho admin
function checkAdminLogin() {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header("Location: ../login.php?redirect=" . $redirect);
        exit();
    }
}

// Kiểm tra đăng nhập cho customer
function checkCustomerLogin() {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?redirect=" . $redirect);
        exit();
    }
}

// Kiểm tra quyền truy cập theo cấp độ
function checkPermission($required_level) {
    if (!isset($_SESSION['admin_level']) || $_SESSION['admin_level'] != $required_level) {
        $level_hierarchy = [
            'Kho' => 1,
            'Nhân viên' => 2,
            'Quản lý' => 3
        ];
        
        $user_level = $_SESSION['admin_level'] ?? '';
        $required_level_value = $level_hierarchy[$required_level] ?? 0;
        $user_level_value = $level_hierarchy[$user_level] ?? 0;
        
        if ($user_level_value < $required_level_value) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header("Location: dashboard.php");
            exit();
        }
    }
}

// Lấy thông tin user hiện tại
function getCurrentUser() {
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] == 'admin') {
            return [
                'type' => 'admin',
                'id' => $_SESSION['admin_id'],
                'name' => $_SESSION['admin_name'],
                'level' => $_SESSION['admin_level']
            ];
        } else {
            return [
                'type' => 'customer',
                'id' => $_SESSION['customer_id'],
                'name' => $_SESSION['customer_name']
            ];
        }
    }
    return null;
}

// Kiểm tra xem user đã đăng nhập chưa
function isLoggedIn() {
    return isset($_SESSION['user_type']);
}

// Lấy loại user
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}
?>