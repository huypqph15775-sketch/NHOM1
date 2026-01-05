<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['product_info'])){
        $product_id = $_GET['product_info'];
        $get_product = "select * from products where product_id='$product_id'";
        $run_product = mysqli_query($conn, $get_product);
        $row_product = mysqli_fetch_array($run_product);
        $product_name = $row_product['product_name'];
        $cartegory_id = $row_product['cartegory_id'];
        // $product_price = $row_product['product_price'];
        // $product_price_des = $row_product['product_price_des'];
        // $product_price_format = currency_format($row_product['product_price']);
        // $product_price_des_format = currency_format($row_product['product_price_des']);
        $product_des = $row_product['product_des'];
        $product_screen = $row_product['product_screen'];
        $product_os = $row_product['product_os'];
        $product_rear_cam = $row_product['product_rear_cam'];
        $product_front_cam = $row_product['product_front_cam'];
        $product_chip = $row_product['product_chip'];
        $product_ram = $row_product['product_ram'];
        $product_internal_memory = $row_product['product_internal_memory'];
        $product_sim = $row_product['product_sim'];
        $product_battery = $row_product['product_battery'];

        $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
        $run_cartegory = mysqli_query($conn, $get_cartegory);
        $row_cartegory = mysqli_fetch_array($run_cartegory);
        $cartegory_name = $row_cartegory['cartegory_name'];
    }
}
?>


            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?product_list">Điện thoại</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thông tin cấu hình</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                            <label for="product_name" class="col-sm-3 col-form-label text-md-end">Id điện thoại</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" value="<?php echo $product_id; ?>" readonly>
                            </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_name" class="col-sm-3 col-form-label text-md-end">Tên điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_name; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="cartegory" class="col-sm-3 col-form-label text-md-end">Thương hiệu</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="cartegory_id" name="cartegory_id" value="<?php echo $cartegory_name; ?>" readonly>
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label for="product_price" class="col-sm-3 col-form-label text-md-end">Giá</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo $product_price; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_price_des" class="col-sm-3 col-form-label text-md-end">Giá khuyến mãi</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price_des" name="product_price_des" value="<?php echo $product_price_des; ?>" readonly>
                        </div>
                    </div> -->

                    <div class="row mb-3">
                        <label for="product_des" class="col-sm-3 col-form-label text-md-end">Mô tả</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" id="product_des" name="product_des" readonly><?php echo $product_des; ?></textarea>
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label for="product_keyword" class="col-sm-3 col-form-label text-md-end">Đường dẫn</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_keyword" name="product_keyword" value="<?php echo $product_keyword; ?>" readonly>
                        </div>
                    </div> -->
                    <div class="row mb-3">
                        <label for="product_screen" class="col-sm-3 col-form-label text-md-end">Màn hình</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_screen" name="product_screen" value="<?php echo $product_screen; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_os" class="col-sm-3 col-form-label text-md-end">Hệ điều hành</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_os" name="product_os" value="<?php echo $product_os; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_rear_cam" class="col-sm-3 col-form-label text-md-end">Camera sau</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_rear_cam" name="product_rear_cam" value="<?php echo $product_rear_cam; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_front_cam" class="col-sm-3 col-form-label text-md-end">Camera trước</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_front_cam" name="product_front_cam" value="<?php echo $product_front_cam; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_chip" class="col-sm-3 col-form-label text-md-end">Chip</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_chip" name="product_chip" value="<?php echo $product_chip; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_ram" class="col-sm-3 col-form-label text-md-end">RAM</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_ram" name="product_ram" value="<?php echo $product_ram; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_internal_memory" class="col-sm-3 col-form-label text-md-end">Bộ nhớ trong</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_internal_memory" name="product_internal_memory" value="<?php echo $product_internal_memory; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_sim" class="col-sm-3 col-form-label text-md-end">Sim</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_sim" name="product_sim" value="<?php echo $product_sim; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_battery" class="col-sm-3 col-form-label text-md-end">Pin, Sạc</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_battery" name="product_battery" value="<?php echo $product_battery; ?>" readonly>
                        </div>
                    </div>
                    </div>
                </form>
                <!-- <table class="table table-striped table-hover">
                    <tr>
                        <th scope="col">Màu</th>
                        <th scope="col">Ảnh</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Giá giảm</th>
                        <th scope="col">Số lượng</th>
                    </tr>
                    <?php
                        // if(isset($_GET['product_info'])){
                        //     $product_id1 = $_GET['product_info'];
                            $get_price = "select * from product_img where product_id = $product_id";
                            $run_get_price = mysqli_query($conn,$get_price);
                            while($row_get_price = mysqli_fetch_array($run_get_price)){
                                $product_color_id = $row_get_price['product_color_id'];
                                $get_product_color_name = "select * from product_color where product_color_id=$product_color_id";
                                $run_get_product_color_name = mysqli_query($conn,$get_product_color_name);
                                $row_get_product_color_name = mysqli_fetch_array($run_get_product_color_name);
                                $product_color_name = $row_get_product_color_name['product_color_name'];

                                $product_color_img = $row_get_price['product_color_img'];
                                $product_price = $row_get_price['product_price'];
                                $product_price_des = $row_get_price['product_price_des'];
                                $product_quantity = $row_get_price['product_quantity'];
                    ?>
                    <tr>
                        <td><?=$product_color_name?></td>
                        <td><img src="product_img/<?php echo $product_color_img ?>" width="100px" style="object-fit: contain;" alt=""></td>
                        <td><?=$product_price?></td>
                        <td><?=$product_price_des?></td>
                        <td><?=$product_quantity?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table> -->
            </div>