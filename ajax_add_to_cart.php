<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'includes/database.php';

// helper to log debug info and send JSON response
function send_json($arr){
  // try to log incoming POST and the response for debugging
  $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
  if(!is_dir($logDir)) @mkdir($logDir, 0755, true);
  $logFile = $logDir . DIRECTORY_SEPARATOR . 'cart_debug.log';
  $entry = date('Y-m-d H:i:s') . "\nREQUEST: " . json_encode($_REQUEST) . "\nRESPONSE: " . json_encode($arr) . "\n---\n";
  @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
  echo json_encode($arr);
  exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  send_json(['success'=>false, 'message'=>'Invalid method']);
}

if(!isset($_SESSION['customer_id'])){
  send_json(['success'=>false, 'message'=>'Bạn cần đăng nhập để thêm vào giỏ hàng']);
}

$customer_id = (int)$_SESSION['customer_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$color = isset($_POST['product_color']) ? trim($_POST['product_color']) : (isset($_POST['color']) ? trim($_POST['color']) : '');

if($product_id <= 0){
  send_json(['success'=>false, 'message'=>'Sản phẩm không hợp lệ']);
}

// find color id and product quantity
$get_color = "select * from product_color where product_color_name = ? LIMIT 1";
if($stmt = $conn->prepare($get_color)){
  $stmt->bind_param('s', $color);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res ? $res->fetch_assoc() : null;
  $stmt->close();
  $product_color_id = $row ? (int)$row['product_color_id'] : 0;
} else {
  $product_color_id = 0;
}

$select_quantity = "select * from product_img where product_id=? and product_color_id=? LIMIT 1";
if($stmt2 = $conn->prepare($select_quantity)){
  $stmt2->bind_param('ii', $product_id, $product_color_id);
  $stmt2->execute();
  $res2 = $stmt2->get_result();
  $row2 = $res2 ? $res2->fetch_assoc() : null;
  $stmt2->close();
  $product_quantity = $row2 ? (int)$row2['product_quantity'] : 0;
} else {
  $product_quantity = 0;
}

// check existing
$check_product = "select * from cart where customer_id = ? and product_id = ? and color = ? LIMIT 1";
if($stmt3 = $conn->prepare($check_product)){
  $stmt3->bind_param('iis', $customer_id, $product_id, $color);
  $stmt3->execute();
  $res3 = $stmt3->get_result();
  $exists = $res3 && $res3->num_rows>0;
  $stmt3->close();
} else {
  $exists = false;
}

if($exists){
  // compute current items count to allow client to update badge even when product already exists
  $items_count = 0;
  $q = "select quantity from cart where customer_id=?";
  if($s = $conn->prepare($q)){
    $s->bind_param('i', $customer_id);
    $s->execute();
    $r = $s->get_result();
    while($c = $r ? $r->fetch_assoc() : null){
      $items_count += (int)$c['quantity'];
    }
    $s->close();
  }

  send_json(['success'=>false, 'message'=>'Sản phẩm đã có trong giỏ hàng', 'items_count'=>$items_count]);
}

if($quantity > $product_quantity){
  // include current items_count so the client can update the badge
  $items_count = 0;
  $q = "select quantity from cart where customer_id=?";
  if($s = $conn->prepare($q)){
    $s->bind_param('i', $customer_id);
    $s->execute();
    $r = $s->get_result();
    while($c = $r ? $r->fetch_assoc() : null){
      $items_count += (int)$c['quantity'];
    }
    $s->close();
  }

  send_json(['success'=>false, 'message'=>'Số lượng vượt quá số lượng tồn', 'items_count'=>$items_count]);
}

$insert = "insert into cart (customer_id, product_id, color, quantity) values (?, ?, ?, ?)";
if($stmt4 = $conn->prepare($insert)){
  $stmt4->bind_param('iisi', $customer_id, $product_id, $color, $quantity);
  $ok = $stmt4->execute();
  $stmt4->close();
  if($ok){
    // compute items count
    $items_count = 0;
    $q = "select * from cart where customer_id=?";
    if($s = $conn->prepare($q)){
      $s->bind_param('i', $customer_id);
      $s->execute();
      $r = $s->get_result();
      while($c = $r ? $r->fetch_assoc() : null){
        $items_count += (int)$c['quantity'];
      }
      $s->close();
    }

    send_json(['success'=>true, 'message'=>'Đã thêm vào giỏ hàng', 'items_count'=>$items_count]);
  }
}

send_json(['success'=>false, 'message'=>'Không thể thêm vào giỏ hàng']);
