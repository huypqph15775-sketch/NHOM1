
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?product_list">Điện thoại</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách điện thoại</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên SP</th>
                    <th scope="col">Thương hiệu</th>
                    <th scope="col" class="text-center">Giá sản phẩm</th>
                    <th scope="col" class="text-center">Thông tin cấu hình</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center"></th>
                </tr>
                <?php
                    $get_product = "select * from products ORDER BY product_id DESC";
                    $run_product = mysqli_query($conn, $get_product);
                    while($row_product = mysqli_fetch_array($run_product)){
                        $product_id = $row_product['product_id'];
                        $cartegory_id = $row_product['cartegory_id'];
                        $product_name = $row_product['product_name'];
                        // $product_price = currency_format($row_product['product_price']);
                        // $product_price_des = currency_format($row_product['product_price_des']);
                        $product_des = $row_product['product_des'];
                        $get_img = "select * from product_img where product_id = '$product_id' LIMIT 0,1";
                        $run_img = mysqli_query($conn, $get_img);
                        $row_img = mysqli_fetch_array($run_img);
                        $product_img = $row_img['product_color_img'];
                        $get_cartegory = "select * from cartegory where cartegory_id = '$cartegory_id'";
                        $run_cartegory = mysqli_query($conn, $get_cartegory);
                        $row_cartegory = mysqli_fetch_array($run_cartegory);
                        $cartegory_name = $row_cartegory['cartegory_name'];
                    
                ?>
                <tr>
                    <th scope="row"><?php echo $product_id; ?></th>
                    <td><?php echo $product_name ?></td>
                    <td><?php echo $cartegory_name ?></td>
                    <td class="text-center text-success"><a href="index.php?product_price=<?php echo $product_id; ?>" class="text-success"><i class="fas fa-comment-dollar"></i></td>
                    <td class="text-center text-success"><a href="index.php?product_info=<?php echo $product_id; ?>" class="text-success"><i class="fas fa-eye"></i></td>
                    <td class="text-center text-primary"><a href="index.php?product_edit=<?php echo $product_id; ?>" class="text-primary"><i class="fas fa-edit"></i></td>
                    <td class="text-center"><button onclick="del_Product(<?php echo $product_id; ?>)" class="btn btn-danger text-white">
                        <?php
                            $sql = "select * from product_img where product_id = $product_id and product_status='Đang bán'";
                            $run_sql = mysqli_query($conn,$sql);
                            $count_sql = mysqli_num_rows($run_sql);
                            if($count_sql == 0)
                                echo "Bán lại";
                            else
                                echo "Ngừng bán";
                        ?>
                    </button></td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function del_Product(id){
                var result = confirm("Bạn có chắc chắn thực hiện chức năng này? ");
                if(result==true){
                    document.location = 'index.php?product_delete='+id;
                }
            }
            </script>
                </table>
            </div>




