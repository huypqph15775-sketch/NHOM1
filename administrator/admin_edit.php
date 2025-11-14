
<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_SESSION['admin_level'])){
        if($_SESSION['admin_level']!='Quản lý'){
            echo "<script>alert('Bạn không đủ quyền truy cập vào chức năng này. ')</script>";
            echo "<script>window.open('index.php?admin_list','_self')</script>";
        }
        else{
            if(isset($_GET['admin_edit'])){
                $admin_id = $_GET['admin_edit'];
                $get_admin = "select * from admin where admin_id='$admin_id'";
                $run_admin = mysqli_query($conn, $get_admin);
                $row_admin = mysqli_fetch_array($run_admin);
                $admin_name = $row_admin['admin_name'];
                $admin_img = $row_admin['admin_img'];
                $admin_email = $row_admin['admin_email'];
                $admin_user_name = $row_admin['admin_user_name'];
                $admin_password = $row_admin['admin_password'];
                $admin_address = $row_admin['admin_address'];
                $admin_contact = $row_admin['admin_contact'];
                $admin_level = $row_admin['admin_level'];
            }
        }
    }  
}
?>
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?admin_list">Admin</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Cập nhật Admin</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="admin_name" class="col-sm-3 col-form-label text-md-end">Tên Admin</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?php echo $admin_name; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_email" class="col-sm-3 col-form-label text-md-end">Email</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_email" name="admin_email" value="<?php echo $admin_email; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_user_name" class="col-sm-3 col-form-label text-md-end">Tên tài khoản</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_user_name" name="admin_user_name" value="<?php echo $admin_user_name; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="admin_password" class="col-sm-3 col-form-label text-md-end">Mật khẩu</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="admin_password" name="admin_password" value="<?php echo $admin_password; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="re_admin_password" class="col-sm-3 col-form-label text-md-end">Nhập lại mật khẩu</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="re_admin_password" name="re_admin_password" value="<?php echo $admin_password; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="admin_img" class="col-sm-3 col-form-label text-md-end">Ảnh đại diện</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" id="admin_img" name="admin_img" accept="images/*" onchange="preview_image(event)">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img id="" src="admin_img/<?php echo $admin_img; ?>" style="z-index: 0;width: 150px; height: 150px; object-fit: contain; <?php if($admin_img==''){echo "display: none;";} ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <img id="displayImg" style="z-index: 0;width: 150px; height: 150px; display: none; object-fit: contain;">
                                    </div>
                                </div> 
                            </div>
                            
                    </div>
                    <div class="row mb-3">
                        <label for="admin_address" class="col-sm-3 col-form-label text-md-end">Địa chỉ</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_address" name="admin_address" value="<?php echo $admin_address; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_contact" class="col-sm-3 col-form-label text-md-end">Điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_contact" name="admin_contact" value="<?php echo $admin_contact; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="admin_level" class="col-sm-3 col-form-label text-md-end">Cấp độ</label>
                            <div class="col-sm-5">
                                <select class="form-select" id="admin_level" name="admin_level">
                                    <option value="Quản lý" <?php if($admin_level == 'Quản lý'){echo "selected";} ?>>Quản lý</option>
                                    <option value="Nhân viên" <?php if($admin_level == 'Nhân viên'){echo "selected";} ?>>Nhân viên</option>
                                </select>
                            </div>
                        </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-md-end">
                            <button type="submit" name="update_admin" class="btn btn-primary">Cập nhật</button>
                        </div>
                        <div class="col-sm-3">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Làm mới</button>
                        </div>
                        
                    </div>
                </form>
            </div>
<!-- js -->
<script type="text/javascript" src="js/product_add.js"></script>

<?php
    if(isset($_POST['update_admin'])){

        $admin_name = $_POST['admin_name'];
        $admin_email = $_POST['admin_email'];
        $admin_user_name = $_POST['admin_user_name'];
        $admin_password = $_POST['admin_password'];
        $re_admin_password = $_POST['re_admin_password'];
        $admin_img = $_FILES['admin_img']['name'];
        $temp_name = $_FILES['admin_img']['tmp_name'];
        move_uploaded_file($temp_name, "admin_img/$admin_img");
        $admin_address = $_POST['admin_address'];
        $admin_contact = $_POST['admin_contact'];
        $admin_level = $_POST['admin_level'];
    
        $update_admin = "update admin set 
        admin_name='$admin_name', admin_email='$admin_email', admin_user_name='$admin_user_name', admin_password='$admin_password', admin_img='$admin_img', admin_address='$admin_address', admin_contact='$admin_contact', admin_level='$admin_level' 
        where admin_id='$admin_id'";
        if($_SESSION['admin_id']==$admin_id){
            $run_admin = mysqli_query($conn, $update_admin);
            if($run_admin){
                echo "<script>alert('Cập nhật Admin thành công')</script>";
                echo "<script>window.open('signin.php', '_self')</script>";
            }
        }
        else{
            $run_admin = mysqli_query($conn, $update_admin);
        
            if($run_admin){
                echo "<script>alert('Cập nhật Admin thành công')</script>";
                echo "<script>window.open('index.php?admin_list','_self')</script>";
            }
        }
}


?>