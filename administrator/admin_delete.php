<?php
require_once '../includes/auth.php';
checkAdminLogin();
// Chỉ admin (role_level >= 4) trở lên mới được xóa admin
checkPermission('admin');

if (!isset($_SESSION['admin_id'])) {
    echo "<script>window.open('signin.php', '_self')</script>";
} else {
    if (isset($_GET['admin_delete'])) {
        $admin_id = $_GET['admin_delete'];

        // Không cho phép tự xóa chính mình
        if ($_SESSION['admin_id'] == $admin_id) {
            echo "<script>alert('Bạn không thể tự xóa tài khoản của mình. ')</script>";
            echo "<script>window.open('index.php?admin_list', '_self')</script>";
        } else {
            $delete_admin = "DELETE FROM admin WHERE admin_id = '$admin_id'";
            $run_delete   = mysqli_query($conn, $delete_admin);

            if ($run_delete) {
                echo "<script>alert('Xóa admin thành công')</script>";
                echo "<script>window.open('index.php?admin_list', '_self')</script>";
            }
        }
    }
}
?>
