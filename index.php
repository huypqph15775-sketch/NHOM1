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
        $active="Home";
        include("includes/header.php");
    ?>

    <!-- slider -->
    <section class="slider">
        <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <?php
                $get_slides = "select * from slider LIMIT 1";
                $run_slides = mysqli_query($conn, $get_slides);
                while($row_slides=mysqli_fetch_array($run_slides)){
                  $slide_image = $row_slides['slide_image'];
                  echo "
                  <div class='carousel-item active' data-bs-interval='10000'>
                    <a href='shop.php?cartegory_id=2'><img src='images/slider/$slide_image' class='d-block w-100' alt='OPPO '></a>
                  </div>
                  ";
                }
                $get_slides = "select * from slider LIMIT 1 OFFSET 1";
                $run_slides = mysqli_query($conn, $get_slides);
                while($row_slides=mysqli_fetch_array($run_slides)){
                  $slide_image = $row_slides['slide_image'];
                  echo "
                  <div class='carousel-item' data-bs-interval='10000'>
                    <a href='shop.php?cartegory_id=8'><img src='images/slider/$slide_image' class='d-block w-100' alt='OPPO A95'></a>
                  </div>
                  ";
                }
				        $get_slides = "select * from slider LIMIT 1 OFFSET 2";
                $run_slides = mysqli_query($conn, $get_slides);
                while($row_slides=mysqli_fetch_array($run_slides)){
                  $slide_image = $row_slides['slide_image'];
                  echo "
                  <div class='carousel-item' data-bs-interval='10000'>
                    <a href='shop.php?cartegory_id=7'><img src='images/slider/$slide_image' class='d-block w-100' alt='O A95'></a>
                  </div>
                  ";
                }
                $get_slides = "select * from slider LIMIT 1 OFFSET 3";
                $run_slides = mysqli_query($conn, $get_slides);
                while($row_slides=mysqli_fetch_array($run_slides)){
                  $slide_image = $row_slides['slide_image'];
                  echo "
                  <div class='carousel-item' data-bs-interval='10000'>
                    <a href='shop.php?cartegory_id=5'><img src='images/slider/$slide_image' class='d-block w-100' alt='O A95'></a>
                  </div>
                  ";
                }
              ?>
            </div> 
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
    </section>

    

    <!-- cartegory-product -->
    <section class="cartegory_product">

      <!-- cartegory_sale -->
      <div style="background-color:#0099FF; border-radius: 15px;" class="container-xl mt-4">
        <div style="background-color:#0099FF; border-radius: 15px;" class="row row_cartegory pb-4">
          <div class="col-md-12 col_cartegory_sale">
            <h2><i class="fab fa-gripfire mx-3"></i><b>KHUYẾN MÃI HOT</b></h2>
            <div class="owl-carousel owl-theme">
              <?php
                $get_products = "select * from products order by 1 DESC LIMIT 0,20";
                $run_products = mysqli_query($conn, $get_products);
                while($row_products = mysqli_fetch_array($run_products)){
                    $product_id = $row_products['product_id'];
                    $product_name = $row_products['product_name'];
                    $get_products_img = "select * from product_img where product_id = '$product_id' and product_price > product_price_des LIMIT 0,1";
                    $run_products_img = mysqli_query($conn, $get_products_img);
                    while($row_products_img = mysqli_fetch_array($run_products_img)){
                        $product_price = $row_products_img['product_price'];
                        $product_price_des = $row_products_img['product_price_des'];
                        $product_price_format = currency_format($row_products_img['product_price']);
                        $product_price_des_format = currency_format($row_products_img['product_price_des']);
                        $product_color_id = $row_products_img['product_color_id'];
                        $product_color_img = $row_products_img['product_color_img'];
                        echo "
                        <div class='item item-sale'>
                        
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
                                <img src='administrator/product_img/$product_color_img' class='img-fluid' alt=''>									
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
                                </div>
                                <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                                <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary'>Mua ngay</a>
                              </div>
                            </a>						
                          </div>

                        </div>
                        ";
                    }
                }
              ?>
              
            </div>
          </div>
        </div>
      </div>

      <!-- banner sale -->
      <div style="background-color:#F7C14A; border-radius: 15px;" class="container-xl mt-4 banner-sale">
        <span style="font-size: 30px;" class="title d-block mb-4">CHUỖI MỚI SALE KHỦNG</span>
            <div style="padding-bottom: 25px; margin-top: -22px" class="owl-carousel owl-theme owl-carousel-banner">
              <div class="item item-1">
                <a href="">
                  <img src="images/banner1.png" alt="">
                </a>
              </div>
              <div class="item item-banner">
                <a href="">
                  <img src="images/banner2.png" alt="">
                </a>
              </div>
              <div class="item item-banner">
                <a href="">
                  <img src="images/banner3.png" alt="">
                </a>
              </div>
              <div class="item item-banner">
                <a href="">
                  <img src="images/banner4.png" alt="">
                </a>
              </div>
              <div class="item item-banner">
                <a href="">
                  <img src="images/banner5.png" alt="">
                </a>
              </div>
              <div class="item item-banner">
                <a href="">
                  <img src="images/banner6.png" alt="">
                </a>
              </div>
            </div>
      </div>

      <!-- feature product -->
      <div class="container-xl mt-4 featured-product">
        <span style="font-size: 30px" class="title d-block mb-4">ĐIỆN THOẠI MỚI NHẤT</span>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
            <?php
              getPro();
            ?>
        </div>
      </div>
    </div>

    </section>

    <!-- footer -->
    <?php
        include("includes/footer.php");
    ?>

    <!-- js -->
    <script src="js/index.js"></script>

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