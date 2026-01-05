<?php
require_once '../includes/auth.php';
checkAdminLogin();
// Chỉ admin (role_level >= 4) trở lên mới được thêm admin
checkPermission('admin');
?>

            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?admin_list">Admin</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thêm Admin</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="admin_name" class="col-sm-3 col-form-label text-md-end">Tên Admin</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_name" name="admin_name">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_email" class="col-sm-3 col-form-label text-md-end">Email</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_email" name="admin_email">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_user_name" class="col-sm-3 col-form-label text-md-end">Tên tài khoản (tự sinh)</label>
                        <div class="col-sm-5">
                            <input type="text" readonly class="form-control" id="admin_user_name" name="admin_user_name" placeholder="Chọn cấp để sinh tên tài khoản">
                            <div class="form-text">Hệ thống sẽ tự tạo `nvbanhang` / `nvkho` (thêm số nếu cần).</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="admin_password" class="col-sm-3 col-form-label text-md-end">Mật khẩu</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="admin_password" name="admin_password">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="re_admin_password" class="col-sm-3 col-form-label text-md-end">Nhập lại mật khẩu</label>
                        <div class="col-sm-5">
                            <input type="password" class="form-control" id="re_admin_password" name="re_admin_password">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="admin_img" class="col-sm-3 col-form-label text-md-end">Ảnh đại diện</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" id="admin_img" name="admin_img" accept="images/*" onchange="preview_image(event)">
                                <div class="row">
                                <div class="col-sm-6">
                                    <img id="displayImg" style="width: 150px; height: 150px; display: none; object-fit: contain;">
                                </div>
                                </div>
                            </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_address" class="col-sm-3 col-form-label text-md-end">Địa chỉ</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_address" name="admin_address">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="admin_contact" class="col-sm-3 col-form-label text-md-end">Điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="admin_contact" name="admin_contact">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="admin_level" class="col-sm-3 col-form-label text-md-end">Cấp độ</label>
                            <div class="col-sm-5">
                                <select class="form-select" id="admin_level" name="admin_level">
                                    <option disable selected>Chọn cấp</option>
                                    <option value="sales">Nhân viên bán hàng</option>
                                    <option value="warehouse">Nhân viên kho</option>
                                </select>
                                <script>
                                    (function(){
                                        function fetchUsername(role){
                                            if(!role) return;
                                            var xhr = new XMLHttpRequest();
                                            xhr.open('GET', 'generate_username.php?role=' + encodeURIComponent(role));
                                            xhr.onreadystatechange = function(){
                                                if(xhr.readyState === 4){
                                                    try{
                                                        var r = JSON.parse(xhr.responseText);
                                                        if(r && r.ok && r.username){
                                                            document.getElementById('admin_user_name').value = r.username;
                                                            document.querySelector('button[name="add_admin"]').disabled = false;
                                                        } else {
                                                            document.getElementById('admin_user_name').value = '';
                                                            document.querySelector('button[name="add_admin"]').disabled = true;
                                                        }
                                                    }catch(e){
                                                        document.getElementById('admin_user_name').value = '';
                                                        document.querySelector('button[name="add_admin"]').disabled = true;
                                                    }
                                                }
                                            };
                                            xhr.send();
                                        }
                                        var sel = document.getElementById('admin_level');
                                        var btn = document.querySelector('button[name="add_admin"]');
                                        if(btn) btn.disabled = true;
                                        sel.addEventListener('change', function(e){
                                            var v = sel.value;
                                            if(v === 'sales' || v === 'warehouse'){
                                                fetchUsername(v);
                                            } else {
                                                document.getElementById('admin_user_name').value = '';
                                                if(btn) btn.disabled = true;
                                            }
                                        });
                                    })();
                                </script>
                            </div>
                        </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-md-end">
                            <button type="submit" name="add_admin" class="btn btn-primary">Thêm mới</button>
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
    if(isset($_POST['add_admin'])){

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
        $admin_level_input = $_POST['admin_level'];

        // Map selection to stored admin_level text and role_id, and choose username prefix
        if ($admin_level_input === 'sales') {
            $admin_level = 'Nhân viên bán hàng';
            $role_id = 3;
            $prefix = 'nvbanhang';
        } else {
            $admin_level = 'Nhân viên kho';
            $role_id = 2;
            $prefix = 'nvkho';
        }

        // Prefer the username filled by the frontend (readonly), but if missing generate server-side
        $admin_user_name = trim($_POST['admin_user_name'] ?? '');
        if ($admin_user_name === '') {
            // Auto-generate unique username with prefix (nvbanhang, nvbanhang1, ...)
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
                $admin_user_name = $prefix;
            } else {
                $admin_user_name = $prefix . $new_index;
            }
        }

        $insert_admin = "insert into admin
        (admin_name, admin_email, admin_user_name, admin_password, admin_img, admin_address, admin_contact, admin_level, role_id) 
        values ('" . mysqli_real_escape_string($conn, $admin_name) . "', '" . mysqli_real_escape_string($conn, $admin_email) . "', '" . mysqli_real_escape_string($conn, $admin_user_name) . "', '" . mysqli_real_escape_string($conn, $admin_password) . "', '" . mysqli_real_escape_string($conn, $admin_img) . "', '" . mysqli_real_escape_string($conn, $admin_address) . "', '" . mysqli_real_escape_string($conn, $admin_contact) . "', '" . mysqli_real_escape_string($conn, $admin_level) . "', " . (int)$role_id . ")";
        $run_admin = mysqli_query($conn, $insert_admin);
        
        if($run_admin){
            echo "<script>alert('Thêm Admin thành công')</script>";
            echo "<script>window.open('index.php?admin_list','_self')</script>";
        }
}


?>