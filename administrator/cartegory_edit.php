<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['cartegory_edit'])){
        $cartegory_id = $_GET['cartegory_edit'];
        $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
        $run_cartegory = mysqli_query($conn, $get_cartegory);
        $row_cartegory = mysqli_fetch_array($run_cartegory);
        $cartegory_name = $row_cartegory['cartegory_name'];
        $cartegory_img = $row_cartegory['cartegory_img'];
    }
}
?>

            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Sửa thương hiệu</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="cartegory_name" class="col-sm-3 col-form-label text-md-end">Tên thương hiệu</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="cartegory_name" name="cartegory_name" value="<?php echo $cartegory_name; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="cartegory_img" class="col-sm-3 col-form-label text-md-end">Logo</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" id="cartegory_img" name="cartegory_img" accept="images/*" onchange="preview_image(event)">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img id="" src="cartegory_img/<?php echo $cartegory_img; ?>" style="width: 150px; height: 150px; object-fit: contain; <?php if($cartegory_img==''){echo "display: none;";} ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <img id="displayImg" style="width: 150px; height: 150px; display: none; object-fit: contain;">
                                    </div>
                                </div> 
                            </div>
                        </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 text-md-end">
                            <button type="submit" name="update_cartegory" class="btn btn-primary">Cập nhật</button>
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

if(isset($_POST['update_cartegory'])){

    $cartegory_name = $_POST['cartegory_name'];
    $cartegory_img = $_FILES['cartegory_img']['name'];
    $temp_name = $_FILES['cartegory_img']['tmp_name'];

    move_uploaded_file($temp_name, "cartegory_img/$cartegory_img");

    $update_cartegory = "update cartegory set 
    cartegory_name='$cartegory_name', cartegory_img='$cartegory_img'
    where cartegory_id='$cartegory_id'";
    $run_cartegory = mysqli_query($conn, $update_cartegory);
    
    if($run_cartegory){
        echo "<script>alert('Cập nhật thương hiệu thành công')</script>";
        echo "<script>window.open('index.php?cartegory_list','_self')</script>";
    }
}

?>