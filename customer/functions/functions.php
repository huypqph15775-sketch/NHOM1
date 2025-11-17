<?php

//get real ip user
function getRealIpUser(){
  // switch(true){
  //   case(!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
  //   case(!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
  //   case(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
    
  //   default : return $_SERVER['REMOTE_ADDR'];
  // }
  if(!empty($_SERVER['HTTP_X_REAL_IP'])){
    return $_SERVER['HTTP_X_REAL_IP'];
  }
  else if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    return $_SERVER['HTTP_CLIENT_IP'];
  }
  else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    return $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else{
    return $_SERVER['REMOTE_ADDR'];
  }
}

// get count of items cart
function items(){
  global $conn;
  $count_items = 0;
  if(isset($_SESSION['customer_id'])){
    $customer_id = $_SESSION['customer_id'];
    $get_items = "select * from cart where customer_id='$customer_id'";
    $run_items = mysqli_query($conn, $get_items);
    while($row_items = mysqli_fetch_array($run_items)){
      $count_item = $row_items['quantity'];
      $count_items += $count_item;
    }
  }
  return $count_items;
}

//total_price
function total_price(){
  global $conn;
  $total = 0;
  if(isset($_SESSION['customer_id'])){
    $customer_id = $_SESSION['customer_id'];
    $select_cart = "select * from cart where customer_id='$customer_id'";
    $run_cart = mysqli_query($conn, $select_cart);
    while($record = mysqli_fetch_array($run_cart)){
      $product_id = $record['product_id'];
      $quantity = $record['quantity'];
      $color = $record['color'];
      $get_color = "select * from product_color where product_color_name = '$color'";
      $run_color = mysqli_query($conn, $get_color);
      $row_color = mysqli_fetch_array($run_color);
      $color_id = $row_color['product_color_id'];
      $get_price_des = "select * from product_img where product_id = '$product_id' and product_color_id = '$color_id'";
      $run_price_des =  mysqli_query($conn, $get_price_des);
      while($row_price_des = mysqli_fetch_array($run_price_des)){
        $sub_total = $row_price_des['product_price_des'] * $quantity;
        $total += $sub_total;
      }
    }
  }
  return $total;
}

// format price
if (!function_exists('currency_format')) {
  function currency_format($number, $suffix = '₫') {
      if (!empty($number)) {
          return number_format($number, 0, ',', '.') . "{$suffix}";
      }
  }
}

?>