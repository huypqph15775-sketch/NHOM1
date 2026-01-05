<?php
// format price
if (!function_exists('currency_format')) {
    function currency_format($number, $suffix = '₫') {
        if (!empty($number)) {
            return number_format($number, 0, ',', '.') . "{$suffix}";
        }
    }
}

/**
 * Auto-export kho khi đơn hàng được xác nhận/vận chuyển
 * Được gọi khi admin cập nhật trạng thái đơn hàng
 */
if (!function_exists('auto_export_stock')) {
    function auto_export_stock($conn, $order_id) {
        // Lấy danh sách sản phẩm trong đơn hàng
        $sql = "
          SELECT cop.product_id, cop.product_color_id, cop.quantity
          FROM customer_order_products cop
          WHERE cop.order_id = '$order_id'
        ";
        
        $result = mysqli_query($conn, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            return false;
        }
        
        while ($row = mysqli_fetch_assoc($result)) {
            $product_id = (int)$row['product_id'];
            $product_color_id = (int)$row['product_color_id'];
            $quantity = (int)$row['quantity'];
            
            // Tìm product_color_img_id
            $sql_img = "
              SELECT product_color_img_id, product_price
              FROM product_img
              WHERE product_id = '$product_id' AND product_color_id = '$product_color_id'
              LIMIT 1
            ";
            
            $res_img = mysqli_query($conn, $sql_img);
            if ($res_img && mysqli_num_rows($res_img) > 0) {
                $img_row = mysqli_fetch_assoc($res_img);
                $product_color_img_id = (int)$img_row['product_color_img_id'];
                $export_price = (int)$img_row['product_price'];
                
                // Kiểm tra xem đã export chưa (tránh duplicate)
                $sql_check = "
                  SELECT movement_id FROM stock_movements
                  WHERE product_color_img_id = '$product_color_img_id'
                    AND movement_type = 'export'
                    AND notes LIKE '%Order ID: $order_id%'
                  LIMIT 1
                ";
                
                $res_check = mysqli_query($conn, $sql_check);
                if (!$res_check || mysqli_num_rows($res_check) == 0) {
                    // Tạo record export
                    $sql_export = "
                      INSERT INTO stock_movements 
                      (product_color_img_id, product_id, product_color_id, movement_type, quantity, export_price, notes, created_by, created_at)
                      VALUES 
                      ('$product_color_img_id', '$product_id', '$product_color_id', 'export', '$quantity', '$export_price', 'Order ID: $order_id', 1, NOW())
                    ";
                    
                    if (mysqli_query($conn, $sql_export)) {
                        // Cập nhật quantity trong product_img
                        $sql_update = "
                          UPDATE product_img 
                          SET product_quantity = product_quantity - $quantity
                          WHERE product_color_img_id = '$product_color_img_id'
                            AND product_quantity >= $quantity
                        ";
                        mysqli_query($conn, $sql_update);
                    }
                }
            }
        }
        
        return true;
    }
}

/**
 * Log lịch sử thay đổi giá bán sản phẩm
 * Được gọi khi admin sửa giá sản phẩm
 */
if (!function_exists('log_price_change')) {
    function log_price_change($conn, $product_color_img_id, $old_price, $new_price, $old_discount_price = 0, $new_discount_price = 0, $reason = '') {
        if (!$product_color_img_id || $new_price <= 0) {
            return false;
        }
        
        // Lấy thông tin product từ product_color_img_id
        $sql = "
          SELECT product_id, product_color_id FROM product_img 
          WHERE product_color_img_id = '$product_color_img_id'
          LIMIT 1
        ";
        
        $res = mysqli_query($conn, $sql);
        if (!$res || mysqli_num_rows($res) == 0) {
            return false;
        }
        
        $row = mysqli_fetch_assoc($res);
        $product_id = (int)$row['product_id'];
        $product_color_id = (int)$row['product_color_id'];
        
        // Insert vào price_history
        $admin_id = $_SESSION['admin_id'] ?? 1;
        $reason = mysqli_real_escape_string($conn, $reason);
        
        $sql_insert = "
          INSERT INTO price_history 
          (product_color_img_id, product_id, product_color_id, old_price, new_price, old_discount_price, new_discount_price, change_reason, changed_by, changed_at)
          VALUES 
          ('$product_color_img_id', '$product_id', '$product_color_id', '$old_price', '$new_price', '$old_discount_price', '$new_discount_price', '$reason', '$admin_id', NOW())
        ";
        
        return mysqli_query($conn, $sql_insert);
    }
}

?>
