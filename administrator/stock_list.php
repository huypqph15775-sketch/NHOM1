<?php
include("includes/database.php");

// Tổng tồn kho
$sql_total = "SELECT SUM(product_quantity) AS total_stock FROM product_img";
$res_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($res_total);
$total_stock = (int) ($row_total['total_stock'] ?? 0);

// Lấy danh sách tồn kho theo sản phẩm / màu
$sql = "SELECT pi.*, p.product_name, p.product_id, pc.product_color_name
        FROM product_img AS pi
        INNER JOIN products AS p ON pi.product_id = p.product_id
        LEFT JOIN product_color AS pc ON pi.product_color_id = pc.product_color_id
        ORDER BY p.product_id DESC, pi.product_color_id ASC";
$result = mysqli_query($conn, $sql);
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Tồn kho sản phẩm (Tổng: <?php echo $total_stock; ?>)</h5>
    <a href="index.php?product_list" class="btn btn-secondary btn-sm">Danh sách sản phẩm</a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>#</th>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Mã SP</th>
            <th>Màu</th>
            <th>Giá (VNĐ)</th>
            <th>Giá KM (VNĐ)</th>
            <th>Tồn kho</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
        <?php if($result && mysqli_num_rows($result) > 0): $i = 1; ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?php echo $i++; ?></td>
              <td class="text-center">
                <?php if(!empty($row['product_color_img'])): ?>
                  <img src="product_img/<?php echo htmlspecialchars($row['product_color_img']); ?>" width="80px" style="object-fit: contain;" alt="">
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($row['product_name']); ?></td>
              <td class="text-center"><?php echo (int)$row['product_id']; ?></td>
              <td class="text-center"><?php echo htmlspecialchars($row['product_color_name'] ?? '-'); ?></td>
              <td class="text-end"><?php echo number_format($row['product_price']); ?> đ</td>
              <td class="text-end"><?php echo number_format($row['product_price_des']); ?> đ</td>
              <td class="text-center"><?php echo (int)$row['product_quantity']; ?></td>
              <td class="text-center">
                <a href="index.php?product_edit&product_id=<?php echo (int)$row['product_id']; ?>" class="btn btn-sm btn-outline-primary">Sửa SP</a>
                <a href="index.php?product_add_img&product_id=<?php echo (int)$row['product_id']; ?>" class="btn btn-sm btn-outline-secondary">Sửa kho</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center text-muted">Chưa có dữ liệu tồn kho.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
