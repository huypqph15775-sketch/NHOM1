<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    // Ensure `cartegory_status` column exists (best-effort)
    $check_col = mysqli_query($conn, "SHOW COLUMNS FROM `cartegory` LIKE 'cartegory_status'");
    if(mysqli_num_rows($check_col) == 0){
        @mysqli_query($conn, "ALTER TABLE `cartegory` ADD COLUMN `cartegory_status` VARCHAR(20) DEFAULT 'visible'");
    }

    // Hide action
    if(isset($_GET['cartegory_hide'])){
            $cartegory_id = $_GET['cartegory_hide'];

            // Mark related product images as 'Ngừng bán' so they won't show on public pages
            $update_product_img = "UPDATE product_img SET product_status = 'Ngừng bán' WHERE product_id IN (SELECT product_id FROM products WHERE cartegory_id = '$cartegory_id')";
            $run_update_img = mysqli_query($conn, $update_product_img);

            // Also set product_status in products table if that column exists (best-effort)
            $check = mysqli_query($conn, "SHOW COLUMNS FROM `products` LIKE 'product_status'");
            if(mysqli_num_rows($check) > 0){
                $update_products = "UPDATE products SET product_status = 'Ngừng bán' WHERE cartegory_id = '$cartegory_id'";
                @mysqli_query($conn, $update_products);
            }

            // Mark category as hidden
            $update_cat = "UPDATE cartegory SET cartegory_status = 'hidden' WHERE cartegory_id = '$cartegory_id'";
            $run_update_cat = mysqli_query($conn, $update_cat);

            if($run_update_img || $run_update_cat){
                echo "<script>alert('Ẩn thương hiệu và các sản phẩm liên quan thành công')</script>";
                echo "<script>window.open('index.php?cartegory_list', '_self')</script>";
            } else {
                echo "<script>alert('Đã xảy ra lỗi khi ẩn thương hiệu')</script>";
                echo "<script>window.open('index.php?cartegory_list', '_self')</script>";
            }

    }

    // Unhide action
    if(isset($_GET['cartegory_unhide'])){
            $cartegory_id = $_GET['cartegory_unhide'];

            // Unmark product images
            $update_product_img = "UPDATE product_img SET product_status = NULL WHERE product_id IN (SELECT product_id FROM products WHERE cartegory_id = '$cartegory_id')";
            $run_update_img = mysqli_query($conn, $update_product_img);

            // Unmark products if column exists
            $check = mysqli_query($conn, "SHOW COLUMNS FROM `products` LIKE 'product_status'");
            if(mysqli_num_rows($check) > 0){
                $update_products = "UPDATE products SET product_status = NULL WHERE cartegory_id = '$cartegory_id'";
                @mysqli_query($conn, $update_products);
            }

            // Mark category as visible
            $update_cat = "UPDATE cartegory SET cartegory_status = 'visible' WHERE cartegory_id = '$cartegory_id'";
            $run_update_cat = mysqli_query($conn, $update_cat);

            if($run_update_cat){
                echo "<script>alert('Hiện thương hiệu và các sản phẩm liên quan thành công')</script>";
                echo "<script>window.open('index.php?cartegory_list', '_self')</script>";
            } else {
                echo "<script>alert('Đã xảy ra lỗi khi hiện thương hiệu')</script>";
                echo "<script>window.open('index.php?cartegory_list', '_self')</script>";
            }

    }
}
?>