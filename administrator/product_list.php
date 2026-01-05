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
                <form method="post" id="bulkForm" action="index.php?product_list">
                <div class="mb-3 d-flex gap-2 align-items-center">
                    <label class="mb-0">Thay đổi trạng thái tất cả sản phẩm:</label>
                    <select name="bulk_status" class="form-select" style="width:160px">
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="Ngừng bán">Ngừng bán</option>
                        <option value="Đang bán">Đang bán</option>
                    </select>
                    <button type="submit" name="bulk_update" class="btn btn-primary">Áp dụng</button>
                </div>
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên SP</th>
                    <th scope="col">Thương hiệu</th>
                    <th scope="col" class="text-center">Giá sản phẩm</th>
                    <th scope="col" class="text-center">Thông tin cấu hình</th>
                    <th scope="col" class="text-center">Cảnh báo tồn kho</th>
                    <th scope="col" class="text-center">Trạng thái</th>
                    <th scope="col" class="text-center">Sửa</th>
                    <th scope="col" class="text-center"></th>
                </tr>
                <?php
                    // handle bulk update cho tất cả sản phẩm
                    if(isset($_POST['bulk_update'])){
                        if(!empty($_POST['bulk_status'])){
                            $status = mysqli_real_escape_string($conn, $_POST['bulk_status']);
                            $update_status_q = "UPDATE product_img SET product_status='$status'";
                            mysqli_query($conn, $update_status_q);
                            echo "<script>alert('Cập nhật thành công');window.location.href='index.php?product_list';</script>";
                            exit;
                        }
                    }

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
                    <?php
                        $qty_sql = "SELECT MIN(product_quantity) AS min_q, SUM(product_quantity) AS total_q FROM product_img WHERE product_id = '$product_id'";
                        $run_qty = mysqli_query($conn, $qty_sql);
                        $row_qty = mysqli_fetch_array($run_qty);
                        $min_q = isset($row_qty['min_q']) ? intval($row_qty['min_q']) : 0;
                        $total_q = isset($row_qty['total_q']) ? intval($row_qty['total_q']) : 0;
                    ?>
                    <td class="text-center">
                        <?php if($min_q > 0 && $min_q < 20){ ?>
                            <span class="badge bg-warning text-dark">Sắp hết hàng (nhỏ nhất: <?php echo $min_q; ?>)</span>
                        <?php } else if($min_q == 0){ ?>
                            <span class="badge bg-danger">Hết hàng</span>
                        <?php } else { ?>
                            <span class="badge bg-success">Tồn kho: <?php echo $total_q; ?></span>
                        <?php } ?>
                    </td>
                    <td class="text-center">
                        <?php
                            // Lấy trạng thái hiện tại của sản phẩm (nếu có nhiều biến thể, ưu tiên trạng thái "Ngừng bán" nếu có ít nhất 1 biến thể ngừng bán)
                            $status_sql = "SELECT product_status FROM product_img WHERE product_id = '$product_id' GROUP BY product_status";
                            $run_status = mysqli_query($conn, $status_sql);
                            $statuses = [];
                            while($row_status = mysqli_fetch_array($run_status)){
                                $statuses[] = $row_status['product_status'];
                            }
                            if(in_array('Ngừng bán', $statuses)){
                                echo '<span class="badge bg-danger">Ngừng bán</span>';
                            } else {
                                echo '<span class="badge bg-success">Đang bán</span>';
                            }
                        ?>
                    </td>
                    <td class="text-center text-primary">
                        <a href="index.php?product_edit=<?php echo $product_id; ?>" class="text-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <br>
                        <small class="text-muted">
                            <?php
                                $variant_count = mysqli_num_rows(
                                    mysqli_query($conn, "SELECT * FROM product_img WHERE product_id = '$product_id'")
                                );
                                echo "($variant_count biến thể)";
                            ?>
                        </small>
                    </td>
                   
                <?php
                    }
                ?>

            
                </table>
            </div>




