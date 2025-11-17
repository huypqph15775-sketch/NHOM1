<div>
    <div class="myaccount-content">
        <h3><?php echo $customer_name; ?></h3>
        <div class="row">
            <div class="table-responsive col-8">
                <table class="table table-striped table-hover">
                    <tr>
                        <td>Giới tính: </td>
                        <td><strong><?php echo $customer_sex; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Tên tài khoản: </td>
                        <td><strong><?php echo $customer_user_name; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Email: </td>
                        <td><strong><?php echo $customer_email; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Số điện thoại: </td>
                        <td><strong><?php echo $customer_phone; ?></strong></td>
                    </tr>
                    <tr>
                        <td>Địa chỉ: </td>
                        <td><strong><?php echo $customer_address; ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="welcome mt-5">
            <p>Xin chào,  <strong><?php echo $customer_name; ?></strong> (Không phải là <strong><?php echo $customer_name; ?> ? </strong><a href="../signout.php" class="signup">Đăng xuất</a>)</p>
        </div>
    </div>
</div>