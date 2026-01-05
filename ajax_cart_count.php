<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'includes/database.php';

$count = 0;
if(isset($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] > 0){
  $customer_id = (int)$_SESSION['customer_id'];
  $q = "select quantity from cart where customer_id = ?";
  if($stmt = $conn->prepare($q)){
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($r = $res ? $res->fetch_assoc() : null){
      $count += (int)$r['quantity'];
    }
    $stmt->close();
  }
}

echo json_encode(['success'=>true, 'count'=>$count]);
exit;
