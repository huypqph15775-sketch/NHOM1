<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/auth.php';

checkAdminLogin();
checkPermission('Quản lý'); // Chỉ Quản lý mới được truy cập
?>
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách Admin</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ảnh</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">SĐT</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center">Xóa</th>
                </tr>
                <?php
                    $get_admin = "select * from admin ORDER BY admin_id DESC";
                    $run_admin = mysqli_query($conn, $get_admin);
                    $i=0;
                    while($row_admin = mysqli_fetch_array($run_admin)){
                        $admin_id = $row_admin['admin_id'];
                        $admin_name = $row_admin['admin_name'];
                        $admin_img = $row_admin['admin_img'];
                        $admin_email = $row_admin['admin_email'];
                        $admin_address = $row_admin['admin_address'];
                        $admin_contact = $row_admin['admin_contact'];
                        $i++;
                    
                ?>
                <tr>
                    <th scope="row"><?php echo $i; ?></th>
                    <td><?php echo $admin_name ?></td>
                    <td><?php echo $admin_email ?></td>
                    <td><img src="admin_img/<?php echo $admin_img ?>" width="80px" alt=""></td>
                    <td><?php echo $admin_address ?></td>
                    <td><?php echo $admin_contact ?></td>
                    <td class="text-center text-primary"><a href="index.php?admin_edit=<?php echo $admin_id; ?>" class="text-primary"><i class="fas fa-edit"></i></td>
                    <td class="text-center"><button style="border: none; background-color: transparent;" onclick="del_admin(<?php echo $admin_id; ?>)" class="text-danger"><i class="fas fa-trash-alt"></i></button></td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function del_admin(id){
                var result = confirm("Bạn chắc chắn muốn xóa admin này? ");
                if(result==true){
                    document.location = 'index.php?admin_delete='+id;
                }
            }
            </script>
                </table>
            </div>




