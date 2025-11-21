<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['product_edit'])){
        $product_id = $_GET['product_edit'];
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
        // $product_keyword = $row_product['product_keyword'];
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
                          <li class="breadcrumb-item active" aria-current="page">Sửa điện thoại</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                <div class="row mb-3">
                    <label >Thông tin cấu hình sản phẩm</label>
                    </div>
                    <div class="row mb-3">
                        <label for="product_name" class="col-sm-3 col-form-label text-md-end">Tên điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_name; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="cartegory" class="col-sm-3 col-form-label text-md-end">Thương hiệu</label>
                        <div class="col-sm-5">
                            <select class="form-select" id="cartegory" name="cartegory">
                                <option value="<?php echo $cartegory_id; ?>" selected><?php echo $cartegory_name; ?></option>
                                <?php
                                    $get_cartegory = "select * from cartegory";
                                    $run_cartegory = mysqli_query($conn, $get_cartegory);

                                    while($row_cartegory = mysqli_fetch_array($run_cartegory)){
                                        $cartegory_id = $row_cartegory['cartegory_id'];
                                        $cartegory_name = $row_cartegory['cartegory_name'];

                                        echo"
                                        
                                        <option value='$cartegory_id'>$cartegory_name</option>

                                        ";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label for="product_price" class="col-sm-3 col-form-label text-md-end">Giá</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo $product_price; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_price_des" class="col-sm-3 col-form-label text-md-end">Giá khuyến mãi</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price_des" name="product_price_des" value="<?php echo $product_price_des; ?>">
                        </div>
                    </div> -->

                    <div class="row mb-3">
                        <label for="product_des" class="col-sm-3 col-form-label text-md-end">Mô tả</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" id="product_des" name="product_des"><?php echo $product_des; ?></textarea>
                        </div>
                    </div>
                    <!-- <div class="row mb-3">
                        <label for="product_keyword" class="col-sm-3 col-form-label text-md-end">Đường dẫn</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_keyword" name="product_keyword" value="<?php echo $product_keyword; ?>">
                        </div>
                    </div> -->
                    <div class="row mb-3">
                        <label for="product_screen" class="col-sm-3 col-form-label text-md-end">Màn hình</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_screen" name="product_screen" value="<?php echo $product_screen; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_os" class="col-sm-3 col-form-label text-md-end">Hệ điều hành</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_os" name="product_os" value="<?php echo $product_os; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_rear_cam" class="col-sm-3 col-form-label text-md-end">Camera sau</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_rear_cam" name="product_rear_cam" value="<?php echo $product_rear_cam; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_front_cam" class="col-sm-3 col-form-label text-md-end">Camera trước</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_front_cam" name="product_front_cam" value="<?php echo $product_front_cam; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_chip" class="col-sm-3 col-form-label text-md-end">Chip</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_chip" name="product_chip" value="<?php echo $product_chip; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_ram" class="col-sm-3 col-form-label text-md-end">RAM</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_ram" name="product_ram" value="<?php echo $product_ram; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_internal_memory" class="col-sm-3 col-form-label text-md-end">Bộ nhớ trong</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_internal_memory" name="product_internal_memory" value="<?php echo $product_internal_memory; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_sim" class="col-sm-3 col-form-label text-md-end">Sim</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_sim" name="product_sim" value="<?php echo $product_sim; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_battery" class="col-sm-3 col-form-label text-md-end">Pin, Sạc</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_battery" name="product_battery" value="<?php echo $product_battery; ?>">
                        </div>
                    </div>
                    <div class="row mb-3 mt-3">
                        <div class="col-sm-4 text-md-end">
                            <button type="submit" name="update_product" class="btn btn-primary">Cập nhật</button>
                        </div>
                        <div class="col-sm-3">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Làm mới</button>
                        </div>
                        
                    </div>
                    <div class="row mb-3 mt-5">
                        <label for="">Thông tin về giá của sản phẩm</label>
                    </div>
                <table class="table table-striped table-hover">
                    <tr>
                        <th scope="col">Màu</th>
                        <th scope="col">Ảnh</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Giá giảm</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Trạng thái bán</th>
                        <th scope="col" colspan=2></th>
                    </tr>
                    <?php
                            $get_price = "select * from product_img where product_id = $product_id";
                            $run_get_price = mysqli_query($conn,$get_price);
                            while($row_get_price = mysqli_fetch_array($run_get_price)){
                                $product_color_img_id = $row_get_price['product_color_img_id'];
                                $product_color_id = $row_get_price['product_color_id'];
                                $get_product_color_name = "select * from product_color where product_color_id=$product_color_id";
                                $run_get_product_color_name = mysqli_query($conn,$get_product_color_name);
                                $row_get_product_color_name = mysqli_fetch_array($run_get_product_color_name);
                                $product_color_name = $row_get_product_color_name['product_color_name'];

                                $product_color_img = $row_get_price['product_color_img'];
                                $product_price = $row_get_price['product_price'];
                                $product_price_des = $row_get_price['product_price_des'];
                                $product_quantity = $row_get_price['product_quantity'];
                                $product_status = $row_get_price['product_status'];
                    ?>
                    <tr>
                        <form action="" method="post">
                            <td><?=$product_color_name?></td>
                            <td><img src="product_img/<?php echo $product_color_img ?>" width="100px" style="object-fit: contain;" alt=""></td>
                            <td><input type="number" name="product_price" value="<?=$product_price?>"></td>
                            <td><input type="number" name="product_price_des" value="<?=$product_price_des?>"></td>
                            <td><input type="number" name="product_quantity" value="<?=$product_quantity?>"></td>
                            <td>
                                <select class="form-select" name="product_status">
                                    <option value="Đang bán" <?php if($product_status == 'Đang bán') echo "selected";?>>Đang bán</option>
                                    <option value="Ngừng bán" <?php if($product_status == 'Ngừng bán') echo "selected";?>>Ngừng bán</option>
                                </select>
                            </td>
                            <td><input type="hidden" name="product_color_img_id" value="<?=$product_color_img_id?>"></td>
                            <td><button type="submit" name="update_product_price" class="btn btn-primary">Cập nhật</button>
                            </td>
                        </form>
                    </tr>
                    <?php
                        }
                    ?>
                    <?php
                        //update thông tin giá của sản phẩm
                        if(isset($_POST['update_product_price'])){
                            $product_color_img_id1= $_POST['product_color_img_id'];
                            $product_price1 = $_POST['product_price'];
                            $product_price_des1 = $_POST['product_price_des'];
                            $product_quantity1 = $_POST['product_quantity'];
                            $product_status1 = $_POST['product_status'];
                            if($product_quantity1 < 0 || $product_price1 < 0 || $product_price_des1 < 0){
                                echo "<script>alert('Nhập sai dữ liệu ! Số lượng sản phẩm hoặc giá sản phẩm phải >= 0')</script>";
                            }
                            else if($product_price1 < $product_price_des1){
                                echo "<script>alert('Giá giảm phải nhỏ hơn giá ban đầu của sản phẩm ! Nhập lại !')</script>";
                            }
                            else{
                        
                                $update_price = "update product_img set product_price='$product_price1',
                                product_price_des='$product_price_des1',product_quantity='$product_quantity1', product_status='$product_status1' where product_color_img_id='$product_color_img_id1'";
                                $run_product_price = mysqli_query($conn,$update_price);
                                if($run_product_price){
                                    echo "<script>alert('Cập nhật thành công')</script>";
                                    echo "<script>window.open('index.php?product_edit=$product_id','_self')</script>";
                                }

                            }
                        }
                    ?>
                </table>
                <!-- <div class="row mb-5 mt-5">
                        <div class="col-sm-6 text-md-end">
                            <button type="submit" name="update_product" class="btn btn-primary">Cập nhật</button>
                        </div>
                        <div class="col-sm-6">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Làm mới</button>
                        </div>
                </div> -->
                </form>
            </div>

            <?php

if(isset($_POST['update_product'])){
    //update thông tin cấu hình

    $product_name = $_POST['product_name'];
    $cartegory = $_POST['cartegory'];
    // $product_price = $_POST['product_price'];
    // $product_price_des = $_POST['product_price_des'];
    $product_des = $_POST['product_des'];
    // $product_keyword = $_POST['product_keyword'];
    $product_screen = $_POST['product_screen'];
    $product_os = $_POST['product_os'];
    $product_rear_cam = $_POST['product_rear_cam'];
    $product_front_cam = $_POST['product_front_cam'];
    $product_chip = $_POST['product_chip'];
    $product_ram = $_POST['product_ram'];
    $product_internal_memory = $_POST['product_internal_memory'];
    $product_sim = $_POST['product_sim'];
    $product_battery = $_POST['product_battery'];

    $update_product = "update products set 
    cartegory_id='$cartegory', product_name='$product_name', date=NOW(), product_des='$product_des',
    product_screen='$product_screen', product_os='$product_os', product_rear_cam='$product_rear_cam', product_front_cam='$product_front_cam', product_chip='$product_chip', product_ram='$product_ram', product_internal_memory='$product_internal_memory', product_sim='$product_sim', product_battery='$product_battery'
    where product_id = '$product_id'";
    $run_product = mysqli_query($conn, $update_product);

    if($run_product){
        echo "<script>alert('Cập nhật thông tin sản phẩm thành công')</script>";
        echo "<script>window.open('index.php?product_edit=$product_id','_self')</script>";
    }
}

?>