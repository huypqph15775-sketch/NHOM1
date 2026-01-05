<?php
// Printable order/invoice/delivery note
include_once 'includes/database.php';
include_once 'functions/functions.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if(!isset($_GET['order_id'])){
    echo "Missing order_id"; exit;
}
$order_id = intval($_GET['order_id']);
$type = isset($_GET['type']) && in_array($_GET['type'], ['invoice','delivery']) ? $_GET['type'] : 'invoice';

// fetch order
$res = mysqli_query($conn, "SELECT * FROM customer_orders WHERE order_id = '$order_id'");
if(!$res || mysqli_num_rows($res) == 0){
    echo "Order not found"; exit;
}
$order = mysqli_fetch_assoc($res);

// access control: admin or owner
if(!isset($_SESSION['admin_id'])){
    if(!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] != $order['customer_id']){
        echo "Access denied"; exit;
    }
}

// fetch customer
$cust = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '{$order['customer_id']}'"));

// fetch items
$items_res = mysqli_query($conn, "SELECT * FROM customer_order_products WHERE order_id = '$order_id'");

function h($s){ return htmlspecialchars($s); }

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo ($type=='invoice')? 'Hoá đơn' : 'Phiếu giao hàng'; ?> #<?php echo h($order_id); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family: Arial, Helvetica, sans-serif; color:#111; margin:20px}
    .header{display:flex;justify-content:space-between;align-items:center}
    .company{font-weight:700}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    table th, table td{border:1px solid #ddd;padding:8px;text-align:left}
    .text-right{text-align:right}
    .no-print{margin-top:12px}
    @media print{ .no-print{display:none} }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="company">Phone Store</div>
      <div>Địa chỉ: 128A, Hồ Tùng Mậu,Mai Dịch, Cầu Giấy,Tp Hà Nội.</div>
      <div>Hotline: 9999.9999</div>
    </div>
    <div>
      <h2><?php echo ($type=='invoice')? 'HOÁ ĐƠN' : 'PHIẾU GIAO HÀNG'; ?></h2>
      <div>#<?php echo h($order_id); ?></div>
      <?php
        // Choose display date: use received_date for delivery notes when available
        $display_date_raw = $order['order_date'];
        if($type === 'delivery' && !empty($order['received_date'])){
          $display_date_raw = $order['received_date'];
        }
        $display_date_formatted = '';
        if(!empty($display_date_raw) && strtotime($display_date_raw) !== false){
          $display_date_formatted = date('d/m/Y H:i', strtotime($display_date_raw));
        } else {
          $display_date_formatted = h($display_date_raw);
        }
      ?>
      <div>Ngày: <?php echo h($display_date_formatted); ?></div>
    </div>
  </div>

  <h4>Thông tin khách hàng</h4>
  <div><?php echo h($cust['customer_name'] ?? $order['receiver']); ?></div>
  <div>SĐT: <?php echo h($order['receiver_phone'] ?? $cust['customer_phone']); ?></div>
  <div>Địa chỉ: <?php echo h($order['delivery_location'] ?? $cust['customer_address']); ?></div>

  <h4>Chi tiết</h4>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Giá</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i=1; $subtotal = 0;
      while($it = mysqli_fetch_assoc($items_res)){
          $pid = $it['product_id'];
          $qty = intval($it['quantity']);
          // try to get product name and price from product_img or products
          $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.product_name, pi.product_price_des FROM products p LEFT JOIN product_img pi ON p.product_id=pi.product_id WHERE p.product_id='$pid' LIMIT 1"));
          $name = $prod['product_name'] ?? ('#'.$pid);
          $price = $prod['product_price_des'] ?? 0;
          $line = $price * $qty;
          $subtotal += $line;
          echo '<tr>';
          echo '<td>'.h($i).'</td>';
          echo '<td>'.h($name).' ('.h($it['color']).')</td>';
          echo '<td>'.h($qty).'</td>';
          echo '<td class="text-right">'.h(number_format($price)).'</td>';
          echo '<td class="text-right">'.h(number_format($line)).'</td>';
          echo '</tr>';
          $i++;
      }
      ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="4" class="text-right">Tạm tính</th>
        <th class="text-right"><?php echo number_format($subtotal); ?></th>
      </tr>
      <?php if(!empty($order['discount_value'])): ?>
      <tr>
        <th colspan="4" class="text-right">Giảm giá</th>
        <th class="text-right"><?php echo number_format($order['discount_value']); ?></th>
      </tr>
      <?php endif; ?>
      <tr>
        <th colspan="4" class="text-right">Tổng</th>
        <th class="text-right"><?php echo number_format($order['total_price']); ?></th>
      </tr>
    </tfoot>
  </table>

  <div style="margin-top:18px">Ghi chú: <?php echo h($order['note'] ?? ''); ?></div>

  <?php if(!empty($order['tracking_code'])): ?>
    <div style="margin-top:12px"><b>Mã vận đơn:</b> <?php echo h($order['tracking_code']); ?></div>
  <?php endif; ?>

  <div class="no-print">
    <button onclick="window.print()" class="btn btn-primary">In</button>
    <a href="<?php echo isset($_SERVER['HTTP_REFERER'])? h($_SERVER['HTTP_REFERER']):'index.php'; ?>" class="btn btn-secondary">Đóng</a>
  </div>

  <script>
    // auto open print dialog for convenience
    window.onload = function(){ setTimeout(function(){ window.print(); }, 500); };
  </script>
</body>
</html>
