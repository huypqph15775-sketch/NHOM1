<?php
// include this where product $product_id is in scope
if(!isset($product_id)) return;
if(session_status() === PHP_SESSION_NONE) session_start();

// Build visibility filter: admin sees all; customers see only their own reviews; guests see none.
$where = "pr.product_id='".intval($product_id)."'";
$visible_only_for_customer = false;
if(isset($_SESSION['admin_id'])){
  // admin sees all
} else if(isset($_SESSION['customer_id'])){
  $cust = intval($_SESSION['customer_id']);
  $where .= " AND pr.customer_id='".$cust."'";
  $visible_only_for_customer = true;
} else {
  // not logged in and not admin: no visible reviews
  echo "<p class='mt-3'>Chưa có đánh giá nào.</p>";
  return;
}

$sql = "SELECT pr.*, c.customer_name FROM product_reviews pr LEFT JOIN customer c ON pr.customer_id = c.customer_id WHERE $where ORDER BY pr.created_at DESC LIMIT 20";
$q = mysqli_query($conn, $sql);
if(!$q){
  error_log('reviews_list query failed: '.mysqli_error($conn));
  echo "<p class='mt-3'>Chưa có đánh giá nào.</p>";
  return;
}
if(mysqli_num_rows($q) == 0){
  echo "<p class='mt-3'>Chưa có đánh giá nào.</p>";
  return;
}
while($r = mysqli_fetch_assoc($q)){
  $name = htmlspecialchars($r['customer_name'] ?? 'Khách hàng');
  $rating = intval($r['rating']);
  $title = htmlspecialchars($r['title'] ?? '');
  $msg = nl2br(htmlspecialchars($r['message'] ?? ''));
  $date = htmlspecialchars($r['created_at']);
  echo "<div class='card mb-2 p-2'>";
  // for non-admin, hide the reviewer's full name (optional) — keep name but it's the reviewer themselves
  echo "<div><strong>$name</strong> <span class='text-warning'>".str_repeat('★',$rating).str_repeat('☆',5-$rating)."</span></div>";
  if($title) echo "<div class='fw-bold'>".$title."</div>";
  if($msg) echo "<div class='small text-muted'>".$msg."</div>";
  echo "<div class='small text-secondary mt-1'>".$date."</div>";
  echo "</div>";
}
?>