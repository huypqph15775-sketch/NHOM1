<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/mailer.php';

$error = '';
$success = false;
$provinces = [
    'Hà Nội' => ['Hoàn Kiếm', 'Đống Đa', 'Hai Bà Trưng', 'Ba Đình', 'Thanh Xuân', 'Cầu Giấy', 'Long Biên'],
    'TP. Hồ Chí Minh' => ['Quận 1', 'Quận 2', 'Quận 3', 'Quận 4', 'Quận 5', 'Quận 6', 'Quận 7', 'Quận 8', 'Quận 9', 'Quận 10'],
    'Đà Nẵng' => ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn', 'Liên Chiểu'],
    'Hải Phòng' => ['Hồng Bàng', 'Ngô Quyền', 'Lê Chân', 'Kiến An', 'Đô Sơn'],
    'Hải Dương' => ['Hải Dương', 'Kinh Môn', 'Cẩm Phả', 'Chí Linh', 'Thanh Hà'],
    'Nghệ An' => ['Vinh', 'Cửa Lò', 'Yên Thành', 'Anh Sơn', 'Con Cuông'],
    'Hưng Yên' => ['Hưng Yên', 'Mỹ Hào', 'Yên Mỹ', 'Khoái Châu'],
    'Thái Bình' => ['Thái Bình', 'Tiền Hải', 'Vũ Thư', 'Hưng Hà'],
    'Nam Định' => ['Nam Định', 'Giao Thủy', 'Ý Yên', 'Trực Ninh'],
    'Thanh Hóa' => ['Thanh Hóa', 'Sầm Sơn', 'Mường Lát', 'Hà Trung']
];

// Two-step registration: initial submit -> send OTP -> verify OTP -> finalize insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Resend OTP from signup pending
  if (isset($_POST['resend_otp']) && isset($_SESSION['signup_pending']) && is_array($_SESSION['signup_pending'])) {
    $pending = &$_SESSION['signup_pending'];
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
      $pending['otp_expires'] = time() + (10 * 60);
      $pending['otp_sent_at'] = $now;
      $pending['otp_send_count'] = $count + 1;

      $subject = 'Mã OTP đăng ký PhoneStore - Gửi lại';
      $body = "<p>Mã xác thực đăng ký của bạn là: <strong>" . htmlspecialchars($otp) . "</strong></p>\n" .
              "<p>Mã có hiệu lực trong 10 phút.</p>";
      $mail_error = null;
      send_email_otp($pending['customer_email'], $subject, $body, $mail_error);
      if ($mail_error) {
        $error = 'Không thể gửi email OTP: ' . $mail_error;
      }
    }
  }

  // If this is OTP verification for signup
  if (isset($_POST['verify_otp']) && isset($_SESSION['signup_pending']) && is_array($_SESSION['signup_pending'])) {
    $input_otp = trim($_POST['otp_code'] ?? '');
    $pending = $_SESSION['signup_pending'];
    $now = time();
    if (empty($input_otp)) {
      $error = 'Vui lòng nhập mã OTP!';
    } elseif (empty($pending['otp_hash']) || empty($pending['otp_expires']) || $pending['otp_expires'] < $now) {
      $error = 'Mã OTP đã hết hạn. Vui lòng gửi lại.';
      unset($_SESSION['signup_pending']);
    } elseif (!password_verify($input_otp, $pending['otp_hash'])) {
      $error = 'Mã OTP không đúng.';
    } else {
      // OTP đúng -> finalize registration
      $customer_name = $pending['customer_name'];
      $customer_sex = $pending['customer_sex'];
      $customer_email = $pending['customer_email'];
      $customer_phone = $pending['customer_phone'];
      $customer_province = $pending['customer_province'];
      $customer_district = $pending['customer_district'];
      $customer_address = $pending['customer_address'];
      $customer_user_name = $pending['customer_user_name'];
      $customer_password_hashed = $pending['customer_password_hashed'];
      $tmp_image = $pending['tmp_image'] ?? '';

      // Move image from tmp to final location
      $customer_img = '';
      if (!empty($tmp_image) && file_exists($tmp_image)) {
        $ext = pathinfo($tmp_image, PATHINFO_EXTENSION);
        $customer_img = 'customer_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $final_path = 'customer/customer_img/' . $customer_img;
        if (!rename($tmp_image, $final_path)) {
          $error = 'Không thể lưu ảnh đại diện.';
        }
      }

      if (empty($error)) {
        $full_address = $customer_province . ', ' . $customer_district . ', ' . $customer_address;
        $conn->begin_transaction();
        try {
          $insert_customer = "INSERT INTO customer
            (customer_name, customer_sex, customer_email, customer_phone, customer_address, 
             customer_user_name, customer_password, customer_img, account_status, role_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active', 1)";
          $stmt_insert = $conn->prepare($insert_customer);
          if ($stmt_insert) {
            $stmt_insert->bind_param(
              'ssssssss',
              $customer_name,
              $customer_sex,
              $customer_email,
              $customer_phone,
              $full_address,
              $customer_user_name,
              $customer_password_hashed,
              $customer_img
            );
            if ($stmt_insert->execute()) {
              $customer_id = $stmt_insert->insert_id;
              $insert_address = "INSERT INTO customer_addresses 
                (customer_id, receiver_name, phone, address_detail, is_default) 
                VALUES (?, ?, ?, ?, 1)";
              $stmt_address = $conn->prepare($insert_address);
              if ($stmt_address) {
                $stmt_address->bind_param('isss', $customer_id, $customer_name, $customer_phone, $full_address);
                if ($stmt_address->execute()) {
                  $conn->commit();
                  $success = true;
                  unset($_SESSION['signup_pending']);
                } else {
                  $conn->rollback();
                  $error = 'Lỗi khi lưu địa chỉ: ' . $conn->error;
                }
                $stmt_address->close();
              } else {
                $conn->rollback();
                $error = 'Lỗi khi chuẩn bị lưu địa chỉ!';
              }
            } else {
              $conn->rollback();
              $error = 'Lỗi khi đăng ký: ' . $conn->error;
            }
            $stmt_insert->close();
          } else {
            $conn->rollback();
            $error = 'Lỗi khi chuẩn bị đăng ký!';
          }
        } catch (Exception $e) {
          $conn->rollback();
          $error = 'Lỗi không xác định: ' . $e->getMessage();
        }
      }
    }
  }

  // Initial registration submission: validate then send OTP
  if (isset($_POST['register']) && empty($success) && empty($error)) {
    // Validate & sanitize input
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_sex = trim($_POST['customer_sex'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_province = trim($_POST['customer_province'] ?? '');
    $customer_district = trim($_POST['customer_district'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $customer_user_name = trim($_POST['customer_user_name'] ?? '');
    $customer_password = $_POST['customer_password'] ?? '';
    $customer_repw = $_POST['customer_repw'] ?? '';

    if (empty($customer_name)) {
      $error = 'Vui lòng nhập họ và tên!';
    } elseif (empty($customer_sex)) {
      $error = 'Vui lòng chọn giới tính!';
    } elseif (empty($customer_email)) {
      $error = 'Vui lòng nhập email!';
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Email không hợp lệ!';
    } elseif (empty($customer_phone)) {
      $error = 'Vui lòng nhập số điện thoại!';
    } elseif (!preg_match('/^0\d{9}$/', $customer_phone)) {
      $error = 'Số điện thoại phải là 10 số và bắt đầu bằng 0 (VD: 0123456789)!';
    } elseif (empty($customer_province)) {
      $error = 'Vui lòng chọn tỉnh/thành phố!';
    } elseif (empty($customer_district)) {
      $error = 'Vui lòng chọn quận/huyện!';
    } elseif (empty($customer_address)) {
      $error = 'Vui lòng nhập địa chỉ chi tiết!';
    } elseif (strlen($customer_address) < 5) {
      $error = 'Địa chỉ phải có ít nhất 5 ký tự!';
    } elseif (empty($customer_user_name)) {
      $error = 'Vui lòng nhập tên đăng nhập!';
    } elseif (strlen($customer_user_name) < 3 || strlen($customer_user_name) > 50) {
      $error = 'Tên đăng nhập phải từ 3-50 ký tự!';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $customer_user_name)) {
      $error = 'Tên đăng nhập chỉ được chứa chữ, số, và dấu gạch dưới!';
    } elseif (empty($customer_password)) {
      $error = 'Vui lòng nhập mật khẩu!';
    } elseif (strlen($customer_password) < 6) {
      $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } elseif ($customer_password !== $customer_repw) {
      $error = 'Mật khẩu nhập lại không khớp!';
    } else {
      // Check username & email uniqueness
      $check_user = "SELECT customer_id FROM customer WHERE customer_user_name = ? LIMIT 1";
      $stmt_check = $conn->prepare($check_user);
      if ($stmt_check) {
        $stmt_check->bind_param('s', $customer_user_name);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
          $error = 'Tên đăng nhập đã tồn tại! Vui lòng chọn tên khác.';
        }
        $stmt_check->close();
      }

      if (empty($error)) {
        $check_email = "SELECT customer_id FROM customer WHERE customer_email = ? LIMIT 1";
        $stmt_check_email = $conn->prepare($check_email);
        if ($stmt_check_email) {
          $stmt_check_email->bind_param('s', $customer_email);
          $stmt_check_email->execute();
          $result_check_email = $stmt_check_email->get_result();
          if ($result_check_email->num_rows > 0) {
            $error = 'Email này đã được đăng ký! Vui lòng sử dụng email khác.';
          }
          $stmt_check_email->close();
        }
      }

      // Handle image upload to temporary folder
      if (empty($error)) {
        $customer_img_tmp = '';
        if (isset($_FILES['customer_img']) && $_FILES['customer_img']['error'] === 0) {
          $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
          $max_size = 5 * 1024 * 1024; // 5MB
          $file_type = $_FILES['customer_img']['type'];
          $file_size = $_FILES['customer_img']['size'];
          if (!in_array($file_type, $allowed_types)) {
            $error = 'Chỉ chấp nhận các định dạng ảnh: JPEG, PNG, GIF, WebP!';
          } elseif ($file_size > $max_size) {
            $error = 'Kích thước ảnh không được vượt quá 5MB!';
          } else {
            if (!is_dir(__DIR__ . '/../tmp_uploads')) {
              @mkdir(__DIR__ . '/../tmp_uploads', 0755, true);
            }
            $file_extension = pathinfo($_FILES['customer_img']['name'], PATHINFO_EXTENSION);
            $tmp_name = __DIR__ . '/../tmp_uploads/customer_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file_extension;
            if (!move_uploaded_file($_FILES['customer_img']['tmp_name'], $tmp_name)) {
              $error = 'Lỗi khi upload ảnh tạm thời!';
            } else {
              $customer_img_tmp = $tmp_name;
            }
          }
        } else {
          $error = 'Vui lòng chọn ảnh đại diện!';
        }
                
        if (empty($error)) {
          // Generate OTP and store pending data in session
          $otp = random_int(100000, 999999);
          $otp_hash = password_hash((string)$otp, PASSWORD_DEFAULT);
          $otp_expires = time() + (10 * 60); // 10 minutes

                    $_SESSION['signup_pending'] = [
            'customer_name' => $customer_name,
            'customer_sex' => $customer_sex,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'customer_province' => $customer_province,
            'customer_district' => $customer_district,
            'customer_address' => $customer_address,
            'customer_user_name' => $customer_user_name,
            'customer_password_hashed' => password_hash($customer_password, PASSWORD_BCRYPT, ['cost' => 12]),
            'tmp_image' => $customer_img_tmp,
            'otp_hash' => $otp_hash,
            'otp_expires' => $otp_expires,
                    'otp_sent_at' => time(),
                    'otp_send_count' => 1,
          ];

          $subject = 'Mã OTP đăng ký PhoneStore';
          $body = "<p>Mã xác thực đăng ký của bạn là: <strong>" . htmlspecialchars($otp) . "</strong></p>\n" .
              "<p>Mã có hiệu lực trong 10 phút.</p>";
          $mail_error = null;
          send_email_otp($customer_email, $subject, $body, $mail_error);
          if ($mail_error) {
            $error = 'Không thể gửi email OTP: ' . $mail_error;
            // cleanup tmp image
            if (!empty($customer_img_tmp) && file_exists($customer_img_tmp)) @unlink($customer_img_tmp);
            unset($_SESSION['signup_pending']);
          } else {
            // Show OTP input on the same page (rendered below using session)
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký - PhoneStore</title>
  <link rel="icon" href="images/phone.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<div class="auth-container">
  <div class="card shadow">
    <div class="card-body p-4">
      <h2 class="text-center mb-4">
        <i class="fas fa-user-plus"></i> Đăng ký tài khoản
      </h2>
      
      <?php if ($success): ?>
        <div class="success-msg">
          <i class="fas fa-check-circle"></i> <strong>Thành công!</strong> 
          Tài khoản đã được tạo. Vui lòng <a href="signin.php">đăng nhập</a> để tiếp tục.
        </div>
      <?php elseif (!empty($error)): ?>
        <div class="error-msg">
          <i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['signup_pending']) && !empty($_SESSION['signup_pending']) && !$success):
        $pending = $_SESSION['signup_pending']; ?>
        <form method="POST" action="">
          <p>Chúng tôi đã gửi mã OTP tới email <strong><?php echo htmlspecialchars($pending['customer_email']); ?></strong>. Vui lòng nhập mã (6 chữ số).</p>
          <div class="form-group">
            <label class="form-label">Mã OTP</label>
            <input type="text" name="otp_code" class="form-control" maxlength="6" required placeholder="123456">
          </div>
          <div class="d-grid gap-2">
            <button type="submit" name="verify_otp" class="btn btn-primary">Xác thực OTP</button>
            <button type="submit" name="resend_otp" class="btn btn-outline-secondary">Gửi lại mã OTP</button>
          </div>
          <small class="text-muted">Bạn có thể gửi lại tối đa 5 lần, chờ 60 giây giữa các lần gửi.</small>
        </form>
      <?php else: ?>
      <form method="POST" action="" enctype="multipart/form-data" id="signupForm" novalidate>
        <div class="form-group">
          <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" class="form-control" required 
                 value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>"
                 placeholder="Nhập họ và tên của bạn">
          <small class="form-text text-muted">Tối thiểu 3 ký tự</small>
        </div>

        <div class="form-group">
          <label class="form-label">Giới tính <span class="text-danger">*</span></label>
          <select name="customer_sex" class="form-select" required>
            <option value="">-- Chọn giới tính --</option>
            <option value="Nam" <?php echo (isset($_POST['customer_sex']) && $_POST['customer_sex'] === 'Nam') ? 'selected' : ''; ?>>Nam</option>
            <option value="Nữ" <?php echo (isset($_POST['customer_sex']) && $_POST['customer_sex'] === 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="customer_email" class="form-control" required
                 value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>"
                 placeholder="example@gmail.com">
        </div>

        <div class="form-group">
          <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
          <input type="tel" name="customer_phone" class="form-control" required
                 value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>"
                 placeholder="0123456789" inputmode="numeric">
          <small class="form-text text-muted">Phải là 10 số bắt đầu bằng 0 (VD: 0123456789)</small>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
            <select name="customer_province" class="form-select" id="provinceSelect" required>
              <option value="">-- Chọn tỉnh/thành phố --</option>
              <?php foreach ($provinces as $province => $districts): ?>
                <option value="<?php echo htmlspecialchars($province); ?>" 
                  <?php echo (isset($_POST['customer_province']) && $_POST['customer_province'] === $province) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($province); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group col-md-6">
            <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
            <select name="customer_district" class="form-select" id="districtSelect" required>
              <option value="">-- Chọn quận/huyện --</option>
              <?php 
              if (isset($_POST['customer_province']) && isset($provinces[$_POST['customer_province']])) {
                  foreach ($provinces[$_POST['customer_province']] as $district) {
                      $selected = (isset($_POST['customer_district']) && $_POST['customer_district'] === $district) ? 'selected' : '';
                      echo "<option value='" . htmlspecialchars($district) . "' $selected>" . htmlspecialchars($district) . "</option>";
                  }
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
          <input type="text" name="customer_address" class="form-control" required
                 value="<?php echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : ''; ?>"
                 placeholder="Nhập số nhà, tên đường, phường">
          <small class="form-text text-muted">Tối thiểu 5 ký tự (VD: 123 Đường Lê Lợi)</small>
        </div>

        <div class="form-group">
          <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
          <input type="text" name="customer_user_name" class="form-control" required
                 value="<?php echo isset($_POST['customer_user_name']) ? htmlspecialchars($_POST['customer_user_name']) : ''; ?>"
                 placeholder="Nhập tên đăng nhập (3-50 ký tự)">
          <small class="form-text text-muted">3-50 ký tự, chỉ chứa chữ, số, dấu gạch dưới</small>
        </div>

        <div class="form-group">
          <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
          <input type="password" id="password" name="customer_password" class="form-control" required
                 placeholder="Tối thiểu 6 ký tự">
          <div class="password-strength" id="passwordStrength"></div>
          <br>
          <small class="form-text text-muted">Tối thiểu 6 ký tự, nên có chữ hoa, số, ký tự đặc biệt</small>
        </div>

        <div class="form-group">
          <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
          <input type="password" name="customer_repw" class="form-control" required
                 placeholder="Nhập lại mật khẩu">
        </div>

        <div class="form-group">
          <label class="form-label">Ảnh đại diện <span class="text-danger">*</span></label>
          <input type="file" name="customer_img" class="form-control" required accept="image/*"
                 id="customerImg">
          <small class="form-text text-muted">Định dạng: JPG, PNG, GIF, WebP. Tối đa 5MB</small>
          <div id="imagePreview" style="margin-top: 10px;"></div>
        </div>

        <button type="submit" name="register" class="btn-auth">
          <i class="fas fa-user-check"></i> Đăng ký
        </button>
      </form>
      <?php endif; ?>

      <div class="links">
        <a href="signin.php">Đăng nhập</a>
        <a href="forgot_password.php">Quên mật khẩu</a>
        <a href="index.php">Về trang chủ</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Dữ liệu tỉnh/thành phố
  const provincesData = <?php echo json_encode($provinces); ?>;

  // Handle province selection
  document.getElementById('provinceSelect').addEventListener('change', function() {
    const selectedProvince = this.value;
    const districtSelect = document.getElementById('districtSelect');
    
    districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
    
    if (selectedProvince && provincesData[selectedProvince]) {
      provincesData[selectedProvince].forEach(district => {
        const option = document.createElement('option');
        option.value = district;
        option.textContent = district;
        districtSelect.appendChild(option);
      });
    }
  });

  // Password strength indicator
  document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = document.getElementById('passwordStrength');
    let score = 0;
    
    if (password.length >= 6) score++;
    if (password.length >= 10) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
    const labels = ['Yếu', 'Tạm được', 'Trung bình', 'Mạnh', 'Rất mạnh'];
    
    strength.style.backgroundColor = colors[Math.min(score - 1, 4)];
    strength.textContent = password ? labels[Math.min(score - 1, 4)] : '';
  });
  
  // Image preview
  document.getElementById('customerImg').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const file = e.target.files[0];
    
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        preview.innerHTML = '<img src="' + event.target.result + '" style="max-width: 100px; border-radius: 5px;">';
      };
      reader.readAsDataURL(file);
    }
  });
  
  // Phone number validation
  document.querySelector('input[name="customer_phone"]').addEventListener('blur', function() {
    const phone = this.value.trim();
    if (phone && !(/^0\d{9}$/.test(phone))) {
      this.classList.add('is-invalid');
    } else {
      this.classList.remove('is-invalid');
    }
  });

  // Username validation
  document.querySelector('input[name="customer_user_name"]').addEventListener('blur', function() {
    const username = this.value.trim();
    if (username && !(/^[a-zA-Z0-9_]+$/.test(username))) {
      this.classList.add('is-invalid');
    } else {
      this.classList.remove('is-invalid');
    }
  });
  
  // Form validation
  document.getElementById('signupForm').addEventListener('submit', function(e) {
    const phone = document.querySelector('input[name="customer_phone"]').value.trim();
    const username = document.querySelector('input[name="customer_user_name"]').value.trim();
    const password = document.querySelector('input[name="customer_password"]').value;
    const confirmPassword = document.querySelector('input[name="customer_repw"]').value;
    
    // Validate phone
    if (!(/^0\d{9}$/.test(phone))) {
      e.preventDefault();
      alert('Số điện thoại phải là 10 số và bắt đầu bằng 0!');
      return false;
    }
    
    // Validate username
    if (!(/^[a-zA-Z0-9_]{3,50}$/.test(username))) {
      e.preventDefault();
      alert('Tên đăng nhập chỉ được chứa chữ, số, dấu gạch dưới (3-50 ký tự)!');
      return false;
    }

    // Validate password
    if (password !== confirmPassword) {
      e.preventDefault();
      alert('Mật khẩu không khớp!');
      return false;
    }
    
    if (password.length < 6) {
      e.preventDefault();
      alert('Mật khẩu phải có ít nhất 6 ký tự!');
      return false;
    }
  });
</script>
</body>
</html>
