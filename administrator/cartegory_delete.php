<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['cartegory_delete'])){
            $cartegory_id = $_GET['cartegory_delete'];
            $select_product = "select * from products where cartegory_id = '$cartegory_id'";
            $run_select = mysqli_query($conn, $select_product);
            while($row_select = mysqli_fetch_array($run_select)){
                $product_id = $row_select['product_id'];
                $delete_order = "delete from customer_orders where product_id = '$product_id'";
                $run_delete_order = mysqli_query($conn, $delete_order);
                $delete_product_img = "delete from product_img where product_id = '$product_id'";
                $run_delete_img = mysqli_query($conn, $delete_product_img);
                $delete_product = "delete from products where product_id = '$product_id'";
                $run_delete = mysqli_query($conn, $delete_product);
            }
            $delete_cartegory = "delete from cartegory where cartegory_id = '$cartegory_id'";
            $run_delete = mysqli_query($conn, $delete_cartegory);
            if($run_delete){
                echo "<script>alert('Xóa thương hiệu thành công')</script>";
                echo "<script>window.open('index.php?cartegory_list', '_self')</script>";
            }

    }
}
?>