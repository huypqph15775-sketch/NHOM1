<style>
  .link-active{
    background-color: #000;
    color: #ffd400 !important;
  }
</style>

<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  date_default_timezone_set("Asia/Ho_Chi_Minh");    
  include("includes/database.php");
  include("functions/functions.php");
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
      echo "<script>window.open('../signout.php', '_self')</script>";
  }
}
?>

<!-- Header -->
<header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
              <a class="navbar-brand" href="../index.php">
                  <img src="images/smartphone.png" alt="logo">
              </a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Home") echo "link-active"; ?>" aria-current="page" href="../index.php">Trang chủ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Shop") echo "link-active"; ?>" href="../shop.php">Điện thoại</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="Contact") echo "link-active"; ?>" href="../contact.php">Liên hệ</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="News") echo "link-active"; ?>" href="../news.php">Tin tức</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if($active=="About") echo "link-active"; ?>" href="../about.php">About Us</a>
                  </li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 action-menu">
          <form action="" method="get" class="search-form">
            <input class="form-control me-2 search-input" name="key" type="search" placeholder="Bạn tìm gì..." aria-label="Search" value="<?php if(isset($_GET['key'])){$key=$_GET['key']; echo "$key";}; ?>">
                        <button class="btn btn-outline-success search-btn" name="search">
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
                          echo "<script>window.open('../search.php?key=$key', '_self')</script>";
                        }
                        
                      }
                    ?>
                    <li class="nav-item">
                      <a class="nav-link" href="#offcanvasRight" data-bs-toggle="offcanvas" aria-controls="offcanvasRight">
                          <i class="fas fa-heart" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Yêu thích"></i>
                      </a>
                    </li>
                    <!-- Notifications -->
                    <li class="nav-item">
                      <a class="nav-link position-relative" href="#offcanvasNotifications" data-bs-toggle="offcanvas" aria-controls="offcanvasNotifications">
                        <span class="position-relative d-inline-block">
                          <i class="fas fa-bell" data-bs-placement="bottom" title="Thông báo"></i>
                          <?php
                          if(isset($_SESSION['customer_id'])){
                            $customer_id = $_SESSION['customer_id'];
                            $check_table = "SHOW TABLES LIKE 'notifications'";
                            $run_check_table = @mysqli_query($conn, $check_table);
                            if($run_check_table && mysqli_num_rows($run_check_table) > 0){
                              $notif_user_col = false;
                              $col_check = "SHOW COLUMNS FROM `notifications` LIKE 'user_id'";
                              $run_col_check = @mysqli_query($conn, $col_check);
                              if($run_col_check && mysqli_num_rows($run_col_check) > 0){
                                $notif_user_col = 'user_id';
                              } else {
                                $col_check2 = "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'";
                                $run_col_check2 = @mysqli_query($conn, $col_check2);
                                if($run_col_check2 && mysqli_num_rows($run_col_check2) > 0){
                                  $notif_user_col = 'customer_id';
                                }
                              }

                              if($notif_user_col){
                                $user_col = $notif_user_col;
                                $has_is_admin = false;
                                $col_check_is_admin = "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'";
                                $run_col_check_is_admin = @mysqli_query($conn, $col_check_is_admin);
                                if($run_col_check_is_admin && mysqli_num_rows($run_col_check_is_admin) > 0){
                                  $has_is_admin = true;
                                }

                                $is_admin_condition = $has_is_admin ? " AND is_admin = 0" : "";
                                $get_unread = "SELECT * FROM notifications WHERE `".$user_col."` = '$customer_id' AND is_read = 0".$is_admin_condition;
                                $run_unread = @mysqli_query($conn, $get_unread);
                                if($run_unread){
                                  $count_unread = mysqli_num_rows($run_unread);
                                  if($count_unread > 0){
                                    echo "<span style='font-size:12px' class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>".$count_unread."</span>";
                                  }
                                }
                              }
                            }
                          }
                          ?>
                        </span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="my_orders.php?pending_orders">
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
                      <a class="nav-link" href="cart.php">
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
                            <li><a class='dropdown-item' href='../signin.php'>Đăng nhập</a></li>
                            <li><a class='dropdown-item' href='../signup.php'>Đăng ký</a></li>
                            ";
                          }
                          else{
                            echo "
                            <li><a class='dropdown-item' href='myaccount.php?profile'>Tài khoản</a></li>
                            <li><a class='dropdown-item' href='../signout.php'>Đăng xuất</a></li>
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
            <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank"><img src="../administrator/product_img/<?= $product_color_img;?>" class="img-fluid" style="object-fit: contain !importaint;" alt=""></a>
            </div>
            <div class="col-7">
              <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank" class="fw-bold text-decoration-none">
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
      <!-- offcanvas for notifications -->
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNotifications" aria-labelledby="offcanvasNotificationsLabel">
        <div class="offcanvas-header">
          <h5 class="text-danger fw-bold" id="offcanvasNotificationsLabel">Thông báo</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
        <?php
        if(!isset($_SESSION['customer_id'])){
          echo "<p>Bạn cần đăng nhập để xem thông báo</p>";
        } else {
          $check_table = "SHOW TABLES LIKE 'notifications'";
          $run_check_table = @mysqli_query($conn, $check_table);
          if(!$run_check_table || mysqli_num_rows($run_check_table) == 0){
            echo "<p>Không có thông báo.</p>";
          } else {
            $customer_id = $_SESSION['customer_id'];
            $notif_user_col = false;
            $col_check = "SHOW COLUMNS FROM `notifications` LIKE 'user_id'";
            $run_col_check = @mysqli_query($conn, $col_check);
            if($run_col_check && mysqli_num_rows($run_col_check) > 0){
              $notif_user_col = 'user_id';
            } else {
              $col_check2 = "SHOW COLUMNS FROM `notifications` LIKE 'customer_id'";
              $run_col_check2 = @mysqli_query($conn, $col_check2);
              if($run_col_check2 && mysqli_num_rows($run_col_check2) > 0){
                $notif_user_col = 'customer_id';
              }
            }

            if(!$notif_user_col){
              echo "<p>Không có thông báo.</p>";
            } else {
              $user_col = $notif_user_col;
              $get_notifs = "SELECT * FROM notifications WHERE `$user_col` = '$customer_id' ORDER BY created_at DESC LIMIT 0,10";
              $run_notifs = @mysqli_query($conn, $get_notifs);

              if(!$run_notifs || mysqli_num_rows($run_notifs) == 0){
                echo "<p>Không có thông báo.</p>";
              } else {
                while($n = mysqli_fetch_array($run_notifs)){
                  // Support both new and old notification schemas
                  $nid = isset($n['id']) ? $n['id'] : (isset($n['notify_id']) ? $n['notify_id'] : '');
                  $title = isset($n['title']) ? $n['title'] : '';
                  // message column may be 'message' (new) or 'content' (old)
                  $message = isset($n['message']) ? $n['message'] : (isset($n['content']) ? $n['content'] : '');
                  $is_read = isset($n['is_read']) ? $n['is_read'] : 0;
                  $created = isset($n['created_at']) ? $n['created_at'] : (isset($n['created']) ? $n['created'] : '');
                  $badge = intval($is_read) ? '' : ' <span class="badge bg-danger">Mới</span>';

                  echo "<div class='mb-3'>";
                  echo "  <div class='d-flex w-100 justify-content-between'>";
                  echo "    <strong>".htmlspecialchars($title, ENT_QUOTES, 'UTF-8')."$badge</strong>";
                  echo "    <small class='text-muted'>".htmlspecialchars($created, ENT_QUOTES, 'UTF-8')."</small>";
                  echo "  </div>";
                  echo "  <div class='small text-wrap'>".nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'))."</div>";
                  echo "  <hr>";
                  echo "</div>";
                }
                echo "<div class='text-center'><a href='notifications.php' class='btn btn-sm btn-primary'>Xem tất cả</a></div>";
              }
            }
          }
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
        }
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
