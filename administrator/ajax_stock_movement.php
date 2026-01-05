<?php
include("includes/database.php");

header('Content-Type: application/json');

if(isset($_POST['add_movement'])){
  $product_color_img_id = (int)$_POST['product_color_img_id'];
  $movement_type = $_POST['movement_type'] ?? 'import';
  $quantity = (int)$_POST['quantity'];
  $import_price = (int)$_POST['import_price'] ?? 0;
  $export_price = (int)$_POST['export_price'] ?? 0;
  $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
  
  // Lấy thông tin product từ product_color_img_id
  $get_product = "SELECT product_id, product_color_id, product_quantity FROM product_img WHERE product_color_img_id = '$product_color_img_id'";
  $res_product = mysqli_query($conn, $get_product);
  $product_info = mysqli_fetch_assoc($res_product);
  
  if(!$product_info || $quantity <= 0){
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
  }
  
  $product_id = $product_info['product_id'];
  $product_color_id = $product_info['product_color_id'];
  $current_stock = (int)$product_info['product_quantity'];
  $admin_id = $_SESSION['admin_id'] ?? null;
  
  // Kiểm tra xuất vượt quá tồn kho
  if($movement_type === 'export' && $current_stock < $quantity){
    echo json_encode(['success' => false, 'message' => 'Số lượng xuất vượt quá tồn kho!']);
    exit;
  }
  
  // Insert vào stock_movements
  $insert_movement = "
    INSERT INTO stock_movements (product_color_img_id, product_id, product_color_id, movement_type, quantity, import_price, export_price, notes, created_by)
    VALUES ('$product_color_img_id', '$product_id', '$product_color_id', '$movement_type', '$quantity', '$import_price', '$export_price', '$notes', '$admin_id')
  ";
  
  if(mysqli_query($conn, $insert_movement)){
    // Cập nhật product_quantity
    if($movement_type === 'import'){
      $new_stock = $current_stock + $quantity;
      $update_qty = "UPDATE product_img SET product_quantity = '$new_stock' WHERE product_color_img_id = '$product_color_img_id'";
    } else if($movement_type === 'export'){
      $new_stock = $current_stock - $quantity;
      $update_qty = "UPDATE product_img SET product_quantity = '$new_stock' WHERE product_color_img_id = '$product_color_img_id'";
    } else {
      $new_stock = $current_stock;
      $update_qty = null;
    }
    
    if($update_qty) mysqli_query($conn, $update_qty);
    
    // Lấy dữ liệu tổng kho mới
    $sql_total = "SELECT SUM(product_quantity) AS total_stock FROM product_img";
    $res_total = mysqli_query($conn, $sql_total);
    $row_total = mysqli_fetch_assoc($res_total);
    $total_stock = (int)($row_total['total_stock'] ?? 0);
    
    // Lấy dữ liệu nhập/xuất mới
    $sql = "
      SELECT 
        COALESCE(SUM(CASE WHEN movement_type = 'import' THEN quantity ELSE 0 END), 0) AS total_imported,
            COALESCE(SUM(CASE WHEN movement_type = 'export' THEN quantity ELSE 0 END), 0) AS total_exported,
            -- Weighted average: sum(import_price * quantity) / sum(quantity)
            COALESCE(ROUND(
              SUM(CASE WHEN movement_type = 'import' THEN import_price * quantity ELSE 0 END)
              / NULLIF(SUM(CASE WHEN movement_type = 'import' THEN quantity ELSE 0 END), 0)
            ), 0) AS avg_import_price,
            -- last import price (most recent import record)
            (SELECT sm2.import_price FROM stock_movements sm2 WHERE sm2.product_color_img_id = '$product_color_img_id' AND sm2.movement_type = 'import' ORDER BY sm2.created_at DESC, sm2.movement_id DESC LIMIT 1) AS last_import_price
          FROM stock_movements
          WHERE product_color_img_id = '$product_color_img_id'
    ";
    $res = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($res);
    
    echo json_encode([
      'success' => true,
      'message' => 'Cập nhật kho thành công!',
      'new_stock' => (int)$new_stock,
      'total_stock' => $total_stock,
      'total_imported' => (int)$data['total_imported'],
      'total_exported' => (int)$data['total_exported'],
      'avg_import_price' => (int)$data['avg_import_price'],
      'last_import_price' => isset($data['last_import_price']) ? (int)$data['last_import_price'] : 0
    ]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . mysqli_error($conn)]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
}
?>
