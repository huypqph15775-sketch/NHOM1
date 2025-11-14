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
    <link rel="stylesheet" href="css/about.css">
    
</head>
<body>
	<!-- Header -->
        <?php
            $active="About";
            include("includes/header.php");
        ?>
	<!-- section contact -->
<section class="about-section">
        <div  class="container">
            <div style="border: double 1px" class="card bg-white my-4 p-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                            <div class="img-head">
                                <img style="width: 400px; height: 400px" src="images/smartphone.png" alt="about us">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h2 style="color: #31c27c;" class="text-head">
                                VỀ SMARTPHONE STORE
                            </h2>
							
                            <div style="" class="break-small mb-2"></div>
                            <p class="text-about">
								<div style="display:inline">
									<span style="font-size: 30px">SmartPhone Store</span>  - nơi bạn có thể khám phá và trải nghiệm những công nghệ di động tuyệt vời nhất! <br>

Với SmartPhone Store, bạn có thể dễ dàng tìm thấy điện thoại di động phù hợp với nhu cầu và phong cách của bạn. Chúng tôi cung cấp các loại điện thoại thông minh từ những thương hiệu hàng đầu như Apple, Samsung, Xiaomi, Oppo, và nhiều hãng khác. Bất kể bạn đang tìm kiếm một chiếc điện thoại cao cấp, một thiết bị giá trị với hiệu suất mạnh mẽ hoặc một lựa chọn tiết kiệm, chúng tôi sẽ đáp ứng mọi yêu cầu của bạn.<br>

Hãy ghé thăm SmartPhone Store ngay hôm nay và khám phá thế giới di động tuyệt vời đang chờ đợi bạn!
							</div>
                                
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	</div>
	<!-- footer -->
    <?php
        include("includes/footer.php");
    ?>

      <!-- js -->
    <script src="js/index.js"></script>
</body>
</html>