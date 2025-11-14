<?php
session_start();
include("includes/database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - SmartPhone Store</title>
    <!-- favicon -->
    <link rel="icon" href="images/smartphone.png">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>
<body style="background-color: #ccc">
    <div class="container">
        <div class="row pt-5">
            <div class="col-xl-6 col-lg-7 col-md-8 col-12 mt-5">
                <img style="margin-left: 220px; border-radius: 10px" src="images/smartphone.png" width="348.5px" alt="">
            </div>
            <div class="col-xl-3 col-lg-5 col-md-8 col-10 mt-5">
                <form action="" method="post"> 
                    <div style="border-radius: 10px" class="card shadow">
                        <img class="m-auto my-4" src="images/smartphone.png" width="100px" alt="...">
                        <div class="mb-3 px-3">
                            <input type="text" class="form-control py-2" name="admin_email_user_name" id="email_user_name" placeholder="Tên tài khoản hoặc email">
                        </div>
                        <div class=" mb-3 px-3">
                            <input type="password" class="form-control py-2" name="admin_password" id="password" placeholder="Mật khẩu">
                        </div>
                        <div class=" mb-5 px-3">
                            <input type="submit" name="signin" class="form-control btn btn-primary" value="Đăng nhập">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
if(isset($_POST['signin'])){
  $admin_email_user_name = $_POST['admin_email_user_name'];
  $admin_password = $_POST['admin_password'];
  $select_admin = "select * from admin where (admin_email = '$admin_email_user_name' or admin_user_name = '$admin_email_user_name') and admin_password='$admin_password'";
  $run_admin = mysqli_query($conn, $select_admin);
  $check_admin = mysqli_num_rows($run_admin);
  if($check_admin == 0){
    echo "<script>alert('Tên tài khoản hoặc mật khẩu không đúng!')</script>";
    exit();
  }
  else{
    $row_admin = mysqli_fetch_array($run_admin);
    $admin_id = $row_admin['admin_id'];
    $admin_name = $row_admin['admin_name'];
    $admin_level = $row_admin['admin_level'];
    $_SESSION['admin_name'] = $admin_name;
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_level'] = $admin_level;
    echo "<script>alert('Đăng nhập thành công')</script>";
    echo "<script>window.open('index.php?dashboard', '_self')</script>";
  }
}
?>