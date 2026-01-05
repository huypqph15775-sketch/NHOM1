<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'includes/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  echo json_encode(['success' => false, 'message' => 'Invalid method']);
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if($username === '' || $password === ''){
  echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu']);
  exit;
}

// Basic rate-limiting via session
$session_key = 'login_attempts_' . md5($username);
if(!isset($_SESSION[$session_key])){
  $_SESSION[$session_key] = ['attempts'=>0,'first'=>time(),'locked_until'=>0];
}
$attempts = &$_SESSION[$session_key];
$max_attempts = 5;
$lockout_time = 15*60;
if($attempts['locked_until'] > time()){
  echo json_encode(['success'=>false, 'message'=>'Tài khoản bị khóa tạm thời. Thử lại sau vài phút.']);
  exit;
}

$login_success = false;

// Try customer
$sql_customer = "SELECT c.*, r.role_name, r.role_level FROM customer c LEFT JOIN roles r ON c.role_id = r.role_id WHERE c.customer_user_name = ? AND c.account_status = 'Active' LIMIT 1";
if($stmt = $conn->prepare($sql_customer)){
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $res = $stmt->get_result();
  if($res && $res->num_rows>0){
    $user = $res->fetch_assoc();
    $stored = (string)$user['customer_password'];
    $valid = false;
    if(strlen($stored) >= 60 && strpos($stored, '$2') === 0){
      $valid = password_verify($password, $stored);
    } else {
      $valid = ($password === $stored || $password == $stored);
    }
    if($valid){
      session_regenerate_id(true);
      $_SESSION['customer_id'] = (int)$user['customer_id'];
      $_SESSION['customer_name'] = htmlspecialchars($user['customer_name'], ENT_QUOTES, 'UTF-8');
      $_SESSION['user_type'] = 'customer';
      $_SESSION['role_id'] = (int)($user['role_id'] ?? 1);
      $_SESSION['role_name'] = $user['role_name'] ?? 'customer';
      $_SESSION['role_level'] = (int)($user['role_level'] ?? 1);
      $_SESSION['login_time'] = time();
      $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
      $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
      unset($_SESSION[$session_key]);
      $login_success = true;
    }
  }
  $stmt->close();
}

if(!$login_success){
  // Try admin
  $sql_admin = "SELECT a.*, r.role_name, r.role_level FROM admin a LEFT JOIN roles r ON a.role_id = r.role_id WHERE a.admin_user_name = ? LIMIT 1";
  if($stmt2 = $conn->prepare($sql_admin)){
    $stmt2->bind_param('s', $username);
    $stmt2->execute();
    $r2 = $stmt2->get_result();
    if($r2 && $r2->num_rows>0){
      $admin = $r2->fetch_assoc();
      $stored = (string)$admin['admin_password'];
      $valid = false;
      if(strlen($stored) >= 60 && strpos($stored, '$2') === 0){
        $valid = password_verify($password, $stored);
      } else {
        $valid = ($password === $stored || $password == $stored);
      }
      if($valid){
  session_regenerate_id(true);
  $_SESSION['user_type'] = 'admin';
  $_SESSION['admin_id'] = (int)$admin['admin_id'];
  $_SESSION['admin_name'] = htmlspecialchars($admin['admin_name'], ENT_QUOTES, 'UTF-8');
  // store login username too so permission helpers can match it
  $_SESSION['admin_user_name'] = htmlspecialchars($admin['admin_user_name'], ENT_QUOTES, 'UTF-8');
        $_SESSION['role_id'] = (int)$admin['role_id'];
        $_SESSION['role_name'] = $admin['role_name'] ?? 'admin';
        $_SESSION['role_level'] = (int)($admin['role_level'] ?? 4);
        $_SESSION['admin_level'] = (int)($admin['admin_level_number'] ?? 1);
        if(isset($_SESSION['role_level'])){
          $role_lvl = (int)$_SESSION['role_level'];
          if(empty($_SESSION['admin_level']) || $_SESSION['admin_level'] < $role_lvl){
            $_SESSION['admin_level'] = $role_lvl;
          }
        }
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        unset($_SESSION[$session_key]);
        $login_success = true;
      }
    }
    $stmt2->close();
  }
}

if($login_success){
  // Include a suggested redirect URL for the client-side to follow
  $suggested = '';
  if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'){
    $login_name = strtolower($_SESSION['admin_user_name'] ?? '');
    if(strpos($login_name, 'nvbanhang') === 0){
      $suggested = 'administrator/index.php?pending_orders';
    } elseif(strpos($login_name, 'nvkho') === 0){
      $suggested = 'administrator/index.php?stock_list';
    } else {
      $suggested = 'administrator/index.php?dashboard';
    }
  } else {
    $suggested = 'index.php';
  }

  echo json_encode(['success' => true, 'message' => 'Đăng nhập thành công', 'redirect' => $suggested]);
} else {
  // record attempt
  $_SESSION[$session_key]['attempts'] = ($_SESSION[$session_key]['attempts'] ?? 0) + 1;
  if($_SESSION[$session_key]['attempts'] >= $max_attempts){
    $_SESSION[$session_key]['locked_until'] = time() + $lockout_time;
  }
  echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
}

exit;
