
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thêm thương hiệu</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <form class="mt-4" action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="cartegory_name" class="col-sm-3 col-form-label text-md-end">Tên thương hiệu</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="cartegory_name" name="cartegory_name">
                        </div>
                    </div>
                    <div class="row mb-3">
                            <label for="cartegory_img" class="col-sm-3 col-form-label text-md-end">Logo</label>
                            <div class="col-sm-5">
                                <input type="file" class="form-control" id="cartegory_img" name="cartegory_img" accept="images/*" onchange="preview_image(event)">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img id="displayImg" style="width: 150px; height: 150px; display: none; object-fit: contain;">
                                    </div>
                                </div> 
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

    $cartegory_name = $_POST['cartegory_name'];
    $cartegory_img = $_FILES['cartegory_img']['name'];
    $temp_name = $_FILES['cartegory_img']['tmp_name'];

    move_uploaded_file($temp_name, "cartegory_img/$cartegory_img");

    $insert_cartegory = "insert into cartegory  
    (cartegory_name, cartegory_img) 
    values ('$cartegory_name', '$cartegory_img')";
    $run_cartegory = mysqli_query($conn, $insert_cartegory);
    
    if($run_cartegory){
        echo "<script>alert('Thêm thương hiệu thành công')</script>";
        echo "<script>window.open('index.php?cartegory_list','_self')</script>";
    }
}

?>