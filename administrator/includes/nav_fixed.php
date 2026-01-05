<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Kết nối DB & các hàm trong thư mục administrator
include("includes/database.php");
include("functions/functions.php");

// Sử dụng hệ thống phân quyền chung
require_once __DIR__ . '/../../includes/auth.php';

// Bắt buộc phải là admin mới vào được khu administrator
checkAdminLogin();

// Lấy thông tin user hiện tại (admin đang đăng nhập)
 $current_user = getCurrentUser();
// DEPRECATED: temporary fallback sidebar used during edits.
// The file is intentionally kept minimal now. Remove this file when you're
// ready. Restored nav.php is the primary sidebar.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// small no-op placeholder
// (kept to avoid include errors in case any code still references this file)
?>
