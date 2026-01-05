<?php
// Đảm bảo sử dụng giao diện admin chuẩn
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../functions/functions.php';
// ensure admin is logged in (will redirect to signin if not)
checkAdminLogin();
// include admin navigation and header for consistent admin layout
include(__DIR__ . '/includes/nav.php');

// Handle deletion
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])){
    $del_id = intval($_POST['delete_review_id']);
    mysqli_query($conn, "DELETE FROM product_reviews WHERE id='$del_id'");
    header('Location: reviews_list.php'); exit;
}

// Fetch reviews with product and customer
$sql = "SELECT pr.*, p.product_name, c.customer_name FROM product_reviews pr LEFT JOIN products p ON pr.product_id=p.product_id LEFT JOIN customer c ON pr.customer_id=c.customer_id ORDER BY pr.created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Danh sách đánh giá</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
<div class="admin-content mt-4" style="max-width:100vw;">
  <h3 class="text-start">Danh sách đánh giá</h3>
  <?php if(!$res || mysqli_num_rows($res) == 0): ?>
    <div class="alert alert-info">Chưa có đánh giá nào.</div>
  <?php else: ?>
    <table class="table table-striped text-start pe-0 ps-0" style="margin-left:0;width:100%;">
      <thead>
        <tr><th>ID</th><th>Sản phẩm</th><th>Khách hàng</th><th>Đơn hàng</th><th>Rating</th><th>Tiêu đề</th><th>Nội dung</th><th>Ngày</th><th></th></tr>
      </thead>
      <tbody>
      <?php while($r = mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?php echo intval($r['id']); ?></td>
          <td><?php echo htmlspecialchars($r['product_name'] ?? ('#'.$r['product_id'])); ?></td>
          <td><?php echo htmlspecialchars($r['customer_name'] ?? ('#'.$r['customer_id'])); ?></td>
          <td><?php echo intval($r['order_id']); ?></td>
          <td><?php echo intval($r['rating']); ?></td>
          <td><?php echo htmlspecialchars($r['title']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($r['message'])); ?></td>
          <td><?php echo htmlspecialchars($r['created_at']); ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Xóa đánh giá này?');">
              <input type="hidden" name="delete_review_id" value="<?php echo intval($r['id']); ?>">
              <button class="btn btn-sm btn-danger">Xóa</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
