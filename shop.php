<?php
include("includes/database.php");
?>

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
    <link rel="stylesheet" href="css/shop.css">

</head>
<body>
        <!-- Header -->
       <?php
            $active="Shop";
            include("includes/header.php");
       ?>

    <!-- section list product -->
    <section class="mt-2">
        <div class="container">
           <?php
            if(isset($_GET['cartegory_id'])){
              $cartegory_id = $_GET['cartegory_id'];
              $get_cartegory = "select * from cartegory where cartegory_id='$cartegory_id'";
              $run_cartegory = mysqli_query($conn, $get_cartegory);
              $row_cartegory = mysqli_fetch_array($run_cartegory);
              $cartegory_name = $row_cartegory['cartegory_name'];
              echo "
              <div aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                  <li class='breadcrumb-item'><a class='text-primary' href='index.php'>Trang chủ</a></li>
                  <li class='breadcrumb-item'><a class='text-primary' href='shop.php'>Điện thoại</a></li>
                  <li class='breadcrumb-item active text-secondary' aria-current='page'>Điện thoại $cartegory_name</li>
                </ol>
              </div>
              ";
            }
            else{
              echo "
              <div aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                  <li class='breadcrumb-item'><a class='text-primary' href='index.php'>Trang chủ</a></li>
                  <li class='breadcrumb-item active text-secondary' aria-current='page'>Điện thoại</li>
                </ol>
              </div>
              ";
            }
            ?>
            <div class="q-menu mb-2">
              <?php
                getCartegory();
              ?>
            </div>
            <div class="row mb-4">
            <?php
                getCatProCount();
            ?>
            <?php
            if(!isset($_GET['cartegory_id'])){
              $get_products_all = "select * from products";
              $run_products_all = mysqli_query($conn, $get_products_all);
              $count = mysqli_num_rows($run_products_all);
              echo "
              <h6 class='fw-bold mb-3'>$count điện thoại</h6>
              ";
            }
            ?>

            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
                <?php

                if(!isset($_GET['cartegory_id'])){

                  $per_page = 20;

                  if(isset($_GET['page'])){
                    $current_page = $_GET['page'];
                  }
                  else{
                    $current_page = 1;
                  }
                    $start_from = ($current_page-1)*$per_page;
                    $get_products = "select * from products order by 1 DESC LIMIT $start_from, $per_page";
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
                        $product_color_img = $row_products_img['product_color_img'];
                        $product_color_id = $row_products_img['product_color_id'];
                        
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
                                  <p class='item-price'><strike></strike> <b>$product_price_des_format</b></p>
                                  <a href='shop-detail.php?product_id=".$product_id."&color=$product_color_id' class='btn btn-primary'>Mua ngay</a>
                                </div>
                            </a>						
                            </div>

                          </div>
                          ";
                        }else{
                          echo "
                                  <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                                  <a href='shop-detail.php?product_id=".$product_id."&color=".$product_color_id."' class='btn btn-primary'>Mua ngay</a>
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
              <nav aria-label="..." class="mt-4">
                <ul class="pagination justify-content-center">
                <?php
                  $query = "select * from products";
                  $result = mysqli_query($conn, $query);
                  $total_records = mysqli_num_rows($result);
                  $total_pages = ceil($total_records / $per_page);

                  if ($current_page > $total_pages){
                    $current_page = $total_pages;
                  }
                  else if ($current_page < 1){
                      $current_page = 1;
                  }

                  if ($current_page > 1 && $total_pages > 1){
                    echo "
                      <li class='page-item'>
                      <a class='page-link' href='shop.php?page=".($current_page-1)."' aria-label='Previous'>
                        <span aria-hidden='true'>&laquo;</span>
                      </a>
                      </li>
                    ";
                  }
                  
                  for($i=1; $i<=$total_pages; $i++){
                    if ($i == $current_page){
                      echo "
                      </li>
                      <li class='page-item'>
                        <a class='page-link'>".$i."</a>
                      </li>
                      ";
                    }
                    else{
                      echo "
                      </li>
                      <li class='page-item'>
                        <a class='page-link' href='shop.php?page=".$i."'>".$i."</a>
                      </li>
                      ";
                    }
                    
                  };
                  if ($current_page < $total_pages && $total_pages > 1){
                    echo "
                    <li class='page-item'>
                      <a class='page-link' href='shop.php?page=".($current_page+1)."' aria-label='Next'>
                        <span aria-hidden='true'>&raquo;</span>
                      </a>
                    </li>
                    ";
                  }
                  
                  // <li class='page-item active' aria-current='page'>
                  //   <span class='page-link'>2</span>
                  // </li>
                  // <li class='page-item'><a class='page-link' href='#'>3</a></li>
                  
                  
                  
                }
                ?>
                </ul>
              </nav>
              
              <?php
                getCatPro();
              ?>
              
        </div>
    </section>

        <!-- footer -->
        <?php
            include("includes/footer.php");
        ?>

          <!-- js -->
    <script src="js/index.js"></script>
</body>
</html>