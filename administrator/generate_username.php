<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/database.php';

$role = $_GET['role'] ?? '';
$role = strtolower(trim($role));
if ($role !== 'sales' && $role !== 'warehouse') {
    echo json_encode(['ok' => false, 'message' => 'Invalid role']);
    exit;
}
$prefix = ($role === 'sales') ? 'nvbanhang' : 'nvkho';
$escaped_prefix = mysqli_real_escape_string($conn, $prefix);
$like = $escaped_prefix . '%';
$query = "SELECT admin_user_name FROM admin WHERE admin_user_name LIKE '$like'";
$res = mysqli_query($conn, $query);
$max_index = -1;
while ($row = mysqli_fetch_assoc($res)) {
    $name = $row['admin_user_name'];
    $tail = substr($name, strlen($prefix));
    if ($tail === '') {
        $num = 0;
    } elseif (ctype_digit($tail)) {
        $num = (int)$tail;
    } else {
        continue;
    }
    if ($num > $max_index) $max_index = $num;
}
$new_index = $max_index + 1;
if ($new_index === 0) {
    $username = $prefix;
} else {
    $username = $prefix . $new_index;
}

echo json_encode(['ok' => true, 'username' => $username]);
exit;
