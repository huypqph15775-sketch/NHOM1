<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
// Simple endpoint to save a pending action (e.g., add_to_cart) into session
// Expected POST fields: action=add_to_cart, product_id, quantity, product_color (or color)

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  echo json_encode(['success'=>false,'message'=>'Invalid method']);
  exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
if($action !== 'add_to_cart'){
  echo json_encode(['success'=>false,'message'=>'Unsupported action']);
  exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$color = isset($_POST['product_color']) ? trim($_POST['product_color']) : (isset($_POST['color']) ? trim($_POST['color']) : '');

if($product_id <= 0){
  echo json_encode(['success'=>false,'message'=>'Invalid product']);
  exit;
}

$_SESSION['pending_action'] = [
  'action' => 'add_to_cart',
  'product_id' => $product_id,
  'quantity' => max(1, $quantity),
  'color' => $color,
  'created_at' => time()
];

echo json_encode(['success'=>true,'message'=>'Pending action saved']);
exit;
