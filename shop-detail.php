<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Store</title>
    <!-- favicon -->
    <link rel="icon" href="images/phone.png">
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- bootstrap icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/shop-detail.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
    integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
    crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"
    integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw=="
    crossorigin="anonymous" />

</head>

<body>
    <!-- Header -->
    <?php
        include("includes/header.php");
    ?>

<?php
  // Validate required GET parameters
  if (!isset($_GET['product_id']) || !isset($_GET['color'])) {
    // missing params: redirect back to shop
    header('Location: shop.php');
    exit;
  }

  $product_id = intval($_GET['product_id']);
  $product_color_id = intval($_GET['color']);

  // Fetch product
  $get_product = "SELECT * FROM products WHERE product_id='$product_id' LIMIT 1";
  $run_product = mysqli_query($conn, $get_product);
  if (!$run_product || mysqli_num_rows($run_product) == 0) {
    // product not found
    header('Location: shop.php');
    exit;
  }
  $row_product = mysqli_fetch_assoc($run_product);
  // safely assign fields with defaults
  $product_name = $row_product['product_name'] ?? 'Sản phẩm';
  $cartegory_id = $row_product['cartegory_id'] ?? 0;
  $product_des = $row_product['product_des'] ?? '';
  $product_screen = $row_product['product_screen'] ?? '';
  $product_os = $row_product['product_os'] ?? '';
  $product_rear_cam = $row_product['product_rear_cam'] ?? '';
  $product_front_cam = $row_product['product_front_cam'] ?? '';
  $product_chip = $row_product['product_chip'] ?? '';
  $product_ram = $row_product['product_ram'] ?? '';
  $product_internal_memory = $row_product['product_internal_memory'] ?? '';
  $product_sim = $row_product['product_sim'] ?? '';
  $product_battery = $row_product['product_battery'] ?? '';

  // Category
  $cartegory_name = 'Danh mục';
  if (!empty($cartegory_id)) {
    $get_cartegory = "SELECT * FROM cartegory WHERE cartegory_id='$cartegory_id' LIMIT 1";
    $run_cartegory = mysqli_query($conn, $get_cartegory);
    if ($run_cartegory && mysqli_num_rows($run_cartegory) > 0) {
      $row_cartegory = mysqli_fetch_assoc($run_cartegory);
      $cartegory_name = $row_cartegory['cartegory_name'] ?? $cartegory_name;
    }
  }

  // Product images and colors
  $get_products_img_main = "SELECT * FROM product_img WHERE product_id = '$product_id' AND product_color_id = '$product_color_id' AND (product_status IS NULL OR product_status != 'Ngừng bán') LIMIT 1";
  $run_products_img_main = mysqli_query($conn, $get_products_img_main);

  // If the main product image is not available (e.g., product was hidden), block access
  if (!$run_products_img_main || mysqli_num_rows($run_products_img_main) == 0) {
    echo "<script>alert('Sản phẩm không tồn tại hoặc đã bị ẩn'); window.location='shop.php';</script>";
    exit;
  }

  // Track recently viewed products (session-based)
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  $rv_key = $product_id . '_' . $product_color_id;
  if (!isset($_SESSION['recently_viewed']) || !is_array($_SESSION['recently_viewed'])) {
    $_SESSION['recently_viewed'] = [];
  }
  // remove existing same entry
  foreach ($_SESSION['recently_viewed'] as $k => $entry) {
    if (isset($entry['key']) && $entry['key'] === $rv_key) {
      unset($_SESSION['recently_viewed'][$k]);
    }
  }
  // prepend new entry
  array_unshift($_SESSION['recently_viewed'], ['key' => $rv_key, 'product_id' => $product_id, 'color_id' => $product_color_id, 'ts' => time()]);
  // keep most recent 10
  $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 10);

  $get_products_img = "SELECT * FROM product_img WHERE product_id = '$product_id' AND (product_status IS NULL OR product_status != 'Ngừng bán')";
  $run_products_img = mysqli_query($conn, $get_products_img);

  $get_color = "SELECT * FROM product_img JOIN product_color ON product_img.product_color_id = product_color.product_color_id WHERE product_id = '$product_id'";
  $run_color = mysqli_query($conn, $get_color);

?>
    <!-- Product-details -->
    <section class="container product-details my-4">
      <div class="row">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="shop.php">Điện thoại</a></li>
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="shop.php?cartegory_id=<?php echo $cartegory_id; ?>">Điện thoại <?php echo $cartegory_name; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product_name; ?></li>
          </ol>
        </nav>
        <div class="product-title">
          <span class="title d-block mb-4"><?php echo $product_name; ?></span>
          <div class="star-rating">
            <ul class="list-inline">
              <li class="list-inline-item"><i class="fas fa-star"></i></li>
              <li class="list-inline-item"><i class="fas fa-star"></i></li>
              <li class="list-inline-item"><i class="fas fa-star"></i></li>
              <li class="list-inline-item"><i class="fas fa-star"></i></li>
              <li class="list-inline-item"><i class="fas fa-star-half-alt"></i></li>
            </ul>
          </div>
        </div>
        <?php
          // show average rating and number of reviews
          // Only show review counts/avg to admin or to the customer who wrote reviews (privacy: reviews are private)
          $reviews_where = "product_id = '$product_id'";
          $show_reviews = false;
          if(isset($_SESSION['admin_id'])){
            $show_reviews = true;
          } else if(isset($_SESSION['customer_id'])){
            $cust = intval($_SESSION['customer_id']);
            $reviews_where = "product_id = '$product_id' AND customer_id = '$cust'";
            $show_reviews = true;
          }
          if($show_reviews){
            $avg_res = mysqli_query($conn, "SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM product_reviews WHERE $reviews_where");
            if($avg_res){
              $avg_row = mysqli_fetch_assoc($avg_res);
              $rev_count = intval($avg_row['cnt']);
              $avg_rating = $avg_row['avg_rating'] ? round($avg_row['avg_rating'],1) : 0;
              if($rev_count > 0){
                echo "<div class='mb-2'><span class='badge bg-warning text-dark' style='font-size:1rem;'>".htmlspecialchars($avg_rating)." ★</span> <small class='text-muted'>(".$rev_count." đánh giá)</small></div>";
              }
            }
          }
        ?>
        <div class="col-lg-4 col-md-12 col-12">
       <?php
         // main image row — ensure query returned a result
         if ($run_products_img_main && mysqli_num_rows($run_products_img_main) > 0) {
           $row_products_img_main = mysqli_fetch_assoc($run_products_img_main);
           $product_price = $row_products_img_main['product_price'] ?? 0;
           $product_price_des = $row_products_img_main['product_price_des'] ?? 0;
           $product_price_format = function_exists('currency_format') ? currency_format($product_price) : number_format($product_price);
           $product_price_des_format = function_exists('currency_format') ? currency_format($product_price_des) : number_format($product_price_des);
           $product_quantity = $row_products_img_main['product_quantity'] ?? 0;
           $product_status = $row_products_img_main['product_status'] ?? '';
           $product_color_img_main = $row_products_img_main['product_color_img'] ?? 'noimage.png';
         } else {
           // fallback defaults when no image record exists
           $product_price = 0;
           $product_price_des = 0;
           $product_price_format = function_exists('currency_format') ? currency_format($product_price) : number_format($product_price);
           $product_price_des_format = function_exists('currency_format') ? currency_format($product_price_des) : number_format($product_price_des);
           $product_quantity = 0;
           $product_status = '';
           $product_color_img_main = 'noimage.png';
         }
         echo "<img class='img-fluid w-100 py-4' src='administrator/product_img/".htmlspecialchars($product_color_img_main, ENT_QUOTES, 'UTF-8')."' id='mainImg' alt=''>";
       ?>

          <div class="small-img-group owl-carousel">
            <?php
               if ($run_products_img && mysqli_num_rows($run_products_img) > 0) {
                 while($row_products_img = mysqli_fetch_assoc($run_products_img)){
                   $product_color_img = $row_products_img['product_color_img'] ?? 'noimage.png';
                   echo "<div class='item-border'><img class='small-img' src='administrator/product_img/".htmlspecialchars($product_color_img, ENT_QUOTES, 'UTF-8')."' onclick='showImg(this.src)' alt=''></div>";
                 }
               } else {
                 echo "<div class='item-border'><img class='small-img' src='administrator/product_img/noimage.png' alt=''></div>";
               }
            ?>
            
          </div>
        </div>
        <div class="col-lg-8 col-md-12 col-12 ps-5 py-4">

          <?php
            add_cart();
        ?>
          <form action="" method="post" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <span class="py-2 fw-bold d-block">Chọn màu</span>
            
              <?php
                // Determine the current selected color
                $color_id = $product_color_id;
                $get_color_cart = "SELECT * FROM product_color WHERE product_color_id = '$color_id' LIMIT 1";
                $run_color_cart = mysqli_query($conn, $get_color_cart);
                $color_cart = '';
                if ($run_color_cart && mysqli_num_rows($run_color_cart) > 0) {
                  $row_color_cart = mysqli_fetch_assoc($run_color_cart);
                  $color_cart = $row_color_cart['product_color_name'] ?? '';
                }

                if ($run_color && mysqli_num_rows($run_color) > 0) {
                    while($row_color = mysqli_fetch_assoc($run_color)){
                        $product_color = $row_color['product_color_name'] ?? '';
                        $get_color_1 = "SELECT * FROM product_color WHERE product_color_name = '".mysqli_real_escape_string($conn,$product_color)."' LIMIT 1";
                        $run_color_1 = mysqli_query($conn, $get_color_1);
                        $product_color_id = null;
                        if ($run_color_1 && mysqli_num_rows($run_color_1) > 0) {
                            $row_color_1 = mysqli_fetch_assoc($run_color_1);
                            $product_color_id = $row_color_1['product_color_id'];
                        }
                        if (!$product_color_id) continue;
                        echo "<a class='btn btn-white border border-warning ";
                        if($product_color_id == $color_id){ echo "bg-warning"; }
                        echo "' href='shop-detail.php?product_id=$product_id&color=$product_color_id'>".htmlspecialchars($product_color, ENT_QUOTES, 'UTF-8')."</a>";
                        echo "<input type='hidden' class='btn-check' name='product_color' id='".htmlspecialchars($product_color_id, ENT_QUOTES, 'UTF-8')."' value='".htmlspecialchars($color_cart, ENT_QUOTES, 'UTF-8')."'>";
                    }
                }
              ?>

            <p class="item-price mt-4"> <b><?php echo $product_price_des_format; ?></b> <strike><?php if($product_price != $product_price_des) echo $product_price_format; ?></strike></p>
            <span class="fw-bold">Mô tả sản phẩm</span>
            <p class="item-des"><?php echo $product_des; ?></p>
            <?php
              if($product_quantity == 0){
            ?>
              <p class="text-danger fw-bold mt-3 fs-5">Đã hết hàng</p>
            <?php
              }
              else if($product_status=="Ngừng bán"){
            ?>
              <p class="text-danger fw-bold mt-3 fs-5">Ngừng kinh doanh</p>
            <?php
              }
              else{
            ?>
              <div class="quantity d-inline-block">
                <span class="dec qtybtn" onclick="decreaseCount(event, this)">-</span>
                <input type="text" value="1" name="quantity">
                <span class="inc qtybtn" onclick="increaseCount(event, this)">+</span>
              </div>
              <button type="submit" class="btn btn-primary btn-buy-now" name="add_to_cart">Mua ngay</button>
            </form>
            <!-- separate form for favorite to avoid submitting add-to-cart data -->
            <form action="" method="post" class="favorite-form">
            <?php
              }
            ?>
            <!-- <span class="wish-icon"><i class="far fa-heart" onclick="changeIcon(this)"></i></span> -->
            <button class="btn btn-danger d-block py-2 mt-3 fw-bold text-uppercase" name="like_product" type="submit">
              <?php
                if(isset($_SESSION['customer_id'])){
                  $customer_id = $_SESSION['customer_id'];
                  $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$color_id'";
                  $run_favorite = mysqli_query($conn, $check_favorte);
                  $count_favorite = mysqli_num_rows($run_favorite);
                  if($count_favorite==0){
                    echo "Thêm vào Yêu thích";
                  }
                  else{
                    echo "Xóa khỏi Yêu thích";
                  }
                }
                else{
                  echo "Thêm vào Yêu thích";
                }
              ?>
            </button>
            <input type="hidden" name="favorite_product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_color_id" value="<?php echo $color_id; ?>">
            </form>
          <span class="fw-bold d-block mt-5 mb-2">Cấu hình </span>
          <table class="table table-striped">
            <tr>
              <td>Màn hình:</td>
              <td><?php echo $product_screen; ?></td>
            </tr>
            <tr>
              <td>Hệ điều hành:</td>
              <td><?php echo $product_os; ?></td>
            </tr>
            <tr>
              <td>Camera sau:</td>
              <td><?php echo $product_rear_cam; ?></td>
            </tr>
            <tr>
              <td>Camera trước:</td>
              <td><?php echo $product_front_cam; ?></td>
            </tr>
            <tr>
              <td>Chip:</td>
              <td><?php echo $product_chip; ?></td>
            </tr>
            <tr>
              <td>RAM:</td>
              <td><?php echo $product_ram; ?></td>
            </tr>
            <tr>
              <td>Bộ nhớ trong:</td>
              <td><?php echo $product_internal_memory; ?></td>
            </tr>
            <tr>
              <td>Sim:</td>
              <td><?php echo $product_sim; ?></td>
            </tr>
            <tr>
              <td>Pin, Sạc</td>
              <td><?php echo $product_battery; ?></td>
            </tr>
          </table>
          <div class="col-12 mt-4">
            <h5>Đánh giá của khách hàng</h5>
            <?php include_once('includes/reviews_list.php'); ?>
          </div>
          <?php include_once('includes/recently_viewed.php'); ?>
        </div>
        <span class="title">Sản phẩm cùng hãng</span>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
          <?php
          // Show products of the same brand (cartegory)
          $shown_ids = array();
          if (!empty($product_id)) $shown_ids[] = (int)$product_id;
          $same_brand_products = array();
          if (!empty($cartegory_id)){
            $get_same_brand = "SELECT * FROM products WHERE cartegory_id = '".mysqli_real_escape_string($conn, $cartegory_id)."' AND product_id != '".mysqli_real_escape_string($conn, $product_id)."' LIMIT 12";
            $run_same_brand = mysqli_query($conn, $get_same_brand);
            if($run_same_brand && mysqli_num_rows($run_same_brand) > 0){
              while($rp = mysqli_fetch_assoc($run_same_brand)){
                $same_brand_products[] = $rp;
                $shown_ids[] = (int)$rp['product_id'];
              }
            }
          }

          if(count($same_brand_products) > 0){
            foreach($same_brand_products as $row_products){
              $rel_pid = $row_products['product_id'];
              $rel_name = $row_products['product_name'];
              $get_products_img = "select * from product_img where product_id = '$rel_pid' AND (product_status IS NULL OR product_status != 'Ngừng bán') LIMIT 0,1";
              $run_products_img = mysqli_query($conn, $get_products_img);
              while($row_products_img = mysqli_fetch_array($run_products_img)){
                $product_price = $row_products_img['product_price'];
                $product_price_des = $row_products_img['product_price_des'];
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
                  $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$rel_pid' and product_color_id = '$product_color_id'";
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
                      <input type='hidden' name='favorite_product_id' value='$rel_pid'>
                      <input type='hidden' name='product_color_id' value='$product_color_id'>
                    </form>
                    <a href='shop-detail.php?product_id=$rel_pid&color=$product_color_id'>
                      <div class='img-box'>
                        <img src='administrator/product_img/$product_color_img' class='img-fluid' alt='iPhone'>
                      </div>
                      <div class='thumb-content'>
                        <h4>$rel_name</h4>
                        <div class='star-rating'>
                          <ul class='list-inline'>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='far fa-star'></i></li>
                          </ul>
                        </div>";
                if($product_price==$product_price_des){
                  echo "<p class='item-price'><strike></strike> <b>$product_price_format</b></p><a href='shop-detail.php?product_id=$rel_pid' class='btn btn-primary needs-login'>Mua ngay</a>";
                } else {
                  echo "<p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p><a href='shop-detail.php?product_id=$rel_pid' class='btn btn-primary needs-login'>Mua ngay</a>";
                }
                echo "</div></a></div></div>\n";
              }
            }
          } else {
            echo "<p class='text-muted'>Không tìm thấy sản phẩm cùng hãng.</p>";
          }
          ?>
        </div>
        
        <span class="title mt-4">Bạn có thể thích</span>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
          <?php
          $exclude_sql = '';
          if(count($shown_ids) > 0){
            $exclude_sql = " WHERE product_id NOT IN (" . implode(',', array_map('intval', $shown_ids)) . ") ";
          }
          $get_other = "SELECT * FROM products " . $exclude_sql . " ORDER BY RAND() LIMIT 12";
          $run_other = mysqli_query($conn, $get_other);
          while($row_products = mysqli_fetch_array($run_other)){
            $product_id_other = $row_products['product_id'];
            $product_name_other = $row_products['product_name'];
            $get_products_img = "select * from product_img where product_id = '$product_id_other' AND (product_status IS NULL OR product_status != 'Ngừng bán') LIMIT 0,1";
            $run_products_img = mysqli_query($conn, $get_products_img);
            while($row_products_img = mysqli_fetch_array($run_products_img)){
              $product_price = $row_products_img['product_price'];
              $product_price_des = $row_products_img['product_price_des'];
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
                $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$product_id_other' and product_color_id = '$product_color_id'";
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
                      <input type='hidden' name='favorite_product_id' value='$product_id_other'>
                      <input type='hidden' name='product_color_id' value='$product_color_id'>
                    </form>
                    <a href='shop-detail.php?product_id=$product_id_other&color=$product_color_id'>
                      <div class='img-box'>
                        <img src='administrator/product_img/$product_color_img' class='img-fluid' alt='iPhone'>
                      </div>
                      <div class='thumb-content'>
                        <h4>$product_name_other</h4>
                        <div class='star-rating'>
                          <ul class='list-inline'>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='fas fa-star'></i></li>
                            <li class='list-inline-item'><i class='far fa-star'></i></li>
                          </ul>
                        </div>";
              if($product_price==$product_price_des){
                echo "<p class='item-price'><strike></strike> <b>$product_price_format</b></p><a href='shop-detail.php?product_id=$product_id_other' class='btn btn-primary needs-login'>Mua ngay</a>";
              }
              else{
                echo "<p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p><a href='shop-detail.php?product_id=$product_id_other' class='btn btn-primary needs-login'>Mua ngay</a>";
              }
              echo "</div></a></div></div>\n";
            }
          }
          ?>
          </div>
      </div>
    </section>


    <!-- footer -->
    <?php
        include("includes/footer.php");
    ?>
  
      <!-- js -->
      <script src="js/index.js"></script>
      <script src="js/shop-detail.js"></script>
      <!--Jquery -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
      <!-- Owl Carousel -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
      <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script> -->
      <!-- custom JS code after importing jquery and owl -->
      <script src="js/owlcarousel.js"></script>
  </body>
  </html>