<?php
// Đảm bảo session luôn được khởi tạo trước khi dùng $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Lấy thông tin user hiện tại từ session
 */
function getCurrentUser()
{
    if (!isset($_SESSION['user_type'])) {
        return null;
    }

    // ADMIN
    if ($_SESSION['user_type'] === 'admin') {
        return [
            'type'       => 'admin',
            'id'         => $_SESSION['admin_id']    ?? null,
            'name'       => $_SESSION['admin_name']  ?? null,
            // Thông tin quyền theo bảng roles
            'role_id'    => $_SESSION['role_id']     ?? null,
            'role_name'  => $_SESSION['role_name']   ?? null,   // admin / super_admin
            'role_level' => $_SESSION['role_level']  ?? 0,      // 4, 5, ...
            // Giữ lại level cũ nếu bạn đang dùng để hiển thị
            'level'      => $_SESSION['admin_level'] ?? null,
        ];
    }

    // CUSTOMER
    if ($_SESSION['user_type'] === 'customer') {
        return [
            'type'       => 'customer',
            'id'         => $_SESSION['customer_id']   ?? null,
            'name'       => $_SESSION['customer_name'] ?? null,
            'role_id'    => $_SESSION['role_id']       ?? 1,
            'role_name'  => $_SESSION['role_name']     ?? 'customer',
            'role_level' => $_SESSION['role_level']    ?? 1,
        ];
    }

    return null;
}

/**
 * Bắt buộc phải đăng nhập với vai trò admin
 */
function checkAdminLogin()
{
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
 * Bắt buộc phải đăng nhập với vai trò customer
 */
function checkCustomerLogin()
{
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
 * Chuẩn hoá $required_level sang số (dựa trên role_level)
 * Cho phép truyền:
 *  - số: 1..5 (1 customer, 4 admin, 5 super_admin)
 *  - chuỗi: 'admin', 'super_admin', 'Quản trị', 'Toàn quyền hệ thống', 'Quản lý' (tương thích code cũ)
 */
function normalizeRequiredLevel($required_level): int
{
    if (is_numeric($required_level)) {
        return (int)$required_level;
    }

    $map = [
        'customer'            => 1,
        'warehouse'           => 2,
        'staff'               => 3,
        'admin'               => 4,
        'quản trị'            => 4,
        'quản lý'             => 4,  // tương thích code cũ
        'super_admin'         => 5,
        'super admin'         => 5,
        'toàn quyền hệ thống' => 5,
    ];

    $key = mb_strtolower(trim((string)$required_level), 'UTF-8');
    return $map[$key] ?? 0;
}

/**
 * Kiểm tra quyền theo role_level
 *  - Ví dụ:
 *      + chỉ cho admin trở lên:     checkPermission(4);
 *      + chỉ cho super_admin:       checkPermission(5);
 *      + cho staff trở lên:         checkPermission(3);
 */
function checkPermission($required_level)
{
    $user = getCurrentUser();

    // Chưa đăng nhập hoặc không phải admin
    if (!$user || $user['type'] !== 'admin') {
        $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
        header("Location: /phonestoree/index.php");
        exit();
    }

    // Dùng admin_level (numeric) thay vì role_level
    $user_level     = (int)($_SESSION['admin_level'] ?? 0);
    $required_level = normalizeRequiredLevel($required_level);

    if ($user_level < $required_level) {
        $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này! (Yêu cầu level >= $required_level, bạn có level $user_level)";
        header("Location: /phonestoree/administrator/index.php");
        exit();
    }
}

/**
 * Like checkPermission(), but allow a short list of admin usernames to bypass the
 * numeric level check for specific pages (useful for named staff accounts).
 *
 * @param mixed $required_level  Numeric or string level as per normalizeRequiredLevel()
 * @param array $allowed_usernames  Array of admin_name strings (case-sensitive) that are permitted
 */
function checkPermissionOrAllowUser($required_level, array $allowed_usernames = [])
{
    // If current logged-in admin display name or login username is in allowed list, allow access
    // Support entries in $allowed_usernames that end with '*' to indicate prefix matches
    $current_admin_name = $_SESSION['admin_name'] ?? null;
    $current_admin_user = $_SESSION['admin_user_name'] ?? null;
    foreach ($allowed_usernames as $allowed) {
        if (!is_string($allowed) || $allowed === '') continue;
        if (substr($allowed, -1) === '*') {
            $prefix = substr($allowed, 0, -1);
            if ($prefix !== '') {
                if ($current_admin_user && strpos($current_admin_user, $prefix) === 0) return;
                if ($current_admin_name && strpos($current_admin_name, $prefix) === 0) return;
            }
        } else {
            if ($current_admin_user && $current_admin_user === $allowed) return;
            if ($current_admin_name && $current_admin_name === $allowed) return;
        }
    }

    // Otherwise fall back to standard permission check
    checkPermission($required_level);
}

/**
 * Kiểm tra xem user đã đăng nhập chưa
 */
function isLoggedIn()
{
    return isset($_SESSION['user_type']);
}

/**
 * Lấy loại user hiện tại (admin / customer / null)
 */
function getUserType()
{
    return $_SESSION['user_type'] ?? null;
}
