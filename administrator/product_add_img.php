
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?product_list">Điện thoại</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thêm ảnh điện thoại</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="search" class="col-sm-3 col-form-label text-md-end">Tên điện thoại</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="search" name="search" value="<?php if(isset($_GET['product_name'])){echo $_GET['product_name'];}?>">
                            <div id="productList"></div> 
                        </div>
                    </div>
                    <div class="addColorImg">
                        <div class="row mb-3">
                            <label for="product_color" class="col-sm-3 col-form-label text-md-end">Màu sắc</label>
                            <div class="col-sm-5">
                                <select class="form-select" id="product_color" name="product_color">
                                    <option selected>Chọn màu</option>
                                    <?php
                                        $get_product_color = "select * from product_color";
                                        $run_product_color = mysqli_query($conn, $get_product_color);

                                        while($row_product_color = mysqli_fetch_array($run_product_color)){
                                            $product_color_id = $row_product_color['product_color_id'];
                                            $product_color_name = $row_product_color['product_color_name'];

                                            echo"
                                            
                                            <option value='$product_color_id'>$product_color_name</option>

                                            ";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="product_color_img" class="col-sm-3 col-form-label text-md-end">Ảnh theo màu</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" id="product_color_img" name="product_color_img" accept="images/*" onchange="preview_image(event)">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img id="displayImg" style="width: 150px; height: 150px; display: none; object-fit: contain;">
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_price" class="col-sm-3 col-form-label text-md-end">Giá</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price" name="product_price">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_price_des" class="col-sm-3 col-form-label text-md-end">Giá khuyến mãi</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_price_des" name="product_price_des">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_quantity" class="col-sm-3 col-form-label text-md-end">Số lượng</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control" id="product_quantity" name="product_quantity">
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


<!-- js -->
<script type="text/javascript" src="js/product_add.js"></script>


<?php

if(isset($_POST['submit'])){

    $product_name = $_POST['search'];
    $product_color_id = $_POST['product_color'];
    $product_color_img = $_FILES['product_color_img']['name'];
    $temp_name = $_FILES['product_color_img']['tmp_name'];
    move_uploaded_file($temp_name, "product_img/$product_color_img");

    $product_price = $_POST['product_price'];
    $product_price_des = $_POST['product_price_des'];
    $product_quantity = $_POST['product_quantity'];

    $get_product_id = "select * from products where product_name = '$product_name'";
    $run_product_id = mysqli_query($conn, $get_product_id);
    while($row_product_id=mysqli_fetch_array($run_product_id)){
     $product_id = $row_product_id["product_id"];
     $insert_product_img = "insert into product_img
     (product_id, product_color_id, product_color_img, product_price, product_price_des, product_quantity)
     values('$product_id', '$product_color_id', '$product_color_img', '$product_price', '$product_price_des', '$product_quantity')";
 $run_product_img = mysqli_query($conn, $insert_product_img);

 if($run_product_img){
     echo "<script>alert('Thêm sản phẩm theo màu thành công')</script>";;
     echo "<script>window.open('index.php?product_add_img&product_name=$product_name'','_self')</script>";
 }
    }
    
}

?>