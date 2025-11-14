<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store |  Về SmartPhone Store</title>
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
    <link rel="stylesheet" href="css/about.css">
    
</head>
<body>
        <!-- Header -->
        <?php
            include("includes/header.php");
        ?>

    <!-- section contact -->
    <section class="about-section">
        <div class="container mt-3">
            <?php
                if(isset($_GET['key'])){
                    $key = $_GET['key'];
                    if(!empty($key)){
                    $search = "select * from products where LOWER(product_name) LIKE LOWER('%$key%') or LOWER(product_ram)=LOWER('$key') or LOWER(product_internal_memory)=LOWER('$key')";
                    $run_search = mysqli_query($conn, $search);
                    $count_search = mysqli_num_rows($run_search);
                    if($count_search==0){
            ?>
                        <p class="fs-5 p-3">Rất tiếc, smartphonestore.com không tìm thấy kết quả phù hợp với từ khóa <b>"<?= $key; ?>"</b></p>
                        <div class="row justify-content-end mt-5">
                            <div class="col-lg-8 col-12">
                                <p class="fw-bold">Để tìm được kết quả chính xác hơn, bạn vui lòng: </p>
                                <p> 
                                    <ul>
                                        <li>Kiểm tra lỗi chính tả của từ khóa đã nhập</li>
                                        <li>Thử lại bằng từ khóa khác</li>
                                        <li>Thử lại bằng những từ khóa tổng quát hơn</li>
                                        <li>Thử lại bằng những từ khóa ngắn gọn hơn</li>
                                    </ul>
                                </p>
                            </div>
                        </div>

            <?php
                    }
                    else{
            ?>
            <p class = 'fw-bold p-2'>Tìm thấy <?= $count_search ?> điện thoại</p>
            <div class="container-xl mt-4 featured-product">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3">
                
            <?php
            
            while($row_products = mysqli_fetch_array($run_search)){
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
                        echo"
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
                            <a class='text-decoration-none' href='shop-detail.php?product_id=$product_id&color=$product_color_id'>
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
                                <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary'>Mua ngay</a>
                                </div>
                            </a>						
                            </div>

                        </div>
                        ";
                        }
                        else{
                        echo "
                                <p class='item-price'><strike>$product_price_format</strike> <b>$product_price_des_format</b></p>
                                <a href='shop-detail.php?product_id=$product_id&color=$product_color_id' class='btn btn-primary'>Mua ngay</a>
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
</body>
</html>