<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include("includes/database.php");

// add cart
function add_cart(){
  global $conn;
  if(isset($_POST['add_to_cart'])){
    if(isset($_SESSION['customer_id'])){
      $customer_id = $_SESSION['customer_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $color = $_POST['product_color'];
        $get_color = "select * from product_color where product_color_name = '$color'";
        $run_color = mysqli_query($conn, $get_color);
        $row_color = mysqli_fetch_array($run_color);
        $product_color_id = $row_color['product_color_id'];
        $select_quantity = "select * from product_img where product_id='$product_id' and product_color_id='$product_color_id'";
        $run_select_quantity = mysqli_query($conn, $select_quantity);
        $row_select_quantity = mysqli_fetch_array($run_select_quantity);
        $product_quantity = $row_select_quantity['product_quantity'];
        $check_product = "select * from cart where customer_id = '$customer_id' and product_id = '$product_id' and color = '$color'";
        $run_check = mysqli_query($conn, $check_product);
        if(mysqli_num_rows($run_check)>0){
          echo "<script>alert('Sản phẩm đã có giỏ hàng')</script>";
          echo "<script>window.open('shop-detail.php?product_id=$product_id&color=$product_color_id', '_self')</script>";
        }
        else if($quantity > $product_quantity){
          echo "<script>alert('Số lượng sản phẩm đã vượt quá số lượng cho phép')</script>";
        }
        else{

          $query = "insert into cart (customer_id, product_id, color, quantity) values ('$customer_id', '$product_id', '$color', '$quantity')";
          $run_query  = mysqli_query($conn, $query);
          echo "<script>window.open('customer/cart.php', '_self')</script>";
        }
      }
    else{
      echo "<script>window.open('signin.php', '_self')</script>";
    }
  } 
}

// get product
function getPro(){
    global $conn;
    $get_products = "select * from products order by 1 DESC LIMIT 0,15";
    $run_products = mysqli_query($conn, $get_products);
    while($row_products = mysqli_fetch_array($run_products)){
        $product_id = $row_products['product_id'];
        $product_name = $row_products['product_name'];
        $get_products_img = "select * from product_img where product_id = '$product_id' AND (product_status IS NULL OR product_status != 'Ngừng bán') LIMIT 0,1";
        $run_products_img = mysqli_query($conn, $get_products_img);
        while($row_products_img = mysqli_fetch_array($run_products_img)){
            $product_price = $row_products_img['product_price'];
            $product_price_des = $row_products_img['product_price_des'];
            $product_quantity = isset($row_products_img['product_quantity']) ? intval($row_products_img['product_quantity']) : 0;
            $product_price_format = currency_format($row_products_img['product_price']);
            $product_price_des_format = currency_format($row_products_img['product_price_des']);
            $product_color_id = $row_products_img['product_color_id'];
            $product_color_img = $row_products_img['product_color_img'];
              echo "
              <div class='col col-product'>
              
                <div class='thumb-wrapper' style='min-height: 430px;'>
                  <form action='' method='post'>
                    <button name='like_product' class='wish-icon text-danger' style='background: none; border: none;'><i class='";
                    if(isset($_SESSION['customer_id'])){
                      $customer_id = $_SESSION['customer_id'];
                      $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$product_color_id'";
                      $run_favorite = mysqli_query($conn, $check_favorte);
                      $count_favorite = mysqli_num_rows($run_favorite);
                      if($count_favorite==0){
                        echo "far fa-heart";
                      }
                      else{
                        echo "fas fa-heart";
                      }
                    }
                    else{
                      echo "far fa-heart";
                    }
                    echo " text-danger'></i></button>
                    <input type='hidden' name='favorite_product_id' value='$product_id'>
                    <input type='hidden' name='product_color_id' value='$product_color_id'>
                  </form>
                  <a href='shop-detail.php?product_id=$product_id&color=$product_color_id'>
                    <div class='img-box'>
                      <img src='administrator/product_img/$product_color_img' class='img-fluid' alt='iPhone'>									
                    </div>
                    <div class='thumb-content'>
                      <h4>$product_name</h4>									
                      <div class='star-rating'>
                        <ul class='list-inline'>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='far fa-star'></i></li>
                        </ul>
                      </div>";
            if($product_quantity == 0){
              echo "
                      <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                      <p class='text-danger fw-bold mt-2'>Hết hàng</p>
                    </div>
                </a>
                </div>

              </div>
              ";
            } else {
              if($product_price==$product_price_des){
                echo "
                        <p class='item-price'><strike></strike> <b>$product_price_des_format</b></p>
                        <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary needs-login'>Mua ngay</a>
                      </div>
                  </a>                        
                  </div>

                </div>
                ";
              }
              else{
                echo "
                        <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                        <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary needs-login'>Mua ngay</a>
                      </div>
                  </a>                        
                  </div>

                </div>
                ";
              }
            }
        }
    }
}


// get cartegory

function getCartegory(){
  global $conn;
  $get_cartegory = "select * from cartegory WHERE (cartegory_status IS NULL OR cartegory_status != 'hidden')";
  $run_cartegory = mysqli_query($conn, $get_cartegory);
  while($row_cartegory = mysqli_fetch_array($run_cartegory)){
    $cartegory_id = $row_cartegory['cartegory_id'];
    $cartegory_name = $row_cartegory['cartegory_name'];
    $cartegory_img = $row_cartegory['cartegory_img'];
    $count_sql = "SELECT DISTINCT p.product_id FROM products p JOIN product_img pi ON pi.product_id = p.product_id WHERE p.cartegory_id = '$cartegory_id' AND (pi.product_status IS NULL OR pi.product_status != 'Ngừng bán')";
    $run_products = mysqli_query($conn, $count_sql);
    $count = mysqli_num_rows($run_products);
    if($count!=0){
      echo "
      <a href='shop.php?cartegory_id=$cartegory_id' class='quicklink-logo'>
        <img src='administrator/cartegory_img/$cartegory_img' width='30px' alt='' class='no-text'>
      </a>
      ";
    }
  }
}

// get product count of cartegory

function getCatProCount(){
  global $conn;
  if(!empty($_GET['cartegory_id'])){
    $cartegory_id = $_GET['cartegory_id'];
    $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
    $run_cartegory = mysqli_query($conn, $get_cartegory);
    $row_cartegory = mysqli_fetch_array($run_cartegory);
    $cartegory_name = '';
    if($row_cartegory && isset($row_cartegory['cartegory_name'])){
      $cartegory_name = $row_cartegory['cartegory_name'];
    }
    $count_sql = "SELECT DISTINCT p.product_id FROM products p JOIN product_img pi ON pi.product_id = p.product_id WHERE p.cartegory_id = '$cartegory_id' AND (pi.product_status IS NULL OR pi.product_status != 'Ngừng bán')";
    $run_products = mysqli_query($conn, $count_sql);
    $count = mysqli_num_rows($run_products);
    if($count==0){
      echo "
        <h5 class='col-12 text-center fw-bold mt-3' style='min-height: 449px'>Không có điện thoại ".$cartegory_name."</h5>
      ";
    }
    else{
      echo "
        <h6 class='fw-bold'>".$count." điện thoại ".$cartegory_name."</h6>
      ";
    }
  }
}

// get product of cartegory

function getCatPro(){
  global $conn;
  if(!empty($_GET['cartegory_id'])){
    $cartegory_id = $_GET['cartegory_id'];
    $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
    $run_cartegory = mysqli_query($conn, $get_cartegory);
    $row_cartegory = mysqli_fetch_array($run_cartegory);
    $cartegory_name = '';
    if($row_cartegory && isset($row_cartegory['cartegory_name'])){
      $cartegory_name = $row_cartegory['cartegory_name'];
    }
    // support sorting by price when requested
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $order_dir = '';
    if($sort == 'price_asc') $order_dir = 'ASC';
    else if($sort == 'price_desc') $order_dir = 'DESC';

    if($order_dir){
      $get_products = "SELECT p.*, (
        SELECT COALESCE(product_price_des, product_price) FROM product_img pi WHERE pi.product_id = p.product_id AND (pi.product_status IS NULL OR pi.product_status != 'Ngừng bán') ORDER BY COALESCE(product_price_des, product_price) $order_dir LIMIT 1
      ) AS sort_price FROM products p WHERE p.cartegory_id = '$cartegory_id' ORDER BY sort_price $order_dir";
    } else {
      $get_products = "select * from products where cartegory_id = '$cartegory_id'";
    }
    $run_products = mysqli_query($conn, $get_products);
    while($row_products = mysqli_fetch_array($run_products)){
        $product_id = $row_products['product_id'];
        $product_name = $row_products['product_name'];
        $get_products_img = "select * from product_img where product_id = '$product_id' AND (product_status IS NULL OR product_status != 'Ngừng bán') LIMIT 0,1";
        $run_products_img = mysqli_query($conn, $get_products_img);
        while($row_products_img = mysqli_fetch_array($run_products_img)){
            $product_price = $row_products_img['product_price'];
            $product_price_des = $row_products_img['product_price_des'];
            $product_quantity = isset($row_products_img['product_quantity']) ? intval($row_products_img['product_quantity']) : 0;
            $product_price_format = currency_format($row_products_img['product_price']);
            $product_price_des_format = currency_format($row_products_img['product_price_des']);
            $product_color_id = $row_products_img['product_color_id'];
            $product_color_img = $row_products_img['product_color_img'];
            
              echo "
              <div class='col col-product p-2'>
              
                <div class='thumb-wrapper' style='min-height: 430px;'>
                  <form action='' method='post'>
                    <button name='like_product' class='wish-icon text-danger' style='background: none; border: none;'><i class='";
                    if(isset($_SESSION['customer_id'])){
                      $customer_id = $_SESSION['customer_id'];
                      $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$product_color_id'";
                      $run_favorite = mysqli_query($conn, $check_favorte);
                      $count_favorite = mysqli_num_rows($run_favorite);
                      if($count_favorite==0){
                        echo "far fa-heart";
                      }
                      else{
                        echo "fas fa-heart";
                      }
                    }
                    else{
                      echo "far fa-heart";
                    }
                    echo " text-danger'></i></button>
                    <input type='hidden' name='favorite_product_id' value='$product_id'>
                    <input type='hidden' name='product_color_id' value='$product_color_id'>
                  </form>
                  <a href='shop-detail.php?product_id=$product_id&color=$product_color_id'>
                    <div class='img-box'>
                      <img src='administrator/product_img/$product_color_img' class='img-fluid' alt='iPhone'>									
                    </div>
                    <div class='thumb-content'>
                      <h4>$product_name</h4>									
                      <div class='star-rating'>
                        <ul class='list-inline'>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='fas fa-star'></i></li>
                          <li class='list-inline-item'><i class='far fa-star'></i></li>
                        </ul>
                      </div>";
            if($product_quantity == 0){
              echo "
                      <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                      <p class='text-danger fw-bold mt-2'>Hết hàng</p>
                    </div>
                </a>                        
                </div>

              </div>
              ";
            } else {
              if($product_price==$product_price_des){
                echo "
                        <p class='item-price'><strike></strike> <b>$product_price_des_format</b></p>
                        <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary needs-login'>Mua ngay</a>
                      </div>
                  </a>                        
                  </div>

                </div>
                ";
              }
              else{
                echo "
                        <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                        <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary needs-login'>Mua ngay</a>
                      </div>
                  </a>                        
                  </div>

                </div>
                ";
              }
            }
        }
      }
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
      $get_price_des = "select * from products where product_id = '$product_id' and product_color_id = '$color_id'";
      $run_price_des =  mysqli_query($conn, $get_price_des);
      while($row_price_des = mysqli_fetch_array($run_price_des)){
        $sub_total = $row_price_des['product_price_des'] * $quantity;
        $total += $sub_total;
      }
    }
  }
  echo currency_format($total);
}


// format price
if (!function_exists('currency_format')) {
  function currency_format($number, $suffix = '₫') {
      if (!empty($number)) {
          return number_format($number, 0, ',', '.') . "{$suffix}";
      }
  }
}

// add a simple notification helper
if (!function_exists('add_notification')) {
  /**
   * Insert a notification into notifications table.
   * $user_id: recipient customer id (nullable). If null and $is_admin=1 then it's for admins.
   * $is_admin: 1 => notify admins, 0 => notify customer
   * $type/title/message: strings
   * $related_id: optional related id (order id, etc.)
   */
  function add_notification($user_id, $is_admin, $type, $title, $message, $related_id = null){
    global $conn;
    if(!isset($conn)) return false;

    // Inspect existing notifications table columns to determine schema
    $cols = array();
    $res = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications`");
    if($res){
      while($c = mysqli_fetch_assoc($res)){
        $cols[] = $c['Field'];
      }
    }

    $type_sql = mysqli_real_escape_string($conn, $type);
    $title_sql = mysqli_real_escape_string($conn, $title);
    $message_sql = mysqli_real_escape_string($conn, $message);
    $related_sql = is_null($related_id) ? 'NULL' : "'".mysqli_real_escape_string($conn, $related_id)."'";

    // Build insert dynamically to populate all compatible columns discovered
    $insert_cols = array();
    $insert_vals = array();

    // recipient columns
    if(in_array('user_id', $cols)){
      $insert_cols[] = 'user_id';
      $insert_vals[] = is_null($user_id) ? 'NULL' : "'".mysqli_real_escape_string($conn, $user_id)."'";
    }
    if(in_array('customer_id', $cols)){
      $insert_cols[] = 'customer_id';
      $insert_vals[] = is_null($user_id) ? 'NULL' : "'".mysqli_real_escape_string($conn, $user_id)."'";
    }

    // is_admin
    if(in_array('is_admin', $cols)){
      $insert_cols[] = 'is_admin';
      $insert_vals[] = $is_admin ? '1' : '0';
    }

    // type/title
    if(in_array('type', $cols)){
      $insert_cols[] = 'type';
      $insert_vals[] = "'".mysqli_real_escape_string($conn, $type)."'";
    }
    if(in_array('title', $cols)){
      $insert_cols[] = 'title';
      $insert_vals[] = "'".$title_sql."'";
    }

    // message/content
    if(in_array('message', $cols)){
      $insert_cols[] = 'message';
      $insert_vals[] = "'".$message_sql."'";
    }
    if(in_array('content', $cols)){
      $insert_cols[] = 'content';
      $insert_vals[] = "'".$message_sql."'";
    }

    // related id
    if(in_array('related_id', $cols)){
      $insert_cols[] = 'related_id';
      $insert_vals[] = $related_sql;
    }

    // is_read default
    if(in_array('is_read', $cols)){
      $insert_cols[] = 'is_read';
      $insert_vals[] = '0';
    }

    // created_at
    $has_created_at = in_array('created_at', $cols);
    if($has_created_at){
      $insert_cols[] = 'created_at';
      $insert_vals[] = 'NOW()';
    }

    // If we have columns to insert, perform insert
    if(count($insert_cols) > 0){
      $col_sql = implode(',', $insert_cols);
      $val_sql = implode(',', $insert_vals);
      $insert = "INSERT INTO notifications ($col_sql) VALUES ($val_sql)";
      $res = mysqli_query($conn, $insert);
      if(!$res){
        error_log("add_notification failed (dynamic insert): " . mysqli_error($conn) . " -- Query: " . $insert);
        return false;
      }
      return true;
    }

    // As a last resort, try permissive insert into common columns
    $insert = "INSERT INTO notifications (title, message, created_at) VALUES ('".$title_sql."', '".$message_sql."', NOW())";
    $res = @mysqli_query($conn, $insert);
    if(!$res){
      error_log("add_notification failed (permissive insert): " . mysqli_error($conn) . " -- Query: " . $insert);
      return false;
    }
    return true;
  }
}

?>