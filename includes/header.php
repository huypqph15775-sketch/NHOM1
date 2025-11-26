<link rel="stylesheet" href="index.css">

<?php
  date_default_timezone_set("Asia/Ho_Chi_Minh");  
  include ("includes/database.php");
  include ("functions/functions.php");
?>
<?php

if(isset($_SESSION['customer_id'])){
  $customer_id = $_SESSION['customer_id'];
  $get_account_status = "select * from customer where customer_id = '$customer_id'";
  $run_account_status = mysqli_query($conn, $get_account_status);
  $row_account_status = mysqli_fetch_array($run_account_status);
  $account_status = $row_account_status['account_status'];
  if($account_status == "Locked"){
      echo "<script>alert('Tài khoản đã bị khóa do vi phạm chính sách của cửa hàng')</script>";
      echo "<script>window.open('signout.php', '_self')</script>";
  }
}
?>


<!-- Header -->
<header>
        <nav class="navbar navbar-expand-lg navbar-light"  style="background-color:#ffffff;">
            <div class="container">
              <a class="navbar-brand" href="index.php">
                  <img src="images/phone.png" alt="logo">
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Home") echo "link_active"; ?>" aria-current="page" href="index.php">Trang chủ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Shop") echo "link_active"; ?>" href="shop.php">Điện thoại</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Contact") echo "link_active"; ?>" href="contact.php">Liên hệ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="About") echo "link_active"; ?>" href="about.php">About Us</a>
                  </li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 action-menu">
                    <form action="" method="get" class="search-form" >
                        <input class="form-control me-2" style="width: 262px" name="key" type="search" placeholder="Bạn tìm gì..." aria-label="Search" value="<?php if(isset($_GET['key'])){$key=$_GET['key']; echo "$key";}; ?>">
                        <button class="btn btn-outline-success search-btn" name="search" style="background-color: #0099FF">
                            <i class="fas fa-search"></i>
                        </button>
                      </form>
                    <?php
                      if(isset($_GET['search'])){
                        $key = $_GET['key'];
                        if(empty($key)){
                          echo "<script>alert('Vui lòng nhập dữ liệu tìm kiếm')</script>";
                        }
                        else{
                          echo "<script>window.open('search.php?key=$key', '_self')</script>";
                        }
                      }
                    ?>
                    <li class="nav-item">
                      <a class="nav-link" href="#offcanvasRight" data-bs-toggle="offcanvas" aria-controls="offcanvasRight">
                          <i class="fas fa-heart" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Yêu thích"></i>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="customer/my_orders.php?pending_orders">
                          <i class="fas fa-clipboard-list position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Lịch sử đơn hàng">
                            <?php
                                if(isset($_SESSION['customer_id'])){
                                  $customer_id = $_SESSION['customer_id'];
                                  $select_count_order = "select * from customer_orders where customer_id = '$customer_id'";
                                  $run_select_count = mysqli_query($conn, $select_count_order);
                                  $count_order = mysqli_num_rows($run_select_count);
                                  if($count_order!=0){
                                    echo "
                                    <span class='position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle'>
                                      <span class='visually-hidden'>New alerts</span>
                                    </span>
                                    "; 
                                  }
                                }  
                                    
                            ?>
                          </i>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="customer/cart.php">
                          <i class="fas fa-shopping-cart position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Giỏ hàng">
                            <?php
                                    if(items()!=0){
                                      $items = items();
                                      echo "
                                      <span style='font-size: 12px;' class='position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger'>
                                        $items
                                      </span>
                                      ";
                                    } 
                              ?>
                          </i>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" >
                            <?php
                              if(!isset($_SESSION['customer_id'])){
                                echo "<i class='fas fa-user' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Tài khoản'></i>";
                              }
                              else{
                                echo "<p class='d-inline fw-bold' style='font-size:16px' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Tài khoản'>".$_SESSION['customer_name']."</p>";
                              }
                            ?>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end border-1 shadow-sm" aria-labelledby="navbarDropdown">
                        <?php
                          if(!isset($_SESSION['customer_id'])){
                            echo "
                            <li><a class='dropdown-item' href='signin.php'>Đăng nhập</a></li>
                            <li><a class='dropdown-item' href='signin.php'>Đăng ký</a></li>
                            ";
                          }
                          else{
                            echo "
                            <li><a class='dropdown-item' href='customer/myaccount.php?profile'>Tài khoản</a></li>
                            <li><a class='dropdown-item' href='signout.php'>Đăng xuất</a></li>
                            ";
                          }
                        ?>
                      </ul>
                    </li>
                  </ul>
              </div>
            </div>
          </nav>
    </header>
    <!-- offcanvas for like -->
    
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
      <div class="offcanvas-header">
        <h5 class="text-danger fw-bold" id="offcanvasRightLabel">Sản phẩm yêu thích</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
      <?php
      if(isset($_SESSION['customer_id'])){
        $customer_id = $_SESSION['customer_id'];
        $get_favorite_products = "select * from favorite_product where customer_id = '$customer_id'";
        $run_get_favorite = mysqli_query($conn, $get_favorite_products);
        $count_favorite = mysqli_num_rows($run_get_favorite);
        if($count_favorite==0){
      ?>
          <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center">
                    <i class="bi bi-bag-x-fill text-danger" style="font-size: 80px"></i>
                </div>
                <p class="text-center">Không có điện thoại yêu thích</p>
            </div>
        </div>
      <?php
        }
        else{
          while($row_favorite = mysqli_fetch_array($run_get_favorite)){
            $product_id = $row_favorite['product_id'];
            $product_color_id = $row_favorite['product_color_id'];
            $select_color = "select * from product_color where product_color_id = '$product_color_id'";
            $run_select_color = mysqli_query($conn, $select_color);
            $row_select_color = mysqli_fetch_array($run_select_color);
            $product_color_name = $row_select_color['product_color_name'];
            $select_product = "select * from products where product_id = '$product_id'";
            $run_select_product = mysqli_query($conn, $select_product);
            $row_select_product = mysqli_fetch_array($run_select_product);
            $product_name = $row_select_product['product_name'];
            $select_product_img = "select * from product_img where product_id = '$product_id' and product_color_id = '$product_color_id'";
            $run_select_product_img = mysqli_query($conn, $select_product_img);
            $row_select_product_img = mysqli_fetch_array($run_select_product_img);
            $product_price = $row_select_product_img['product_price'];
            $product_price_des = $row_select_product_img['product_price_des'];
            $product_price_format = currency_format($row_select_product_img['product_price']);
            $product_price_des_format = currency_format($row_select_product_img['product_price_des']);
            $product_color_img = $row_select_product_img['product_color_img'];
      ?>
          <div class="row mb-2">
            <div class="col-3">
            <a href="shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank"><img src="administrator/product_img/<?= $product_color_img;?>" class="img-fluid" style="object-fit: contain !importaint;" alt=""></a>
            </div>
            <div class="col-7">
              <a href="shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank" class="fw-bold text-decoration-none">
                <?php echo $product_name; ?>
              </a>
              <span class="d-block my-2">Màu: <?= $product_color_name ?></span>
              <p class="like-item-price"><strike><?= $product_price_format; ?></strike> <b><?= $product_price_des_format; ?></b></p>
            </div>
            <div class="col-2">
              <form action="" method="post">
                <input type="hidden" name="customer_id" value="<?= $customer_id; ?>">
                <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                <input type="hidden" name="product_color_id" value="<?= $product_color_id; ?>">
                <button onclick="delete_favorite(<?= $product_id; ?>, <?= $product_color_id; ?>)" id="delete_favorite<?=$product_id;?><?=$product_color_id;?>" class="btn btn-white text-danger" style="border: none; padding: 0"><i class="fas fa-times me-1"></i>Xóa</button>
              </form>
            </div>
          </div>
      <?php
          }
        }
      }
      else{
      ?>
          <div class="row text-center justify-content-center">
            <p>Bạn cần đăng nhập để xem</p>
            <div class="col-4">
              <a href="signin.php" class="btn btn-primary text-white">Đăng nhập</a>
            </div>
          </div>
      <?php
        }
      ?>
      </div>
    </div>
    <script>
      function delete_favorite(id1, id2){
          var result = confirm("Bạn chắc chắn muốn xóa sản phẩm này khỏi Yêu thích? ");
          if(result==true){
            document.getElementById("delete_favorite"+id1+id2).name = 'delete_favorite';
          }
      }
  </script>

    <?php
      if(isset($_POST['like_product'])){
        if(isset($_SESSION['customer_id'])){
          $customer_id = $_SESSION['customer_id'];
          $product_id = $_POST['favorite_product_id'];
          $product_color_id = $_POST['product_color_id'];
          $path = $_SERVER['SCRIPT_NAME'];
          $queryString = $_SERVER['QUERY_STRING'];
          $check_favorte = "select * from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$product_color_id'";
          $run_check_favorite = mysqli_query($conn, $check_favorte);
          $count_check_favorite = mysqli_num_rows($run_check_favorite);
          if($count_check_favorite==0){
            $insert_favorite_product = "insert into favorite_product(customer_id, product_id, product_color_id) values ('$customer_id', '$product_id', '$product_color_id')";
            $run_favorite_product = mysqli_query($conn, $insert_favorite_product);
            if($run_favorite_product){
              echo "<script>alert('Một sản phẩm vừa được thêm vào Yêu thích')</script>";
              echo "<script>window.open('$path?$queryString','_self')</script>";
            }
          }
          else{
            $delete_favorite_product = "delete from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$product_color_id'";
            $run_delete_favorite_product = mysqli_query($conn, $delete_favorite_product);
            if($run_delete_favorite_product){
              echo "<script>alert('Đã xóa sản phẩm khỏi Yêu thích')</script>";
              echo "<script>window.open('$path?$queryString','_self')</script>";
            }
          }
        }
        else{
          echo "<script>alert('Bạn cần đăng nhập để thực hiện chức năng này')</script>";
        }http://localhost:8080/Nhom10/index.php
      }

      if(isset($_POST['delete_favorite'])){
        $customer_id = $_POST['customer_id'];
        $product_id = $_POST['product_id'];
        $product_color_id = $_POST['product_color_id'];
        $path = $_SERVER['SCRIPT_NAME'];
        $queryString = $_SERVER['QUERY_STRING'];
        $delete_favorite_product = "delete from favorite_product where customer_id = '$customer_id' and product_id = '$product_id' and product_color_id = '$product_color_id'";
        $run_delete_favorite = mysqli_query($conn, $delete_favorite_product);
        if($run_delete_favorite){
          echo "<script>alert('Đã xóa sản phẩm khỏi Yêu thích')</script>";
          echo "<script>window.open('$path?$queryString','_self')</script>";
        }
      }
    ?>
    <!-- Styled logout confirmation modal -->
    <style>
      /* Logout modal custom styles */
      #logoutConfirmModal .modal-dialog { max-width: 420px; }
      #logoutConfirmModal .modal-content {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: none;
      }
      #logoutConfirmModal .modal-header{
        background: linear-gradient(90deg, #ff6b6b 0%, #d63031 100%);
        color: white;
        border-bottom: none;
        padding: 18px 20px;
      }
      #logoutConfirmModal .modal-title{ font-weight:700; letter-spacing:0.2px; }
      #logoutConfirmModal .modal-body{ padding:22px 20px; color:#333; font-size:15px; text-align:center; background: #fff; }
      #logoutConfirmModal .modal-footer{ border-top: none; justify-content:center; gap:12px; padding:16px 20px; background: #fff; }
      #logoutConfirmModal .btn{ min-width:110px; border-radius:10px; padding:10px 14px; font-weight:600; transition: transform .12s ease, box-shadow .12s ease, background .12s ease, filter .12s ease; }
      #logoutConfirmModal .btn-secondary{ background:#f2f2f2; color:#333; border:none; }
      #logoutConfirmModal .btn-secondary:hover, #logoutConfirmModal .btn-secondary:focus{ background:#e9e9e9; transform: translateY(-3px); box-shadow: 0 8px 18px rgba(0,0,0,0.06); }
      #logoutConfirmModal .btn-danger{ background: linear-gradient(90deg,#ff6b6b,#d63031); border:none; color:#fff; box-shadow:0 8px 20px rgba(214,48,49,0.18); }
      #logoutConfirmModal .btn-danger:hover, #logoutConfirmModal .btn-danger:focus{ filter: brightness(.94); transform: translateY(-3px); box-shadow:0 14px 30px rgba(214,48,49,0.22); }
      /* subtle pop animation for modal */
      .modal.fade .modal-dialog { transform: translateY(-6px) scale(.995); transition: transform .18s ease-out, opacity .18s ease-out; }
      .modal.show .modal-dialog { transform: translateY(0) scale(1); }
      @media (max-width:420px){ #logoutConfirmModal .modal-dialog{ margin: 12px; } }
    </style>

    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logoutConfirmLabel">Xác nhận đăng xuất</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Bạn có chắc muốn đăng xuất không?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" id="confirmLogoutBtn" class="btn btn-danger">Xác nhận</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function(){
        // Intercept clicks on any anchor that links to signout.php (handles relative paths too)
        document.addEventListener('click', function(e){
          var el = e.target.closest("a[href]");
          if(!el) return;
          var href = el.getAttribute('href');
          if(!href) return;
          // normalize and check if the link points to signout.php
          var tryHref = href.split('?')[0].split('#')[0];
          if(tryHref.indexOf('signout.php') !== -1){
            e.preventDefault();
            // show bootstrap modal
            var modalEl = document.getElementById('logoutConfirmModal');
            if(!modalEl) {
              // fallback to native confirm if modal missing
              if(confirm('Bạn có chắc muốn đăng xuất không?')){
                window.location.href = href;
              }
              return;
            }
            var bsModal = new bootstrap.Modal(modalEl);
            // set up confirm button to go to the original href
            var confirmBtn = document.getElementById('confirmLogoutBtn');
            // remove previous handler to avoid multiple bindings
            confirmBtn.replaceWith(confirmBtn.cloneNode(true));
            confirmBtn = document.getElementById('confirmLogoutBtn');
            confirmBtn.addEventListener('click', function(){
              // perform logout via POST (so we don't first navigate to the signout.php page)
              // construct absolute URL for signout.php based on the clicked href
              try{
                var signoutUrl = new URL(href, window.location.href).href;
              }catch(err){
                var signoutUrl = href;
              }
              fetch(signoutUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'confirm_logout=1'
              }).then(function(){
                // After logout, redirect to homepage. Adjust path if your app is in a subfolder.
                window.location.href = window.location.origin + '/phonestoree/index.php';
              }).catch(function(){
                // fallback: navigate to the signout link if fetch fails
                window.location.href = signoutUrl;
              });
            });
            bsModal.show();
          }
        });
      })();
    </script>