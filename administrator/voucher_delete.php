<?php
require_once '../includes/auth.php';
checkAdminLogin();
// Chỉ admin cấp cao (level >= 4) mới được xóa voucher
checkPermission(4);

include("includes/database.php");

if (!isset($_GET['voucher_id'])) {
    echo "<script>window.open('index.php?voucher_list','_self')</script>";
    exit();
}

$voucher_id = (int)$_GET['voucher_id'];

// Set inactive thay vì xóa hẳn
$sql = "UPDATE vouchers SET status = 'inactive' WHERE voucher_id = '$voucher_id' LIMIT 1";
mysqli_query($conn, $sql);

echo "<script>window.open('index.php?voucher_list','_self')</script>";
exit();
