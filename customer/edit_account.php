
<div>
    <div class="myaccount-content">
        <h3>Chỉnh sửa thông tin cá nhân</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" class="form-control" value="<?php echo $customer_name; ?>" name="customer_name" id="name">
            </div>
            <div class="mb-3">
                <label for="customer_sex" class="form-label">Giới tính</label>
                <select class="form-select" name="customer_sex" id="customer_sex" aria-label="Giới tính">
                    <option value="Nam" <?php if($customer_sex=="Nam") echo "selected"; ?>>Nam</option>
                    <option value="Nữ" <?php if($customer_sex=="Nữ") echo "selected"; ?>>Nữ</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Tên tài khoản</label>
                <input type="text" class="form-control" value="<?php echo $customer_user_name; ?>" name="customer_user_name" id="username">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo $customer_email; ?>" name="customer_email" id="email">
            </div>
            <div class="mb-3">
                <label for="phonenumber" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" value="<?php echo $customer_phone; ?>" name="customer_phone" id="phonenumber">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" value="<?php echo $customer_address; ?>" name="customer_address" id="address">
            </div>
            <button class="btn btn-primary" name="update">Xác nhận</button>
        </form>
    </div>
</div>

<?php

if(isset($_POST['update'])){
    $customer_name = $_POST['customer_name'];
    $customer_sex = $_POST['customer_sex'];
    $customer_user_name = $_POST['customer_user_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $update_customer = "update customer set customer_name='$customer_name', customer_sex='$customer_sex', customer_user_name='$customer_user_name', 
    customer_email='$customer_email', customer_phone='$customer_phone', customer_address='$customer_address' where customer_id='$customer_id'";
    $run_update = mysqli_query($conn, $update_customer);
    if($run_update){
        echo "<script>alert('Cập nhật thông tin cá nhân thành công. Mời đăng nhập lại.')</script>";
        echo "<script>window.open('../signout.php', '_self')</script>";
    }
}
?>
