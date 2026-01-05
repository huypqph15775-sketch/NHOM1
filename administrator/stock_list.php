<?php
include("includes/database.php");

// Ensure session started and determine current admin/role level
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_level = 0;
if (isset($_SESSION['admin_level'])) {
  $user_level = (int)$_SESSION['admin_level'];
} elseif (isset($_SESSION['role_level'])) {
  $user_level = (int)$_SESSION['role_level'];
}

// Tổng tồn kho
$sql_total = "SELECT SUM(pi.product_quantity) AS total_stock FROM product_img pi";
$res_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($res_total);
$total_stock = (int) ($row_total['total_stock'] ?? 0);

// Lấy thông tin nhập/xuất/tồn kho
$sql = "
  SELECT 
    pi.product_color_img_id,
    pi.product_id,
    pi.product_color_id,
    pi.product_color_img,
    pi.product_price AS export_price,
    pi.product_price_des,
    pi.product_quantity AS current_stock,
    p.product_name,
    pc.product_color_name,
    -- total imported quantity for this product color
    COALESCE((SELECT SUM(quantity) FROM stock_movements sm2 WHERE sm2.product_color_img_id = pi.product_color_img_id AND sm2.movement_type = 'import'), 0) AS total_imported,
    -- total exported quantity for this product color
    COALESCE((SELECT SUM(quantity) FROM stock_movements sm3 WHERE sm3.product_color_img_id = pi.product_color_img_id AND sm3.movement_type = 'export'), 0) AS total_exported,
    -- Weighted average import price per unit: sum(import_price * quantity) / sum(quantity)
    COALESCE(ROUND(
      (SELECT SUM(sm4.import_price * sm4.quantity) FROM stock_movements sm4 WHERE sm4.product_color_img_id = pi.product_color_img_id AND sm4.movement_type = 'import')
      / NULLIF((SELECT SUM(sm5.quantity) FROM stock_movements sm5 WHERE sm5.product_color_img_id = pi.product_color_img_id AND sm5.movement_type = 'import'), 0)
    ), 0) AS avg_import_price,
    -- Last import price (most recent import record)
    (SELECT smn.import_price FROM stock_movements smn WHERE smn.product_color_img_id = pi.product_color_img_id AND smn.movement_type = 'import' ORDER BY smn.created_at DESC, smn.movement_id DESC LIMIT 1) AS last_import_price
  FROM product_img AS pi
  INNER JOIN products AS p ON pi.product_id = p.product_id
  LEFT JOIN product_color AS pc ON pi.product_color_id = pc.product_color_id
  ORDER BY p.product_id DESC, pi.product_color_id ASC
";
$result = mysqli_query($conn, $sql);

// Lấy dữ liệu riêng cho dropdown (vì $result sẽ được dùng hết)
$sql_dropdown = "
  SELECT 
    pi.product_color_img_id,
    p.product_name,
    pc.product_color_name
  FROM product_img AS pi
  INNER JOIN products AS p ON pi.product_id = p.product_id
  LEFT JOIN product_color AS pc ON pi.product_color_id = pc.product_color_id
  ORDER BY p.product_id DESC, pi.product_color_id ASC
";
$result_dropdown = mysqli_query($conn, $sql_dropdown);
$products_list = [];
while($row = mysqli_fetch_assoc($result_dropdown)){
  $products_list[] = $row;
}

// Không xử lý POST ở đây nữa, dùng AJAX thay thế
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Quản lý kho nhập (Tổng tồn: <?php echo number_format($total_stock); ?>)</h5>
    <?php if ($user_level === 2 || $user_level === 4): ?>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMovementModal">+ Thêm phiếu nhập kho</button>
    <?php else: ?>
    <button class="btn btn-primary btn-sm d-none" aria-hidden="true">+ Thêm phiếu nhập kho</button>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <strong>ℹ️ Lưu ý:</strong> Xuất kho tự động theo đơn hàng bán ra. Bạn chỉ cần nhập phiếu khi nhận hàng từ nhà cung cấp.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div class="mb-3 d-flex gap-2">
      <button id="btnAllStock" class="btn btn-secondary btn-sm">Tất cả</button>
      <button id="btnLowStock" class="btn btn-warning btn-sm">Sắp hết hàng</button>
      <button id="btnOutOfStock" class="btn btn-danger btn-sm">Hết hàng</button>
      <button id="btnHighStock" class="btn btn-success btn-sm">Tồn kho</button>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle" id="stockTable">
        <script>
        function getStock(tr) {
          var strong = tr.cells[6].querySelector('strong');
          return strong ? parseInt(strong.textContent) : 0;
        }

        // Tất cả: hiện toàn bộ và sắp xếp tồn kho từ nhỏ đến lớn
        document.getElementById('btnAllStock').addEventListener('click', function() {
          var table = document.getElementById('stockTable').getElementsByTagName('tbody')[0];
          var rows = Array.from(table.rows);
          rows.forEach(function(row) { row.style.display = ''; });
          rows.sort(function(a, b) {
            var sa = getStock(a);
            var sb = getStock(b);
            return sa - sb;
          });
          rows.forEach(function(row) { table.appendChild(row); });
        });


        // Sắp hết hàng: chỉ hiện sản phẩm tồn kho < 20
        document.getElementById('btnLowStock').addEventListener('click', function() {
          var table = document.getElementById('stockTable').getElementsByTagName('tbody')[0];
          var rows = Array.from(table.rows);
          rows.forEach(function(row) {
            var stock = getStock(row);
            row.style.display = (stock > 0 && stock < 20) ? '' : 'none';
          });
        });

        // Hết hàng (chỉ hiện hàng tồn kho = 0)
        document.getElementById('btnOutOfStock').addEventListener('click', function() {
          var table = document.getElementById('stockTable').getElementsByTagName('tbody')[0];
          var rows = Array.from(table.rows);
          rows.forEach(function(row) {
            var stock = getStock(row);
            row.style.display = (stock === 0) ? '' : 'none';
          });
        });

        // Tồn kho (chỉ hiện hàng tồn kho > 100)
        document.getElementById('btnHighStock').addEventListener('click', function() {
          var table = document.getElementById('stockTable').getElementsByTagName('tbody')[0];
          var rows = Array.from(table.rows);
          rows.forEach(function(row) {
            var stock = getStock(row);
            row.style.display = (stock > 100) ? '' : 'none';
          });
        });
        </script>
        <thead class="table-light">
          <tr class="text-center">
            <th style="width: 5%">#</th>
            <th style="width: 8%">Ảnh</th>
            <th style="width: 20%">Sản phẩm</th>
            <th style="width: 10%">Màu</th>
            <th style="width: 12%">Tổng nhập</th>
            <th style="width: 12%">Tổng xuất</th>
            <th style="width: 12%">Tồn kho</th>
            <?php if ($user_level === 2 || $user_level === 4): ?>
            <th style="width: 12%">Giá nhập (VNĐ)</th>
            <?php else: ?>
            <th style="width: 12%" class="d-none">Giá nhập (VNĐ)</th>
            <?php endif; ?>
            <th style="width: 9%">Hành động</th>
          </tr>
        </thead>
        <tbody>
        <?php if($result && mysqli_num_rows($result) > 0): $i = 1; ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?php echo $i++; ?></td>
              <td class="text-center">
                <?php if(!empty($row['product_color_img'])): ?>
                  <img src="product_img/<?php echo htmlspecialchars($row['product_color_img']); ?>" width="70px" style="object-fit: contain;" alt="">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                <br><small class="text-muted">Mã: <?php echo (int)$row['product_id']; ?></small>
              </td>
              <td class="text-center">
                <span class="badge bg-info"><?php echo htmlspecialchars($row['product_color_name'] ?? '-'); ?></span>
              </td>
              <td class="text-center">
                <span class="badge bg-success" style="font-size: 12px;">+<?php echo (int)$row['total_imported']; ?></span>
              </td>
              <td class="text-center">
                <span class="badge bg-danger" style="font-size: 12px;">-<?php echo (int)$row['total_exported']; ?></span>
              </td>
              <td class="text-center">
                <?php $cs = (int)$row['current_stock']; ?>
                <strong class="<?php echo ($cs === 0) ? 'text-danger' : (($cs < 20) ? 'text-warning' : 'text-success'); ?>">
                  <?php echo $cs; ?>
                </strong>
                <?php if($cs === 0): ?>
                  <div><span class="badge bg-danger mt-1">Hết hàng</span></div>
                <?php elseif($cs > 0 && $cs < 20): ?>
                  <div><span class="badge bg-warning text-dark mt-1">Sắp hết (<?php echo $cs; ?>)</span></div>
                <?php endif; ?>
              </td>
              <td class="text-end <?php echo ($user_level === 2 || $user_level === 4) ? '' : 'd-none'; ?>">
                <strong class="last-import-price"><?php echo (!empty($row['last_import_price'])) ? number_format((int)$row['last_import_price'], 0, ',', '.') . ' đ' : '-'; ?></strong>
              </td>
              <td class="text-center">
                <?php if ($user_level === 2 || $user_level === 4): ?>
                <button class="btn btn-sm btn-outline-success open-movement-modal" 
                  data-product-id="<?php echo (int)$row['product_color_img_id']; ?>" 
                  data-product-name="<?php echo htmlspecialchars($row['product_name']); ?>">
                  + Nhập
                </button>
                <?php else: ?>
                <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-4">Chưa có dữ liệu tồn kho.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Thêm Phiếu Nhập Kho -->
<?php if ($user_level === 2 || $user_level === 4): ?>
<div class="modal fade" id="addMovementModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📦 Thêm phiếu nhập kho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div id="alertContainer" style="margin: 10px 15px;"></div>
      <form id="movementForm" onsubmit="submitMovement(event)">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
            <select id="product_color_img_id" name="product_color_img_id" class="form-select" required onchange="updateProductDisplay()">
              <option value="">-- Chọn sản phẩm --</option>
              <?php foreach($products_list as $prod): ?>
                <option value="<?php echo (int)$prod['product_color_img_id']; ?>" 
                  data-product-name="<?php echo htmlspecialchars($prod['product_name']); ?>"
                  data-color="<?php echo htmlspecialchars($prod['product_color_name'] ?? '-'); ?>">
                  <?php echo htmlspecialchars($prod['product_name']); ?> - <?php echo htmlspecialchars($prod['product_color_name'] ?? '-'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Số lượng nhập <span class="text-danger">*</span></label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Nhập theo <span class="text-danger">*</span></label>
            <div class="d-flex gap-3 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="import_mode" id="import_mode_unit" value="unit" checked>
                <label class="form-check-label" for="import_mode_unit">Giá mỗi cái</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="import_mode" id="import_mode_total" value="total">
                <label class="form-check-label" for="import_mode_total">Tổng tiền</label>
              </div>
            </div>
            <label id="import_price_label" class="form-label">Giá nhập (VNĐ) <span class="text-danger">*</span></label>
            <input type="number" id="import_price" name="import_price" class="form-control" min="0" required placeholder="Nhập giá mỗi cái">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Ghi chú / Lý do nhập</label>
            <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="VD: Nhập từ nhà cung cấp A..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" id="submitBtn" class="btn btn-success">✓ Lưu phiếu nhập</button>
        </div>
      </form>
    </div>
  </div>
  </div>
  <?php endif; ?>

<script>
// Auto click 'Tất cả' button on page load
window.addEventListener('DOMContentLoaded', function() {
  var btnAll = document.getElementById('btnAllStock');
  if (btnAll) btnAll.click();
});
let movementModal = null;

document.addEventListener('DOMContentLoaded', function(){
  const modalEl = document.getElementById('addMovementModal');
  if(modalEl){
    movementModal = new bootstrap.Modal(modalEl);

    // Gán sự kiện click cho tất cả nút mở modal
    document.querySelectorAll('.open-movement-modal').forEach(btn => {
      btn.addEventListener('click', function(){
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');
        openMovementModal(productId, productName);
      });
    });

    // Update placeholder/label when import mode changes
    const importModeInputs = document.querySelectorAll('input[name="import_mode"]');
    importModeInputs.forEach(r => r.addEventListener('change', function(){
      const label = document.getElementById('import_price_label');
      const input = document.getElementById('import_price');
      if(this.value === 'total'){
        label.textContent = 'Tổng tiền (VNĐ) *';
        input.placeholder = 'Nhập tổng tiền cho toàn bộ lô hàng';
      } else {
        label.textContent = 'Giá nhập (VNĐ) *';
        input.placeholder = 'Nhập giá mỗi cái';
      }
    }));
  } else {
    // If modal is not rendered (user not permitted), disable any buttons just in case
    document.querySelectorAll('.open-movement-modal').forEach(btn => {
      btn.classList.add('disabled');
      btn.setAttribute('title', 'Không có quyền nhập kho');
      btn.addEventListener('click', function(e){ e.preventDefault(); });
    });
  }
});

function openMovementModal(productColorImgId, productName){
  // Reset form
  document.getElementById('movementForm').reset();
  document.getElementById('alertContainer').innerHTML = '';
  
  // Set giá trị select
  const select = document.getElementById('product_color_img_id');
  select.value = productColorImgId;
  
  // Reset các trường khác
  document.getElementById('quantity').value = '';
  document.getElementById('import_price').value = '';
  document.getElementById('notes').value = '';
  
  // Mở modal
  movementModal.show();
}

function updateProductDisplay(){
  const select = document.getElementById('product_color_img_id');
  const selectedOption = select.options[select.selectedIndex];
  
  if(!selectedOption.value){
    return;
  }
}

function submitMovement(event){
  event.preventDefault();
  
  const productId = document.getElementById('product_color_img_id').value;
  if(!productId){
    document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Vui lòng chọn sản phẩm!</div>`;
    return;
  }
  const quantityVal = parseInt(document.getElementById('quantity').value, 10);
  if(!quantityVal || quantityVal <= 0){
    document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Số lượng phải lớn hơn 0!</div>`;
    return;
  }

  // Determine import price mode (unit or total)
  const importModeEl = document.querySelector('input[name="import_mode"]:checked');
  const importMode = importModeEl ? importModeEl.value : 'unit';
  let inputPrice = parseFloat(document.getElementById('import_price').value) || 0;

  let unitPrice = 0;
  if(importMode === 'total'){
    // If user provided total money, divide by quantity to compute unit price
    unitPrice = Math.round(inputPrice / quantityVal);
  } else {
    unitPrice = Math.round(inputPrice);
  }

  const formData = new FormData();
  formData.append('add_movement', '1');
  formData.append('product_color_img_id', productId);
  formData.append('movement_type', 'import'); // Luôn là import
  formData.append('quantity', quantityVal);
  formData.append('import_price', unitPrice);
  formData.append('export_price', 0);
  formData.append('notes', document.getElementById('notes').value);
  
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
  
  fetch('ajax_stock_movement.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    const alertContainer = document.getElementById('alertContainer');
    if(data.success){
      alertContainer.innerHTML = `<div class="alert alert-success alert-dismissible fade show" role="alert">
        ✓ ${data.message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
      
      // Cập nhật số lượng trong bảng
      updateStockDisplay(productId, data.new_stock, data.total_imported, data.total_exported, data.last_import_price, data.total_stock);
      
      // Reset form
      document.getElementById('movementForm').reset();
      
      // Đóng modal sau 1.5 giây
      setTimeout(() => {
        movementModal.hide();
      }, 1500);
    } else {
      alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
        ✗ ${data.message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`;
    }
  })
  .catch(error => {
    document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Lỗi: ${error.message}</div>`;
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = '✓ Lưu phiếu nhập';
  });
}

function updateStockDisplay(productColorImgId, newStock, totalImported, totalExported, lastImportPrice, totalStock){
  // Cập nhật tổng tồn kho ở header
  const headerTotal = document.querySelector('.card-header h5');
  if(headerTotal){
    headerTotal.textContent = `Quản lý kho nhập (Tổng tồn: ${new Intl.NumberFormat('vi-VN').format(totalStock)})`;
  }
  
  // Tìm hàng của sản phẩm này trong bảng
  const tableRows = document.querySelectorAll('table tbody tr');
  tableRows.forEach(row => {
    const actionBtn = row.querySelector('.open-movement-modal');
    if(actionBtn && actionBtn.getAttribute('data-product-id') == productColorImgId){
      // Cập nhật các cột
      const cells = row.querySelectorAll('td');
      if(cells.length >= 8){
        // Cập nhật nhập (cột 5)
        const importBadge = cells[4].querySelector('span.bg-success');
        if(importBadge) importBadge.textContent = '+' + new Intl.NumberFormat('vi-VN').format(totalImported);
        
        // Cập nhật xuất (cột 6)
        const exportBadge = cells[5].querySelector('span.bg-danger');
        if(exportBadge) exportBadge.textContent = '-' + new Intl.NumberFormat('vi-VN').format(totalExported);
        
        // Cập nhật tồn (cột 7)
        const stockCell = cells[6];
        const stockStrong = stockCell.querySelector('strong');
        if(stockStrong){
          stockStrong.textContent = newStock;
          stockStrong.className = (newStock === 0) ? 'text-danger' : 'text-success';
        }
        
        // Cập nhật giá nhập (tìm theo class .last-import-price để không phụ thuộc vào vị trí cột)
        const importPriceStrong = row.querySelector('.last-import-price');
        if(importPriceStrong){
          importPriceStrong.textContent = lastImportPrice > 0 ? new Intl.NumberFormat('vi-VN').format(lastImportPrice) + ' đ' : '-';
        }
      }
    }
  });
}
</script>

