<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['color_edit'])){
        $product_color_id = $_GET['color_edit'];
        $get_product_color = "select * from product_color where product_color_id='$product_color_id'";
        $run_product_color = mysqli_query($conn, $get_product_color);
        $row_product_color = mysqli_fetch_array($run_product_color);
        $product_color_name = $row_product_color['product_color_name'];
    }
}
?>

            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thêm màu sắc</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="product_color_name" class="col-sm-3 col-form-label text-md-end">Tên màu</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="product_color_name" name="product_color_name" value="<?php echo $product_color_name ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-md-end">
                            <button type="submit" name="update_color" class="btn btn-primary">Cập nhật</button>
                        </div>
                        <div class="col-sm-3">
                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">Làm mới</button>
                        </div>
                        
                    </div>
                </form>
            </div>


<?php

if(isset($_POST['update_color'])){

    $product_color_name = $_POST['product_color_name'];

    $update_color = "update product_color set
    product_color_name = '$product_color_name'
    where product_color_id = '$product_color_id'";
    $run_color = mysqli_query($conn, $update_color);
    
    if($run_color){
        echo "<script>alert('Cập nhật màu thành công')</script>";
        echo "<script>window.open('index.php?color_list','_self')</script>";
    }
}

?>