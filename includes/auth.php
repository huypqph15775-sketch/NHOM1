<?php
// Đảm bảo session luôn được khởi tạo trước khi dùng $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Bắt buộc phải đăng nhập với vai trò admin
 */
function checkAdminLogin() {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        // Đưa về trang đăng nhập chung
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
 * Bắt buộc phải đăng nhập với vai trò customer
 */
function checkCustomerLogin() {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
 * Kiểm tra quyền theo cấp độ admin
 * - Hỗ trợ cả tên (Kho, Nhân viên, Quản lý)
 * - Và số (0–4) nếu sau này bạn dùng role 0–4
 */
function checkPermission($required_level) {
    // Nếu chưa có admin_level thì coi như không có quyền
    if (!isset($_SESSION['admin_level'])) {
        $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
        header("Location: /phonestoree/administrator/index.php");
        exit();
    }

    // Bảng ánh xạ quyền
    $level_hierarchy = [
        // Dạng số (role 0–4)
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,

        // Dạng tên cũ
        'Kho'       => 1,
        'Nhân viên' => 2,
        'Quản lý'   => 3,
    ];

    $user_level     = (string)($_SESSION['admin_level']);
    $required_level = (string)$required_level;

    $required_level_value = $level_hierarchy[$required_level] ?? null;
    $user_level_value     = $level_hierarchy[$user_level] ?? null;

    // Nếu không map được quyền hoặc quyền user < quyền yêu cầu → chặn
    if ($required_level_value === null || $user_level_value === null || $user_level_value < $required_level_value) {
        $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
      
    }
}

/**
 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_type'])) {
        return null;
    }

    if ($_SESSION['user_type'] === 'admin') {
        return [
            'type'  => 'admin',
            'id'    => $_SESSION['admin_id']    ?? null,
            'name'  => $_SESSION['admin_name']  ?? null,
            'level' => $_SESSION['admin_level'] ?? null,
        ];
    }

    // Customer
    return [
        'type' => 'customer',
        'id'   => $_SESSION['customer_id']   ?? null,
        'name' => $_SESSION['customer_name'] ?? null,
    ];
}

/**
 * Kiểm tra xem user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_type']);
}

/**
 * Lấy loại user hiện tại (admin / customer / null)
 */
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}
