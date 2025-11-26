<div class="row">
    <div class="col-lg-7 col-md-9 col-12 myaccount-content">
        <h3>Đổi mật khẩu</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label for="oldpw" class="form-label">Mật khẩu cũ</label>
                <input type="password" class="form-control" name="old_password" id="oldpw">
            </div>
            <div class="mb-3">
                <label for="newpw" class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control" name="new_password" id="newpw">
            </div>
            <div class="mb-3">
                <label for="renewpw" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" name="re_new_password" id="renewpw">
            </div>
            <button type="submit" name="change_pw" class="btn btn-primary">Đổi mật khẩu</button>
        </form>
    </div>
</div>

<?php
if(isset($_POST['change_pw'])){
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $re_new_password = $_POST['re_new_password'];
    $set_old_password = "select * from customer where customer_id = '$customer_id' and customer_password = '$old_password'";
    $run_set_old = mysqli_query($conn, $set_old_password);
    $check_old_password = mysqli_fetch_array($run_set_old);
    if($check_old_password == 0){
        echo "<script>alert('Mật khẩu hiện tại không đúng.')</script>";
        exit();
    }
    if($new_password != $re_new_password){
        echo "<script>alert('Mật khẩu mới không trùng khớp.')</script>";
        exit();
    }
    $change_password = "update customer set customer_password = '$new_password'";
    $run_change = mysqli_query($conn, $change_password);
    if($run_change){
        echo "<script>alert('Đổi mật khẩu thành công')</script>";
        echo "<script>window.open('myaccount.php?profile', '_self')</script>";
    }
}
?>