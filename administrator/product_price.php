<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['product_price'])){
        $product_id = $_GET['product_price'];
    }
}
?>
<div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?product_list">Điện thoại</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Giá sản phẩm</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                    <tr>
                        <th scope="col">Màu</th>
                        <th scope="col">Ảnh</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Giá giảm</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Trạng thái bán</th>
                    </tr>
                    <?php
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
                                $product_status = $row_get_price['product_status'];
                    ?>
                    <tr>
                        <td><?=$product_color_name?></td>
                        <td><img src="product_img/<?php echo $product_color_img ?>" width="100px" style="object-fit: contain;" alt=""></td>
                        <td><?=$product_price?></td>
                        <td><?=$product_price_des?></td>
                        <td><?=$product_quantity?></td>
                        <td><?=$product_status?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table>
                    </div>