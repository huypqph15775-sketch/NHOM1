<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPhone Store</title>
    <!-- favicon -->
    <link rel="icon" href="../images/smartphone.png">
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
    <link rel="stylesheet" href="css/myaccount.css">

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
        if(isset($_SESSION['customer_id'])){
            $customer_id = $_SESSION['customer_id'];
            $get_customer = "select * from customer where customer_id = '$customer_id'";
            $run_customer = mysqli_query($conn, $get_customer);
            $row_customer = mysqli_fetch_array($run_customer);
            $customer_name = $row_customer['customer_name'];
            $customer_sex = $row_customer['customer_sex'];
            $customer_user_name = $row_customer['customer_user_name'];
            $customer_email = $row_customer['customer_email'];
            $customer_phone = $row_customer['customer_phone'];
            $customer_address = $row_customer['customer_address'];
            $customer_img = $row_customer['customer_img'];
        }
    ?>
    <!-- section myacount -->
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a class="text-decoration-none" href="../index.php">Trang chủ</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
                    </ol>
                  </nav>
                <div class="row d-flex align-items-start">
                    <div class="col-lg-3 col-md-4 col-12 pb-4 left-container">
                        <div class="img-account mb-4">
                            <img src="<?php if($customer_img == ''){echo "images/noavatar.webp";} else{echo "customer_img/".$customer_img."";}?>" alt="" class="p-4">
                            <form action="" method="post" enctype="multipart/form-data"> 
                                <div class="input-group">
                                    <input type="file" name="update_img" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                    <button name="update_img_btn" class="btn btn-outline-primary" id="inputGroupFileAddon04">Thay ảnh</button>
                                </div>
                            </form>
                        </div>
                        <?php
                            if(isset($_POST['update_img_btn'])){
                                $update_img = $_FILES['update_img']['name'];
                                $update_img_tmp = $_FILES['update_img']['tmp_name'];
                                move_uploaded_file($update_img_tmp, "customer_img/$update_img");
                                $update_customer_img = "update customer set customer_img = '$update_img' where customer_id='$customer_id'";
                                $run_update_img = mysqli_query($conn, $update_customer_img);
                                if($run_update_img){
                                    echo "<script>alert('Thay ảnh đại diện thành công')</script>";
                                    echo "<script>window.open('myaccount.php?profile', '_self')</script>";
                                }
                            }
                        ?>
                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button onclick="location.href='myaccount.php?profile'" class="nav-link <?php if(isset($_GET['profile'])){echo "active";} ?>" id="v-pills-profile-tab" data-bs-toggle="pill">
                                <i class="fas fa-user-circle pe-3"></i>Thông tin cá nhân
                            </button>
                            <button onclick="location.href='myaccount.php?edit_account'" class="nav-link <?php if(isset($_GET['edit_account'])){echo "active";} ?>" name="edit_account" id="v-pills-editprofile-tab" data-bs-toggle="pill">
                                <i class="fas fa-edit pe-3"></i>Chỉnh sửa thông tin cá nhân
                            </button>
                            <button onclick="location.href='myaccount.php?change_password'" class="nav-link <?php if(isset($_GET['change_password'])){echo "active";} ?>" id="v-pills-editpw-tab" data-bs-toggle="pill">
                                <i class="fas fa-key pe-3"></i>Đổi mật khẩu
                            </button>
                            <button onclick="location.href='../signout.php'" class="nav-link" id="v-pills-signup-tab" data-bs-toggle="pill">
                                <i class="fas fa-sign-out-alt pe-3"></i>Đăng xuất
                            </button>
                          </div>
                    </div>
                    
                    <div class="col-lg-9 col-md-8 col-12 px-4">
                        <div class="tab-content" id="v-pills-tabContent">
                        <?php
                        if(isset($_GET['profile'])){
                            include("profile.php");
                        }
                        if(isset($_GET['edit_account'])){
                            include("edit_account.php");
                        }
                        if(isset($_GET['change_password'])){
                            include("change_password.php");
                        }
                        ?>
                    </div>
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