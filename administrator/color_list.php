
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách màu</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Màu</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center">Xóa</th>
                </tr>
                <?php
                    $get_product_color = "select * from product_color ORDER BY product_color_id DESC";
                    $run_product_color = mysqli_query($conn, $get_product_color);
                    while($row_product_color = mysqli_fetch_array($run_product_color)){
                        $product_color_id = $row_product_color['product_color_id'];
                        $product_color_name = $row_product_color['product_color_name'];
                ?>
                <tr>
                    <th scope="row"><?php echo $product_color_id; ?></th>
                    <td><?php echo $product_color_name ?></td>
                    <td class="text-center text-primary"><a href="index.php?color_edit=<?php echo $product_color_id; ?>" class="text-primary"><i class="fas fa-edit"></i></td>
                    <td class="text-center"><button style="border: none; background-color: transparent;" onclick="del_product_color(<?php echo $product_color_id; ?>)" class="text-danger"><i class="fas fa-trash-alt"></i></button></td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function del_product_color(id){
                var result = confirm("Bạn chắc chắn muốn xóa màu này? ");
                if(result==true){
                    document.location = 'index.php?color_delete='+id;
                }
            }
            </script>
                </table>
            </div>




