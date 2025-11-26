<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['color_delete'])){
            $product_color_id = $_GET['color_delete'];
            $delete_product_color = "delete from product_color where product_color_id = '$product_color_id'";
            $run_delete = mysqli_query($conn, $delete_product_color);
            if($run_delete){
                echo "<script>alert('Xóa màu thành công')</script>";
                echo "<script>window.open('index.php?color_list', '_self')</script>";
            }

    }
}
?>