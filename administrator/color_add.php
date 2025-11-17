
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
                            <input type="text" class="form-control" id="product_color_name" name="product_color_name">
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

    $product_color_name = $_POST['product_color_name'];

    $insert_color = "insert into product_color  
    (product_color_name) 
    values ('$product_color_name')";
    $run_color = mysqli_query($conn, $insert_color);
    
    if($run_color){
        echo "<script>alert('Thêm màu thành công')</script>";
        echo "<script>window.open('index.php?color_list','_self')</script>";
    }
}

?>