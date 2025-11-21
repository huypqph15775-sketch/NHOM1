<?php
include("includes/database.php");

// Lấy danh sách voucher
$sql = "SELECT * FROM vouchers ORDER BY voucher_id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Danh sách mã giảm giá</h5>
    <a href="index.php?voucher_add" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-lg"></i> Thêm mã mới
    </a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>#</th>
            <th>Mã</th>
            <th>Giảm (%)</th>
            <th>Giảm (VNĐ)</th>
            <th>Đơn tối thiểu</th>
            <th>Giảm tối đa</th>
            <th>Số lượt</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td class="text-center"><?= $row['voucher_id']; ?></td>
              <td class="text-center fw-bold"><?= htmlspecialchars($row['code']); ?></td>
              <td class="text-center"><?= (int)$row['discount_percent']; ?>%</td>
              <td class="text-end"><?= number_format($row['discount_amount']); ?> đ</td>
              <td class="text-end"><?= number_format($row['min_order']); ?> đ</td>
              <td class="text-end"><?= number_format($row['max_discount']); ?> đ</td>
              <td class="text-center"><?= (int)$row['quantity']; ?></td>
              <td class="text-center"><?= $row['start_date']; ?></td>
              <td class="text-center"><?= $row['end_date']; ?></td>
              <td class="text-center">
                <?php if($row['status'] == 'active'): ?>
                  <span class="badge bg-success">Đang hoạt động</span>
                <?php elseif($row['status'] == 'inactive'): ?>
                  <span class="badge bg-secondary">Tạm tắt</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']); ?></span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="index.php?voucher_edit&voucher_id=<?= $row['voucher_id']; ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="index.php?voucher_delete&voucher_id=<?= $row['voucher_id']; ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Xóa / vô hiệu hóa mã <?= htmlspecialchars($row['code']); ?>?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="11" class="text-center text-muted">Chưa có mã giảm giá nào.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
