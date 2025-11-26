<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['product_delete'])){
            $product_id = $_GET['product_delete'];
            $sql = "select * from product_img where product_id = $product_id and product_status='Đang bán'";
            $run_sql = mysqli_query($conn,$sql);
            $count_sql = mysqli_num_rows($run_sql);
            if($count_sql == 0){
                $update_product = "update product_img set product_status = 'Đang bán' where product_id = '$product_id'";
                $run_update_product = mysqli_query($conn, $update_product);
            }
            else{
                $update_product = "update product_img set product_status = 'Ngừng bán' where product_id = '$product_id'";
                $run_update_product = mysqli_query($conn, $update_product);
            }
            if($run_update_product){
                echo "<script>window.open('index.php?product_list', '_self')</script>";
            }

    }
}
?>