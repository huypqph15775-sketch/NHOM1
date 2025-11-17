<?php
include("includes/database.php");
include("functions/functions.php");

if(isset($_POST['register'])){
    $customer_name = $_POST['customer_name'];
    $customer_sex = $_POST['customer_sex'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $customer_user_name = $_POST['customer_user_name'];
    $customer_password = $_POST['customer_password'];
    $customer_repw = $_POST['customer_repw'];
    $account_status = "Active";
    $customer_img = $_FILES['customer_img']['name'];
    $customer_img_tmp = $_FILES['customer_img']['tmp_name'];
    
    // Kiểm tra mật khẩu nhập lại
    if($customer_password !== $customer_repw){
        echo "<script>alert('Mật khẩu nhập lại không khớp!')</script>";
        exit();
    }

    // Hash mật khẩu trước khi lưu
    $customer_password_hashed = password_hash($customer_password, PASSWORD_DEFAULT);

    // Upload ảnh
    move_uploaded_file($customer_img_tmp, "customer/customer_img/$customer_img");

    // Insert vào DB
    $insert_customer = "INSERT INTO customer
    (customer_name, customer_sex, customer_email, customer_phone, customer_address, customer_user_name, customer_password, customer_img, account_status) 
    VALUES
    ('$customer_name', '$customer_sex', '$customer_email', '$customer_phone', '$customer_address', '$customer_user_name', '$customer_password_hashed', '$customer_img', '$account_status')";

    $run_customer = mysqli_query($conn, $insert_customer);

    if($run_customer){
        echo "<script>alert('Đăng ký tài khoản thành công')</script>";
        echo "<script>window.open('signin.php', '_self')</script>";
    } else {
        echo "<script>alert('Lỗi khi đăng ký, vui lòng thử lại')</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký - Phone Store</title>
  <link rel="icon" href="images/phone.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/signup.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">Đăng ký</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="customer_sex" class="form-select" required>
                            <option value="">Chọn giới tính</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="customer_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="customer_address" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="customer_user_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="customer_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="customer_repw" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="customer_img" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Đăng ký</button>
                </form>
                <p class="mt-3 text-center">Đã có tài khoản? <a href="signin.php">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
