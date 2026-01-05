<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/mailer.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Allow user to go back to email input
  if (isset($_POST['back_to_email'])) {
    unset($_SESSION['reset_pending']);
    unset($_SESSION['reset_verified']);
    // Redirect to clear POST
    header('Location: forgot_password.php');
    exit;
  }
  // Resend OTP for reset
  if (isset($_POST['resend_otp']) && isset($_SESSION['reset_pending']) && is_array($_SESSION['reset_pending'])) {
    $pending = &$_SESSION['reset_pending'];
    $now = time();
    $last = $pending['otp_sent_at'] ?? 0;
    $count = $pending['otp_send_count'] ?? 0;
    if ($now - $last < 60) {
      $error = 'Vui lòng đợi ít nhất 60 giây trước khi gửi lại mã.';
    } elseif ($count >= 5) {
      $error = 'Đã vượt quá số lần gửi mã OTP. Vui lòng thử lại sau.';
    } else {
      $otp = random_int(100000, 999999);
      $otp_hash = password_hash((string)$otp, PASSWORD_DEFAULT);
      $pending['otp_hash'] = $otp_hash;
      $pending['otp_expires'] = time() + 10*60;
      $pending['otp_sent_at'] = $now;
      $pending['otp_send_count'] = $count + 1;
      $subject = 'Mã OTP đặt lại mật khẩu PhoneStore - Gửi lại';
      $body = '<p>Mã OTP đặt lại mật khẩu của bạn: <strong>' . htmlspecialchars($otp) . '</strong></p>' .
          '<p>Mã có hiệu lực trong 10 phút.</p>';
      $mail_err = null;
      send_email_otp($pending['customer_email'], $subject, $body, $mail_err);
      if ($mail_err) {
        $error = 'Không thể gửi email: ' . $mail_err;
      }
    }
  }
    // Step 1: request reset (send OTP)
    if (isset($_POST['request_reset'])) {
        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Vui lòng nhập email hợp lệ.';
        } else {
            // check email exists
            $sql = "SELECT customer_id, customer_name FROM customer WHERE customer_email = ? LIMIT 1";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $otp = random_int(100000, 999999);
                    $otp_hash = password_hash((string)$otp, PASSWORD_DEFAULT);
                    $_SESSION['reset_pending'] = [
                      'customer_id' => $row['customer_id'],
                      'customer_email' => $email,
                      'customer_name' => $row['customer_name'],
                      'otp_hash' => $otp_hash,
                      'otp_expires' => time() + 10*60,
                      'otp_sent_at' => time(),
                      'otp_send_count' => 1
                    ];
                    $subject = 'Mã OTP đặt lại mật khẩu PhoneStore';
                    $body = '<p>Mã OTP đặt lại mật khẩu của bạn: <strong>' . htmlspecialchars($otp) . '</strong></p>' .
                            '<p>Mã có hiệu lực trong 10 phút.</p>';
                    $mail_err = null;
                    send_email_otp($email, $subject, $body, $mail_err);
                    if ($mail_err) {
                        $error = 'Không thể gửi email: ' . $mail_err;
                        unset($_SESSION['reset_pending']);
                    }
                } else {
                    $error = 'Không tìm thấy email trong hệ thống.';
                }
                $stmt->close();
            }
        }
    }

    // Step 2: verify OTP
    if (isset($_POST['verify_reset_otp']) && isset($_SESSION['reset_pending'])) {
        $input = trim($_POST['otp_code'] ?? '');
        $pending = $_SESSION['reset_pending'];
        if (empty($input)) {
            $error = 'Vui lòng nhập mã OTP.';
        } elseif (empty($pending['otp_hash']) || $pending['otp_expires'] < time()) {
            $error = 'Mã OTP đã hết hạn.';
            unset($_SESSION['reset_pending']);
        } elseif (!password_verify($input, $pending['otp_hash'])) {
            $error = 'Mã OTP không đúng.';
        } else {
            $_SESSION['reset_verified'] = $pending['customer_id'];
            // keep pending for safety but mark verified
        }
    }

    // Step 3: set new password
    if (isset($_POST['set_new_password']) && isset($_SESSION['reset_verified'])) {
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['new_password_confirm'] ?? '';
        if (empty($new) || strlen($new) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        } elseif ($new !== $confirm) {
            $error = 'Mật khẩu xác nhận không khớp.';
        } else {
            $hashed = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
            $cust_id = (int)$_SESSION['reset_verified'];
            $update = "UPDATE customer SET customer_password = ? WHERE customer_id = ? LIMIT 1";
            if ($stmt = $conn->prepare($update)) {
                $stmt->bind_param('si', $hashed, $cust_id);
                if ($stmt->execute()) {
                  // Clear reset state and redirect user to signin page
                  unset($_SESSION['reset_pending']);
                  unset($_SESSION['reset_verified']);
                  header('Location: signin.php');
                  exit;
                } else {
                    $error = 'Lỗi khi cập nhật mật khẩu.';
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quên mật khẩu - PhoneStore</title>
  <link rel="stylesheet" href="css/signin.css">
  <link rel="stylesheet" href="css/auth.css">
  <style>
    body{background:#f5f7fb;font-family:Arial,Helvetica,sans-serif}
    .container{max-width:480px;margin:40px auto}
    .forgot-card{background:#fff;border-radius:8px;box-shadow:0 6px 20px rgba(33,47,60,.08);overflow:hidden}
    .forgot-card .card-body{padding:28px}
    h2{margin-bottom:12px;font-size:22px;color:#0d6efd;text-align:center}
    .form-group{margin-bottom:14px}
    .form-control{width:100%;padding:10px 12px;border:1px solid #dfe6ee;border-radius:6px}
    .btn{display:inline-block;padding:10px 14px;border-radius:6px;border:0;background:#0d6efd;color:#fff;cursor:pointer}
    .btn-outline-secondary{background:#fff;border:1px solid #ced4da;color:#333}
    .btn-link{background:none;border:0;color:#0d6efd;padding:0}
    .error-msg{background:#fff0f0;color:#842029;padding:12px;border-radius:6px;margin-bottom:12px;border:1px solid #f5c2c7}
    .success-msg{background:#e6f4ea;color:#0f5132;padding:12px;border-radius:6px;margin-bottom:12px;border:1px solid #c3e6cb}
    .d-grid{display:flex;gap:10px}
    .d-grid .btn{flex:1}
    .d-grid .btn-outline-secondary{flex:1}
    @media (max-width:520px){
      .d-grid{flex-direction:column}
      .d-grid .btn{width:100%}
      .d-grid .btn-outline-secondary{width:100%}
    }
    .text-muted{color:#6c757d;font-size:13px}
    @media (max-width:520px){.container{margin:20px;padding:0 12px}}
  </style>
</head>
<body>
<div class="container">
  <div class="forgot-card">
    <div class="card-body">
      <h2>Quên mật khẩu</h2>
      <?php if ($success): ?>
    <div class="success-msg">Mật khẩu đã được cập nhật. Vui lòng <a href="signin.php">đăng nhập</a>.</div>
      <?php else: ?>
    <?php if (!empty($error)): ?>
      <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['reset_verified'])): ?>
      <form method="POST">
        <div class="form-group">
          <label>Mật khẩu mới</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Xác nhận mật khẩu</label>
          <input type="password" name="new_password_confirm" class="form-control" required>
        </div>
        <button type="submit" name="set_new_password" class="btn btn-primary">Đặt mật khẩu mới</button>
      </form>
    <?php elseif (isset($_SESSION['reset_pending'])): $p = $_SESSION['reset_pending']; ?>
      <p>Đã gửi mã OTP tới <strong><?php echo htmlspecialchars($p['customer_email']); ?></strong></p>
      <form method="POST">
        <div class="form-group">
          <label>Mã OTP</label>
          <input type="text" name="otp_code" class="form-control" maxlength="6" required>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" name="verify_reset_otp" class="btn btn-primary">Xác thực</button>
          <button type="submit" name="resend_otp" class="btn btn-outline-secondary">Gửi lại mã OTP</button>
        </div>
        <small class="text-muted">Bạn có thể gửi lại tối đa 5 lần, chờ 60 giây giữa các lần gửi.</small>
      </form>
      <form method="POST" style="margin-top:10px;">
        <button type="submit" name="back_to_email" class="btn btn-link">← Quay lại và nhập lại email</button>
      </form>
    <?php else: ?>
      <form method="POST">
        <div class="form-group">
          <label>Nhập email đã đăng ký</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" name="request_reset" class="btn btn-primary">Gửi mã OTP</button>
      </form>
      <?php endif; ?>
      <p style="margin-top:14px;text-align:center"><a href="signin.php">Quay về đăng nhập</a></p>
    <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
