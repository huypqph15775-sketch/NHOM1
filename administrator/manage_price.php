<?php
// This file is intended to be included by `administrator/index.php`
// which already includes admin header, nav, DB and session setup.
if(!isset($conn)){
  $db_path = __DIR__ . '/../includes/database.php';
  if(file_exists($db_path)) require_once $db_path;
}
if(!function_exists('add_notification')){
  $func_path = __DIR__ . '/../functions/functions.php';
  if(file_exists($func_path)) require_once $func_path;
}
if(!function_exists('add_notification') || !isset($conn)){
  require_once __DIR__ . '/includes/nav.php';
}

// Get current user level
$user_level = 0;
if (isset($_SESSION['admin_level'])) {
    $user_level = (int)$_SESSION['admin_level'];
} elseif (isset($_SESSION['role_level'])) {
    $user_level = (int)$_SESSION['role_level'];
}

$success = '';
$error = '';

// Handle price update
if(isset($_POST['update_price'])){
  $product_img_id = intval($_POST['product_color_img_id'] ?? 0);
  $product_id = intval($_POST['product_id'] ?? 0);
  $new_price = intval($_POST['product_price'] ?? 0);
  $new_price_des = intval($_POST['product_price_des'] ?? 0);
  $product_quantity = intval($_POST['product_quantity'] ?? 0);
  $product_status = $_POST['product_status'] ?? '';

  if($product_img_id <= 0 || $product_id <= 0 || $new_price <= 0){
    $error = 'Thông tin sản phẩm không hợp lệ.';
  } else {
    // Get old price for history
    $get_old = "SELECT product_price, product_price_des FROM product_img WHERE product_color_img_id = $product_img_id";
    $run_old = mysqli_query($conn, $get_old);
    $old_data = mysqli_fetch_assoc($run_old);
    $old_price = $old_data['product_price'];
    $old_price_des = $old_data['product_price_des'];

    // Update product_img table
    $safe_status = mysqli_real_escape_string($conn, $product_status);
    $update_sql = "UPDATE product_img SET 
                    product_price = $new_price,
                    product_price_des = $new_price_des,
                    product_quantity = $product_quantity,
                    product_status = '$safe_status'
                    WHERE product_color_img_id = $product_img_id";
    
    if(mysqli_query($conn, $update_sql)){
      // Record in price_history if price changed
      if($old_price != $new_price){
        $history_sql = "INSERT INTO price_history (product_id, old_price, new_price) 
                        VALUES ($product_id, $old_price, $new_price)";
        mysqli_query($conn, $history_sql);
      }
      $success = 'Cập nhật giá sản phẩm thành công!';
    } else {
      $error = 'Lỗi cập nhật giá: ' . mysqli_error($conn);
    }
  }
}

// Get filter parameters
$product_search = $_GET['product_search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

// Build query with filters
$where_clause = "1=1";
if(!empty($product_search)){
  $safe_search = mysqli_real_escape_string($conn, $product_search);
  $where_clause .= " AND p.product_name LIKE '%$safe_search%'";
}
if(!empty($status_filter)){
  $safe_status = mysqli_real_escape_string($conn, $status_filter);
  $where_clause .= " AND pi.product_status = '$safe_status'";
}

$query = "SELECT pi.product_color_img_id, pi.product_id, pi.product_price, pi.product_price_des, 
                 pi.product_quantity, pi.product_status, pi.product_color_img,
                 p.product_name, pc.product_color_name,
                 COALESCE(MAX(sm.import_price), 0) as import_price
          FROM product_img pi
          JOIN products p ON pi.product_id = p.product_id
          JOIN product_color pc ON pi.product_color_id = pc.product_color_id
          LEFT JOIN stock_movements sm ON pi.product_color_img_id = sm.product_color_img_id AND sm.movement_type = 'import'
          WHERE $where_clause
          GROUP BY pi.product_color_img_id
          ORDER BY p.product_id DESC, pi.product_color_id ASC";

$result = mysqli_query($conn, $query);
if(!$result){
  $error = "Lỗi truy vấn: " . mysqli_error($conn);
}
?>

<div class="container-fluid mt-5 pt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <h3 class="mb-3">
        <i class="fas fa-tag"></i> Quản lý giá bán
        <?php if($user_level < 4): ?>
          <span class="badge bg-info ms-2">Nhân viên - Giá tự động (+40% giá nhập)</span>
        <?php else: ?>
          <span class="badge bg-warning ms-2">Admin - Giá tùy ý</span>
        <?php endif; ?>
      </h3>
      
      <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($success); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="row mb-4">
    <div class="col-md-12">
      <form method="get" class="row g-3">
        <div class="col-md-4">
          <input type="text" name="product_search" class="form-control" placeholder="Tìm kiếm sản phẩm..." 
                 value="<?php echo htmlspecialchars($product_search); ?>">
        </div>
        <div class="col-md-3">
          <select name="status_filter" class="form-select">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="Đang bán" <?php echo ($status_filter === 'Đang bán') ? 'selected' : ''; ?>>Đang bán</option>
            <option value="Ngừng bán" <?php echo ($status_filter === 'Ngừng bán') ? 'selected' : ''; ?>>Ngừng bán</option>
            <option value="Hết hàng" <?php echo ($status_filter === 'Hết hàng') ? 'selected' : ''; ?>>Hết hàng</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-search"></i> Tìm kiếm
          </button>
        </div>
        <div class="col-md-2">
          <a href="index.php?manage_price" class="btn btn-secondary w-100">
            <i class="fas fa-redo"></i> Làm mới
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped table-hover table-sm">
      <thead class="table-dark">
        <tr>
          <th>Sản phẩm</th>
          <th>Màu sắc</th>
          <th>Ảnh</th>
          <?php if ($user_level === 2 || $user_level === 4): ?>
            <th>Giá nhập</th>
          <?php else: ?>
            <th class="d-none">Giá nhập</th>
          <?php endif; ?>
          <?php if($user_level < 4): ?>
            <th>Giá bán (Tự động)</th>
            <th>Giá giảm</th>
          <?php else: ?>
            <th>Giá bán</th>
            <th>Giá giảm</th>
          <?php endif; ?>
          <th>Số lượng</th>
          <th>Trạng thái</th>
          <th style="width: 80px;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if($result && mysqli_num_rows($result) > 0){
          while($row = mysqli_fetch_assoc($result)){
            $product_img_id = $row['product_color_img_id'];
            $product_id = $row['product_id'];
            $product_name = htmlspecialchars($row['product_name']);
            $product_color_name = htmlspecialchars($row['product_color_name']);
            $product_price = $row['product_price'];
            $product_price_des = $row['product_price_des'];
            $product_quantity = $row['product_quantity'];
            $product_status = htmlspecialchars($row['product_status']);
            $product_color_img = htmlspecialchars($row['product_color_img']);
            $import_price = intval($row['import_price']);
            
            // Auto calculate price for non-admin (import + 40%)
            $auto_price = $import_price > 0 ? intval($import_price * 1.4) : $product_price;
            
            $status_badge = '';
            if($product_status === 'Đang bán'){
              $status_badge = '<span class="badge bg-success">Đang bán</span>';
            } elseif($product_status === 'Ngừng bán'){
              $status_badge = '<span class="badge bg-secondary">Ngừng bán</span>';
            } else {
              $status_badge = '<span class="badge bg-danger">Hết hàng</span>';
            }
        ?>
        <tr>
          <td>
            <strong><?php echo $product_name; ?></strong>
          </td>
          <td><?php echo $product_color_name; ?></td>
          <td>
            <img src="product_img/<?php echo $product_color_img; ?>" 
                 style="width: 50px; height: 50px; object-fit: contain;" alt="<?php echo $product_color_name; ?>">
          </td>
          <?php if ($user_level === 2 || $user_level === 4): ?>
          <td>
            <span class="badge bg-primary"><?php echo number_format($import_price, 0, ',', '.'); ?> VNĐ</span>
          </td>
          <?php else: ?>
          <td class="d-none">
            <span class="badge bg-primary"><?php echo number_format($import_price, 0, ',', '.'); ?> VNĐ</span>
          </td>
          <?php endif; ?>
          <?php if($user_level < 4): ?>
            <td>
              <span class="text-success fw-bold"><?php echo number_format($auto_price, 0, ',', '.'); ?> VNĐ</span>
              <small class="d-block text-muted">(+40% tự động)</small>
            </td>
          <?php else: ?>
            <td>
              <span><?php echo number_format($product_price, 0, ',', '.'); ?> VNĐ</span>
            </td>
          <?php endif; ?>
          <td>
            <span><?php echo number_format($product_price_des, 0, ',', '.'); ?> VNĐ</span>
          </td>
          <td>
            <?php echo $product_quantity; ?>
          </td>
          <td><?php echo $status_badge; ?></td>
          <td>
            <?php if($user_level >= 4): ?>
              <button type="button" class="btn btn-sm btn-primary editBtn" data-product-img-id="<?php echo $product_img_id; ?>" 
                      data-product-id="<?php echo $product_id; ?>" 
                      data-product-name="<?php echo htmlspecialchars($product_name); ?>"
                      data-product-color="<?php echo htmlspecialchars($product_color_name); ?>"
                      data-price="<?php echo $product_price; ?>"
                      data-price-des="<?php echo $product_price_des; ?>"
                      data-quantity="<?php echo $product_quantity; ?>"
                      data-status="<?php echo $product_status; ?>">
                <i class="fas fa-edit"></i> Sửa
              </button>
            <?php else: ?>
              <span class="badge bg-secondary">Chỉ admin</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php
          }
        } else {
          echo '<tr><td colspan="9" class="text-center text-muted">Không tìm thấy sản phẩm nào</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="row mt-3">
    <div class="col-md-12">
      <a href="index.php?dashboard" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
      </a>
    </div>
  </div>
</div>

<!-- Edit Modal - Chỉ Admin Level 4 -->
<?php if($user_level >= 4): ?>
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cập nhật giá sản phẩm (Admin)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <input type="hidden" name="product_color_img_id" id="modalProductImgId">
          <input type="hidden" name="product_id" id="modalProductId">
          
          <div class="mb-3">
            <label class="form-label">Sản phẩm</label>
            <input type="text" class="form-control" id="modalProductName" readonly>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Màu sắc</label>
            <input type="text" class="form-control" id="modalColorName" readonly>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="product_price" id="modalPrice" required min="0">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Giá giảm (VNĐ)</label>
            <input type="number" class="form-control" name="product_price_des" id="modalPriceDes" min="0">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" class="form-control" name="product_quantity" id="modalQuantity" min="0">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Trạng thái bán</label>
            <select class="form-select" name="product_status" id="modalStatus">
              <option value="Đang bán">Đang bán</option>
              <option value="Hết hàng">Hết hàng</option>
              <option value="Ngừng bán">Ngừng bán</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="update_price" class="btn btn-primary">
            <i class="fas fa-save"></i> Lưu thay đổi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // Edit button click
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function(){
      document.getElementById('modalProductImgId').value = this.dataset.productImgId;
      document.getElementById('modalProductId').value = this.dataset.productId;
      document.getElementById('modalProductName').value = this.dataset.productName;
      document.getElementById('modalColorName').value = this.dataset.productColor;
      document.getElementById('modalPrice').value = this.dataset.price;
      document.getElementById('modalPriceDes').value = this.dataset.priceDes;
      document.getElementById('modalQuantity').value = this.dataset.quantity;
      document.getElementById('modalStatus').value = this.dataset.status;
      
      const modal = new bootstrap.Modal(document.getElementById('editModal'));
      modal.show();
    });
  });
});
</script>
<?php endif; ?>

<?php
// EOF
