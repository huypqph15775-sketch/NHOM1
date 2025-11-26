
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách thương hiệu</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Thương hiệu</th>
                    <th scope="col">Logo</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center">Xóa</th>
                </tr>
                <?php
                    $get_cartegory = "select * from cartegory ORDER BY cartegory_id DESC";
                    $run_cartegory = mysqli_query($conn, $get_cartegory);
                    while($row_cartegory = mysqli_fetch_array($run_cartegory)){
                        $cartegory_id = $row_cartegory['cartegory_id'];
                        $cartegory_name = $row_cartegory['cartegory_name'];
                        $cartegory_img = $row_cartegory['cartegory_img'];
                    
                ?>
                <tr>
                    <th scope="row"><?php echo $cartegory_id; ?></th>
                    <td><?php echo $cartegory_name ?></td>
                    <td><img src="cartegory_img/<?php echo $cartegory_img ?>" width="80px" alt=""></td>
                    <td class="text-center text-primary"><a href="index.php?cartegory_edit=<?php echo $cartegory_id; ?>" class="text-primary"><i class="fas fa-edit"></i></td>
                    <td class="text-center"><button style="border: none; background-color: transparent;" onclick="del_cartegory(<?php echo $cartegory_id; ?>)" class="text-danger"><i class="fas fa-trash-alt"></i></button></td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function del_cartegory(id){
                var result = confirm("Khi xóa thương hiệu, các sản phẩm của thương hiệu sẽ bị xóa. Bạn chắc chắn muốn xóa? ");
                if(result==true){
                    document.location = 'index.php?cartegory_delete='+id;
                }
            }
            </script>
                </table>
            </div>




