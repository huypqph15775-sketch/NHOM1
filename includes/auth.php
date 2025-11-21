<?php
// Đảm bảo session luôn được khởi tạo trước khi dùng $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
<<<<<<< HEAD
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
=======
 * Bắt buộc phải đăng nhập với vai trò admin
 */
function checkAdminLogin() {
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        // Đưa về trang đăng nhập chung
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
 * Bắt buộc phải đăng nhập với vai trò customer
 */
<<<<<<< HEAD
function checkCustomerLogin()
{
=======
function checkCustomerLogin() {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /phonestoree/signin.php?redirect=" . $redirect);
        exit();
    }
}

/**
<<<<<<< HEAD
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

    $user_level     = (int)($user['role_level'] ?? 0);
    $required_level = normalizeRequiredLevel($required_level);

    if ($user_level < $required_level) {
=======
 * Kiểm tra quyền theo cấp độ admin
 * - Hỗ trợ cả tên (Kho, Nhân viên, Quản lý)
 * - Và số (0–4) nếu sau này bạn dùng role 0–4
 */
function checkPermission($required_level) {
    // Nếu chưa có admin_level thì coi như không có quyền
    if (!isset($_SESSION['admin_level'])) {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
        $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
        header("Location: /phonestoree/administrator/index.php");
        exit();
    }
<<<<<<< HEAD
=======

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
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
}

/**
 * Kiểm tra xem user đã đăng nhập chưa
 */
<<<<<<< HEAD
function isLoggedIn()
{
=======
function isLoggedIn() {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
    return isset($_SESSION['user_type']);
}

/**
 * Lấy loại user hiện tại (admin / customer / null)
 */
<<<<<<< HEAD
function getUserType()
{
=======
function getUserType() {
>>>>>>> a35a6cb48d5e68ef90dd1afcdb21499ab3f4514b
    return $_SESSION['user_type'] ?? null;
}
