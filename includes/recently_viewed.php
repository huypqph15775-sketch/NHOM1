<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['recently_viewed']) || !is_array($_SESSION['recently_viewed'])) return;
$items = $_SESSION['recently_viewed'];
if (count($items) == 0) return;
// show recently viewed
echo "<div class='mt-4 recently-viewed'><h5>Sản phẩm đã xem gần đây</h5><div class='row'>";
foreach ($items as $it) {
    $pid = intval($it['product_id']);
    $color_id = intval($it['color_id']);
    // fetch product basic info
    $pq = mysqli_query($conn, "SELECT p.product_name, pi.product_price_des, pi.product_price, pi.product_color_img FROM products p LEFT JOIN product_img pi ON p.product_id=pi.product_id AND pi.product_color_id='$color_id' WHERE p.product_id='$pid' LIMIT 1");
    if (!$pq) continue;
    $pr = mysqli_fetch_assoc($pq);
    if (!$pr) continue;
    $pname = htmlspecialchars($pr['product_name'] ?? 'Sản phẩm');
    $pimg = htmlspecialchars($pr['product_color_img'] ?? 'noimage.png');
    $price = $pr['product_price_des'] ?? $pr['product_price'] ?? 0;
    $pricef = function_exists('currency_format') ? currency_format($price) : number_format($price);
    $link = "shop-detail.php?product_id=$pid&color=$color_id";
    echo "<div class='col-6 col-sm-4 col-md-3 col-lg-2 mb-3'>";
    echo "<div class='card'>";
    echo "<a href='$link'><img src='administrator/product_img/$pimg' class='card-img-top' style='height:110px;object-fit:contain' alt=''></a>";
    echo "<div class='card-body p-2'>";
    echo "<div class='small' style='height:36px;overflow:hidden'>" . $pname . "</div>";
    echo "<div class='text-danger fw-bold'>" . $pricef . "</div>";
    echo "</div></div></div>";
}
echo "</div></div>";
?>