
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?product_list">Điện thoại</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thêm mới điện thoại</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="product_name" class="col-sm-3 col-form-label text-md-end">Tên điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_name" name="product_name">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="cartegory" class="col-sm-3 col-form-label text-md-end">Thương hiệu</label>
                        <div class="col-sm-5">
                            <select class="form-select" id="cartegory" name="cartegory">
                                <option selected>Chọn thương hiệu</option>
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

                    <div class="row mb-3">
                        <label for="product_des" class="col-sm-3 col-form-label text-md-end">Mô tả</label>
                        <div class="col-sm-7">
                            <textarea class="form-control" id="product_des" name="product_des"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_screen" class="col-sm-3 col-form-label text-md-end">Màn hình</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_screen" name="product_screen">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_os" class="col-sm-3 col-form-label text-md-end">Hệ điều hành</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_os" name="product_os">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_rear_cam" class="col-sm-3 col-form-label text-md-end">Camera sau</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_rear_cam" name="product_rear_cam">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_front_cam" class="col-sm-3 col-form-label text-md-end">Camera trước</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_front_cam" name="product_front_cam">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_chip" class="col-sm-3 col-form-label text-md-end">Chip</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_chip" name="product_chip">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_ram" class="col-sm-3 col-form-label text-md-end">RAM</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_ram" name="product_ram">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_internal_memory" class="col-sm-3 col-form-label text-md-end">Bộ nhớ trong</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_internal_memory" name="product_internal_memory">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_sim" class="col-sm-3 col-form-label text-md-end">Sim</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_sim" name="product_sim">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_battery" class="col-sm-3 col-form-label text-md-end">Pin, Sạc</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_battery" name="product_battery">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-md-end">
                            <button type="submit" name="submit" class="btn btn-primary">Thêm mới</button>
                        </div>
                        <div class="col-sm-3">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Làm mới</button>
                        </div>
                        
                    </div>
                </form>
            </div>


<?php

if(isset($_POST['submit'])){

    $product_name = $_POST['product_name'];
    $cartegory = $_POST['cartegory'];
    $product_des = $_POST['product_des'];
    $product_screen = $_POST['product_screen'];
    $product_os = $_POST['product_os'];
    $product_rear_cam = $_POST['product_rear_cam'];
    $product_front_cam = $_POST['product_front_cam'];
    $product_chip = $_POST['product_chip'];
    $product_ram = $_POST['product_ram'];
    $product_internal_memory = $_POST['product_internal_memory'];
    $product_sim = $_POST['product_sim'];
    $product_battery = $_POST['product_battery'];

    $insert_product = "insert into products  
    (cartegory_id, product_name, date, product_des, product_screen, product_os, product_rear_cam, product_front_cam, product_chip, product_ram, product_internal_memory, product_sim, product_battery) 
    values ('$cartegory', '$product_name', NOW(), '$product_des', '$product_screen', '$product_os', '$product_rear_cam', '$product_front_cam', '$product_chip', '$product_ram', '$product_internal_memory', '$product_sim', '$product_battery')";
    $run_product = mysqli_query($conn, $insert_product);
    
    if($run_product){
        echo "<script>alert('Thêm sản phẩm thành công')</script>";
        echo "<script>window.open('index.php?product_add_img&product_name=$product_name','_self')</script>";
    }
}

?>