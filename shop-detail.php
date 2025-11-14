<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store</title>
    <!-- favicon -->
    <link rel="icon" href="images/smartphone.png">
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
  if(isset($_GET['product_id'])&&isset($_GET['color'])){
    $product_id = $_GET['product_id'];
    $product_color_id = $_GET['color'];
    $get_product = "select * from products where product_id='$product_id'";
    $run_product = mysqli_query($conn, $get_product);
    $row_product = mysqli_fetch_array($run_product);
    $product_name = $row_product['product_name'];
    $cartegory_id = $row_product['cartegory_id'];
    $product_des = $row_product['product_des'];
    $product_screen = $row_product['product_screen'];
    $product_os = $row_product['product_os'];
    $product_rear_cam = $row_product['product_rear_cam'];
    $product_front_cam = $row_product['product_front_cam'];
    $product_chip = $row_product['product_chip'];
    $product_ram = $row_product['product_ram'];
    $product_internal_memory = $row_product['product_internal_memory'];
    $product_sim = $row_product['product_sim'];
    $product_battery = $row_product['product_battery'];
    
    $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
    $run_cartegory = mysqli_query($conn, $get_cartegory);
    $row_cartegory = mysqli_fetch_array($run_cartegory);
    $cartegory_name = $row_cartegory['cartegory_name'];

    $get_products_img_main = "select * from product_img where product_id = '$product_id' and product_color_id = '$product_color_id'";
    $run_products_img_main = mysqli_query($conn, $get_products_img_main);

    $get_products_img = "select * from product_img where product_id = '$product_id'";
    $run_products_img = mysqli_query($conn, $get_products_img);

    $get_color = "select * from product_img join product_color on product_img.product_color_id = product_color.product_color_id where product_id = '$product_id'";
    $run_color = mysqli_query($conn, $get_color);

  }
?>
    <!-- Product-details -->
    <section class="container product-details my-4">
      <div class="row">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
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
        <div class="col-lg-4 col-md-12 col-12">
          <?php
             $row_products_img_main = mysqli_fetch_array($run_products_img_main);
                $product_price = $row_products_img_main['product_price'];
                $product_price_des = $row_products_img_main['product_price_des'];
                $product_price_format = currency_format($row_products_img_main['product_price']);
                $product_price_des_format = currency_format($row_products_img_main['product_price_des']);
                $product_quantity = $row_products_img_main['product_quantity'];
                $product_status = $row_products_img_main['product_status'];
                 $product_color_img_main = $row_products_img_main['product_color_img'];
                 echo "
                 <img class='img-fluid w-100 py-4' src='administrator/product_img/$product_color_img_main' id='mainImg' alt=''>
                 ";
          ?>

          <div class="small-img-group owl-carousel">
            <?php
               while($row_products_img = mysqli_fetch_array($run_products_img)){
                   $product_color_img = $row_products_img['product_color_img'];
                   echo "
                   <div class='item-border'>
                    <img class='small-img' src='administrator/product_img/$product_color_img' onclick='showImg(this.src)' alt=''>
                  </div>
                   ";
               }
            ?>
            
          </div>
        </div>
        <div class="col-lg-8 col-md-12 col-12 ps-5 py-4">

          <?php
            add_cart();
        ?>
          <form action="" method="post">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <span class="py-2 fw-bold d-block">Chọn màu</span>
            
              <?php
                $color_id = $_GET['color'];
                $get_color_cart = "select * from product_color where product_color_id = '$color_id'";
                $run_color_cart = mysqli_query($conn, $get_color_cart);
                $row_color_cart = mysqli_fetch_array($run_color_cart);
                $color_cart = $row_color_cart['product_color_name'];
                while($row_color = mysqli_fetch_array($run_color)){
                    $product_color = $row_color['product_color_name'];
                    $get_color_1 = "select * from product_color where product_color_name = '$product_color'";
                    $run_color_1 = mysqli_query($conn, $get_color_1);
                    $row_color_1 = mysqli_fetch_array($run_color_1);
                    $product_color_id = $row_color_1['product_color_id'];
                    echo "
                    <a class='btn btn-white border border-warning ";
                    if($product_color_id == $_GET['color']){echo "bg-warning";}
                    echo "' href='shop-detail.php?product_id=$product_id&color=$product_color_id'>$product_color</a>
                    <input type='hidden' class='btn-check' name='product_color' id='".$_GET['color']."' value='$color_cart'>
                    
                    ";
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
            <?php
              }
            ?>
            <!-- <span class="wish-icon"><i class="far fa-heart" onclick="changeIcon(this)"></i></span> -->
            <button class="btn btn-danger d-block py-2 mt-3 fw-bold text-uppercase" name="like_product">
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
        </div>
        <span class="title">Xem thêm điện thoại khác</span>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
          <?php
          
          $get_products = "select * from products order by rand() LIMIT 10";
          $run_products = mysqli_query($conn, $get_products);
          while($row_products = mysqli_fetch_array($run_products)){
            $product_id = $row_products['product_id'];
            $product_name = $row_products['product_name'];
            $get_products_img = "select * from product_img where product_id = '$product_id' LIMIT 0,1";
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
              if($product_price==$product_price_des){
                echo "
                        <p class='item-price'><strike></strike> <b>$product_price_format</b></p>
                        <a href='shop-detail.php?product_id=".$product_id."' class='btn btn-primary'>Mua ngay</a>
                      </div>
                  </a>						
                  </div>

                </div>
                ";
              }  
              else{
                echo "
                        <p class='item-price'><strike>$product_price_des_format</strike> <b>$product_price_format</b></p>
                        <a href='shop-detail.php?product_id=".$product_id."' class='btn btn-primary'>Mua ngay</a>
                      </div>
                  </a>						
                  </div>

                </div>
                ";
              }
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