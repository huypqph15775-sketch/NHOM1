<link rel="stylesheet" href="index.css">

<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set("Asia/Ho_Chi_Minh");
include ("includes/database.php");
include ("functions/functions.php");

// Nếu có session nhưng tài khoản bị khóa thì đá ra
if(isset($_SESSION['customer_id'])){
  $customer_id = $_SESSION['customer_id'];
  $get_account_status = "SELECT * FROM customer WHERE customer_id = '$customer_id'";
  $run_account_status = mysqli_query($conn, $get_account_status);
  if ($run_account_status) {
    $row_account_status = mysqli_fetch_array($run_account_status);
    if ($row_account_status && isset($row_account_status['account_status'])) {
      $account_status = $row_account_status['account_status'];
      if($account_status === "Locked"){
        echo "<script>alert('Tài khoản đã bị khóa do vi phạm chính sách của cửa hàng')</script>";
        echo "<script>window.open('signout.php', '_self')</script>";
      }
    }
  }
}
?>

<!-- Header -->
<header>
  <script>
    // Navigation guard: prevent multiple rapid redirects that cause the UI to "jump"
    (function(){
      if(window.__navGuardInstalled) return;
      window.__navGuardInstalled = true;
      var navigating = false;
      var NAV_GUARD_TIMEOUT = 800; // ms
      var origOpen = window.open.bind(window);
      window.open = function(url, target){
        if(navigating){ console.warn('Navigation suppressed (open):', url); return null; }
        navigating = true;
        setTimeout(function(){ navigating = false; }, NAV_GUARD_TIMEOUT);
        return origOpen(url, target);
      };

      // patch location.assign & replace (covers many programmatic navigations)
      try{
        var origAssign = window.location.assign.bind(window.location);
        window.location.assign = function(url){
          if(navigating){ console.warn('Navigation suppressed (assign):', url); return; }
          navigating = true;
          setTimeout(function(){ navigating = false; }, NAV_GUARD_TIMEOUT);
          return origAssign(url);
        };
      }catch(e){/* ignore if binding not allowed */}

      try{
        var origReplace = window.location.replace.bind(window.location);
        window.location.replace = function(url){
          if(navigating){ console.warn('Navigation suppressed (replace):', url); return; }
          navigating = true;
          setTimeout(function(){ navigating = false; }, NAV_GUARD_TIMEOUT);
          return origReplace(url);
        };
      }catch(e){/* ignore */}

      // best-effort: mark navigating on beforeunload so subsequent attempts are suppressed
      window.addEventListener('beforeunload', function(){ navigating = true; });
    })();
  </script>
  <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#ffffff;">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="images/phone.png" alt="logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?php if(isset($active) && $active=="Home") echo "link_active"; ?>"
               aria-current="page" href="index.php">Trang chủ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if(isset($active) && $active=="Shop") echo "link_active"; ?>"
               href="shop.php">Điện thoại</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if(isset($active) && $active=="News") echo "link_active"; ?>"
               href="news.php"> Tin tức</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if(isset($active) && $active=="Contact") echo "link_active"; ?>"
               href="contact.php">Liên hệ</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php if(isset($active) && $active=="About") echo "link_active"; ?>"
               href="about.php">Giới Thiệu</a>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 action-menu">
          <!-- Search -->
          <form action="" method="get" class="search-form">
            <input class="form-control me-2" style="width: 262px" name="key" type="search"
                   placeholder="Bạn tìm gì..." aria-label="Search"
                   value="<?php if(isset($_GET['key'])){ echo htmlspecialchars($_GET['key'], ENT_QUOTES, 'UTF-8'); } ?>">
            <button class="btn btn-outline-success search-btn" name="search" style="background-color: #0099FF">
              <i class="fas fa-search"></i>
            </button>
          </form>

          <?php
          if(isset($_GET['search'])){
            $key = isset($_GET['key']) ? trim($_GET['key']) : '';
            if($key === ''){
              echo "<script>alert('Vui lòng nhập dữ liệu tìm kiếm')</script>";
            } else {
              $key_js = rawurlencode($key);
              echo "<script>window.open('search.php?key={$key_js}', '_self')</script>";
            }
          }
          ?>

          <!-- Like icon -->
          <li class="nav-item">
            <a class="nav-link" href="#offcanvasRight" data-bs-toggle="offcanvas" aria-controls="offcanvasRight">
              <i class="fas fa-heart" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Yêu thích"></i>
            </a>
          </li>

          <!-- Notifications icon + badge -->
          <li class="nav-item">
            <a class="nav-link position-relative" href="#offcanvasNotifications" data-bs-toggle="offcanvas"
               aria-controls="offcanvasNotifications" data-bs-placement="bottom" title="Thông báo">
              <span class="position-relative d-inline-block">
                <i class="fas fa-bell"></i>
                <?php
                if(isset($_SESSION['customer_id'])){
                  $customer_id = $_SESSION['customer_id'];
                  // tránh lỗi nếu bảng chưa tồn tại
                  $check_table = "SHOW TABLES LIKE 'notifications'";
                  $run_check_table = @mysqli_query($conn, $check_table);
                  if($run_check_table && mysqli_num_rows($run_check_table) > 0){
                    // kiểm tra cột user
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
                      // check if the notifications table has an `is_admin` column before using it
                      $has_is_admin = false;
                      $col_check_is_admin = "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'";
                      $run_col_check_is_admin = @mysqli_query($conn, $col_check_is_admin);
                      if($run_col_check_is_admin && mysqli_num_rows($run_col_check_is_admin) > 0){
                        $has_is_admin = true;
                      }

                      // build query conditionally depending on column existence
                      $is_admin_condition = $has_is_admin ? " AND is_admin = 0" : "";
                      $get_unread = "SELECT * FROM notifications WHERE `".$user_col."` = '$customer_id' AND is_read = 0".$is_admin_condition;
                      $run_unread = @mysqli_query($conn, $get_unread);
                      if($run_unread){
                        $count_unread = mysqli_num_rows($run_unread);
                        if($count_unread > 0){
                          echo "<span style='font-size:12px' class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>$count_unread</span>";
                        }
                      }
                    }
                  }
                }
                ?>
              </span>
            </a>
          </li>

          <!-- Orders -->
          <li class="nav-item">
            <a class="nav-link" href="customer/my_orders.php?pending_orders">
              <i class="fas fa-clipboard-list position-relative" data-bs-toggle="tooltip"
                 data-bs-placement="bottom" title="Lịch sử đơn hàng">
                <?php
                if(isset($_SESSION['customer_id'])){
                  $customer_id = $_SESSION['customer_id'];
                  $select_count_order = "SELECT * FROM customer_orders WHERE customer_id = '$customer_id'";
                  $run_select_count = mysqli_query($conn, $select_count_order);
                  if($run_select_count && mysqli_num_rows($run_select_count) != 0){
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

          <!-- Cart -->
          <li class="nav-item">
            <a class="nav-link" href="customer/cart.php">
              <i class="fas fa-shopping-cart position-relative" data-bs-toggle="tooltip"
                 data-bs-placement="bottom" title="Giỏ hàng">
                <?php
                if(function_exists('items') && items() != 0){
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

          <!-- Account dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php
              if(!isset($_SESSION['customer_id'])){
                echo "<i class='fas fa-user' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Tài khoản'></i>";
              } else {
                $customer_name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : 'Tài khoản';
                echo "<p class='d-inline fw-bold' style='font-size:16px' data-bs-toggle='tooltip' data-bs-placement='bottom' title='Tài khoản'>".htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8')."</p>";
              }
              ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-1 shadow-sm" aria-labelledby="navbarDropdown">
              <?php
              if(!isset($_SESSION['customer_id'])){
                echo "
                  <li><a class='dropdown-item' href='signin.php'>Đăng nhập</a></li>
                  <li><a class='dropdown-item' href='signup.php'>Đăng ký</a></li>
                ";
              } else {
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
      $get_favorite_products = "SELECT * FROM favorite_product WHERE customer_id = '$customer_id'";
      $run_get_favorite = mysqli_query($conn, $get_favorite_products);
      $count_favorite = $run_get_favorite ? mysqli_num_rows($run_get_favorite) : 0;

      if($count_favorite == 0){
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
      } else {
        while($row_favorite = mysqli_fetch_array($run_get_favorite)){
          $product_id = $row_favorite['product_id'];
          $product_color_id = $row_favorite['product_color_id'];

          $select_color = "SELECT * FROM product_color WHERE product_color_id = '$product_color_id'";
          $run_select_color = mysqli_query($conn, $select_color);
          $row_select_color = $run_select_color ? mysqli_fetch_array($run_select_color) : null;
          $product_color_name = $row_select_color ? $row_select_color['product_color_name'] : '';

          $select_product = "SELECT * FROM products WHERE product_id = '$product_id'";
          $run_select_product = mysqli_query($conn, $select_product);
          $row_select_product = $run_select_product ? mysqli_fetch_array($run_select_product) : null;
          $product_name = $row_select_product ? $row_select_product['product_name'] : '';

          $select_product_img = "SELECT * FROM product_img WHERE product_id = '$product_id' AND product_color_id = '$product_color_id'";
          $run_select_product_img = mysqli_query($conn, $select_product_img);
          $row_select_product_img = $run_select_product_img ? mysqli_fetch_array($run_select_product_img) : null;

          $product_price = $row_select_product_img ? $row_select_product_img['product_price'] : 0;
          $product_price_des = $row_select_product_img ? $row_select_product_img['product_price_des'] : 0;
          $product_price_format = function_exists('currency_format') ? currency_format($product_price) : $product_price;
          $product_price_des_format = function_exists('currency_format') ? currency_format($product_price_des) : $product_price_des;
          $product_color_img = $row_select_product_img ? $row_select_product_img['product_color_img'] : '';
          ?>
          <div class="row mb-2">
            <div class="col-3">
              <a href="shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank">
                <img src="administrator/product_img/<?php echo htmlspecialchars($product_color_img, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" style="object-fit: contain !important;" alt="">
              </a>
            </div>
            <div class="col-7">
              <a href="shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank" class="fw-bold text-decoration-none">
                <?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?>
              </a>
              <span class="d-block my-2">Màu: <?php echo htmlspecialchars($product_color_name, ENT_QUOTES, 'UTF-8'); ?></span>
              <p class="like-item-price"><strike><?php echo $product_price_format; ?></strike> <b><?php echo $product_price_des_format; ?></b></p>
            </div>
            <div class="col-2">
              <form action="" method="post">
                <input type="hidden" name="customer_id" value="<?php echo (int)$customer_id; ?>">
                <input type="hidden" name="product_id" value="<?php echo (int)$product_id; ?>">
                <input type="hidden" name="product_color_id" value="<?php echo (int)$product_color_id; ?>">
                <button onclick="delete_favorite(<?php echo (int)$product_id; ?>, <?php echo (int)$product_color_id; ?>)"
                        id="delete_favorite<?php echo (int)$product_id; ?><?php echo (int)$product_color_id; ?>"
                        class="btn btn-white text-danger" style="border: none; padding: 0"><i class="fas fa-times me-1"></i>Xóa</button>
              </form>
            </div>
          </div>
          <?php
        }
      }
    } else {
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
      // chỉ hiển thị nếu bảng tồn tại
      $check_table = "SHOW TABLES LIKE 'notifications'";
      $run_check_table = @mysqli_query($conn, $check_table);

      if(!$run_check_table || mysqli_num_rows($run_check_table) == 0){
        echo "<p>Không có thông báo.</p>";
      } else {
        $customer_id = $_SESSION['customer_id'];

        // cột user nào?
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
          // support optional date filters via GET (notif_start, notif_end)
          $notif_start = isset($_GET['notif_start']) ? trim($_GET['notif_start']) : '';
          $notif_end = isset($_GET['notif_end']) ? trim($_GET['notif_end']) : '';
          $date_cond = '';
          if(!empty($notif_start)){
            $ds = DateTime::createFromFormat('Y-m-d', $notif_start);
            if($ds && $ds->format('Y-m-d') === $notif_start){
              $date_cond .= " AND created_at >= '".mysqli_real_escape_string($conn, $notif_start)." 00:00:00'";
            }
          }
          if(!empty($notif_end)){
            $de = DateTime::createFromFormat('Y-m-d', $notif_end);
            if($de && $de->format('Y-m-d') === $notif_end){
              $date_cond .= " AND created_at <= '".mysqli_real_escape_string($conn, $notif_end)." 23:59:59'";
            }
          }
          // render a small filter form (GET) - submitting reloads page and filters server-side
          echo '<form method="get" class="mb-2 d-flex gap-2">';
          echo '<input type="hidden" name="" value="">';
          echo '<input type="date" name="notif_start" class="form-control form-control-sm" value="'.htmlspecialchars($notif_start, ENT_QUOTES, 'UTF-8').'" placeholder="Từ ngày">';
          echo '<input type="date" name="notif_end" class="form-control form-control-sm" value="'.htmlspecialchars($notif_end, ENT_QUOTES, 'UTF-8').'" placeholder="Đến ngày">';
          echo '<button class="btn btn-sm btn-primary" type="submit">Lọc</button>';
          echo '<a class="btn btn-sm btn-outline-secondary" href="'.htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8').'">Xóa</a>';
          echo '</form>';

          $get_notifs = "SELECT * FROM notifications WHERE `$user_col` = '$customer_id'" . $date_cond . " ORDER BY created_at DESC LIMIT 0,10";
          $run_notifs = @mysqli_query($conn, $get_notifs);

          if(!$run_notifs || mysqli_num_rows($run_notifs) == 0){
            echo "<p>Không có thông báo.</p>";
          } else {
                while($n = mysqli_fetch_array($run_notifs)){
              // support both new and old notification schemas
              $nid = isset($n['id']) ? $n['id'] : (isset($n['notify_id']) ? $n['notify_id'] : '');
              $title = isset($n['title']) ? $n['title'] : '';
              // message may be in 'message' (new) or 'content' (old)
              $message = isset($n['message']) ? $n['message'] : (isset($n['content']) ? $n['content'] : '');
              $is_read = isset($n['is_read']) ? $n['is_read'] : 0;
              $created = isset($n['created_at']) ? $n['created_at'] : (isset($n['created']) ? $n['created'] : '');
              $badge = intval($is_read) ? '' : ' <span class="badge bg-danger">Mới</span>';

              // render each notification with a delete button and container class for easy removal
              echo "<div class='mb-3 notif-item' data-notif-id='".htmlspecialchars($nid, ENT_QUOTES, 'UTF-8')."'>";
              echo "  <div class='d-flex w-100 justify-content-between align-items-start'>";
              echo "    <div>";
              echo "      <strong>".htmlspecialchars($title, ENT_QUOTES, 'UTF-8')."$badge</strong>";
              echo "      <div class='small text-wrap'>".nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'))."</div>";
              echo "    </div>";
              // delete button (small trash icon)
              echo "    <div class='ms-2'>";
              echo "      <button type='button' class='btn btn-sm btn-outline-danger notif-delete' data-notif-id='".htmlspecialchars($nid, ENT_QUOTES, 'UTF-8')."' title='Xóa'>";
              echo "        <i class='fas fa-trash'></i>";
              echo "      </button>";
              echo "    </div>";
              echo "  </div>";
              echo "  <div class='text-muted small mt-1'>".htmlspecialchars($created, ENT_QUOTES, 'UTF-8')."</div>";
              echo "  <hr>";
              echo "</div>";
                }
                // provide a 'Delete all' control for convenience
                echo '<div class="text-center"><button id="deleteAllNotifsBtn" class="btn btn-sm btn-danger">Xóa tất cả</button></div>';
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
    if(result === true){
      var btn = document.getElementById("delete_favorite"+id1+id2);
      if (btn) btn.name = 'delete_favorite';
    }
  }
</script>

  <?php
  // Provide a JS flag indicating whether customer is logged in so client-side code can decide
  echo "<script>window.isLoggedIn = ".(isset($_SESSION['customer_id']) ? 'true' : 'false').";</script>\n";
  ?>

<script>
  (function(){
    function updateNotifBadge(count){
      var bell = document.querySelector('.fa-bell');
      if(!bell) return;
      var container = bell.parentElement;
      if(!container) return;
      var existing = container.querySelector('.notif-count-badge') || container.querySelector('.position-absolute.badge');
      if(count && parseInt(count) > 0){
        if(existing){
          existing.textContent = count;
          if(!existing.classList.contains('notif-count-badge')) existing.classList.add('notif-count-badge');
        } else {
          var span = document.createElement('span');
          span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-count-badge';
          span.style.fontSize = '12px';
          span.textContent = count;
          container.appendChild(span);
        }
      } else {
        if(existing){ existing.parentNode && existing.parentNode.removeChild(existing); }
      }
    }

    function fetchCount(){
      fetch('ajax_notifications_count.php', { credentials: 'same-origin' })
        .then(function(res){ return res.json(); })
        .then(function(data){ if(data && data.success) updateNotifBadge(data.count); })
        .catch(function(){ });
    }

    document.addEventListener('DOMContentLoaded', function(){ if(window.isLoggedIn){ fetchCount(); setInterval(fetchCount, 10000); } });
  })();
</script>

<script>
  (function(){
    function setNotifBadge(count){
      var bell = document.querySelector('.fa-bell');
      if(!bell) return;
      var container = bell.parentElement;
      if(!container) return;
      var existing = container.querySelector('.notif-count-badge') || container.querySelector('.position-absolute.badge');
      if(count && parseInt(count) > 0){
        if(existing){ existing.textContent = count; if(!existing.classList.contains('notif-count-badge')) existing.classList.add('notif-count-badge'); }
        else {
          var span = document.createElement('span');
          span.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-count-badge';
          span.style.fontSize = '12px';
          span.textContent = count;
          container.appendChild(span);
        }
      } else {
        if(existing) existing.parentNode && existing.parentNode.removeChild(existing);
      }
    }

    document.addEventListener('click', function(e){
      var del = e.target.closest && e.target.closest('.notif-delete');
      if(del){
        var nid = del.getAttribute('data-notif-id');
        if(!nid) return;
        if(!confirm('Xóa thông báo này?')) return;
        var fd = new FormData(); fd.append('action','delete'); fd.append('id', nid);
        fetch('ajax_notifications_delete.php', { method: 'POST', body: fd, credentials: 'same-origin' })
          .then(function(r){ return r.json(); })
          .then(function(j){
            if(j && j.success){
              var item = del.closest('.notif-item'); if(item) item.remove();
              setNotifBadge(j.count || 0);
              showToast(j.message || 'Đã xóa', true);
            } else {
              showToast(j && j.message ? j.message : 'Không thể xóa', false);
            }
          }).catch(function(){ showToast('Lỗi mạng khi xóa thông báo', false); });
      }

      var delAll = e.target.closest && e.target.closest('#deleteAllNotifsBtn');
      if(delAll){
        if(!confirm('Bạn có chắc muốn xóa tất cả thông báo không?')) return;
        var fd = new FormData(); fd.append('action','delete_all');
        fetch('ajax_notifications_delete.php', { method: 'POST', body: fd, credentials: 'same-origin' })
          .then(function(r){ return r.json(); })
          .then(function(j){
            if(j && j.success){
              var items = document.querySelectorAll('.offcanvas-body .notif-item');
              items.forEach(function(it){ it.remove(); });
              setNotifBadge(0);
              showToast(j.message || 'Đã xóa tất cả', true);
            } else {
              showToast(j && j.message ? j.message : 'Không thể xóa tất cả', false);
            }
          }).catch(function(){ showToast('Lỗi mạng khi xóa tất cả', false); });
      }
    }, true);
  })();
</script>

  <!-- Login-required notification modal -->
  <div class="modal fade" id="ajaxLoginModal" tabindex="-1" aria-labelledby="ajaxLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ajaxLoginModalLabel">Yêu cầu đăng nhập</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Bạn cần đăng nhập để thực hiện hành động này.</p>
        </div>
        <div class="modal-footer">
          <button type="button" id="ajaxLoginGoBtn" class="btn btn-primary">Đăng nhập</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </div>
  </div>

        <!-- Toast container for AJAX messages -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 10850">
          <div id="ajaxToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
              <div id="ajaxToastBody" class="toast-body">
                Thành công
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          </div>
        </div>

        <style>
          /* Improve login modal appearance */
          #ajaxLoginModal .modal-content{ border-radius:12px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,0.12); }
          #ajaxLoginModal .modal-header{ background: linear-gradient(90deg,#007bff,#0062cc); color:white; border-bottom:none }
          #ajaxLoginModal .modal-title{ font-weight:700 }
          #ajaxLoginModal .modal-body{ padding:22px }
        </style>

  <script>
  // Intercept add-to-cart forms and handle AJAX-login flow when user is not logged in.
  (function(){
    var pendingCartData = null; // store FormData for pending add-to-cart action
    var pendingFormOrigin = null;

    // Helper: show login modal
    function showLoginModal(){
      var modalEl = document.getElementById('ajaxLoginModal');
      var bs = new bootstrap.Modal(modalEl);
      bs.show();
    }

    // Find add-to-cart forms and submit via AJAX to avoid POST resubmission prompts
    document.addEventListener('submit', function(e){
      var form = e.target;
      if(!form.classList || !form.classList.contains('add-to-cart-form')) return;

      e.preventDefault();
      // collect form data
      pendingCartData = new FormData(form);
      // also expose a lightweight payload so it can be saved to session if user chooses to login
      try{
        var pidEl = form.querySelector('[name="product_id"]') || form.elements['product_id'];
        var qtyEl = form.querySelector('[name="quantity"]') || form.elements['quantity'];
        var colorEl = form.querySelector('[name="product_color"]') || form.querySelector('[name="color"]') || form.elements['product_color'] || form.elements['color'];
        window.pendingCartPayload = {
          product_id: pidEl ? pidEl.value : '',
          quantity: qtyEl ? qtyEl.value : 1,
          product_color: colorEl ? colorEl.value : ''
        };
      }catch(e){ window.pendingCartPayload = null; }
      pendingFormOrigin = { action: form.getAttribute('action') || window.location.href, method: (form.getAttribute('method') || 'POST').toUpperCase() };

      // If user is not logged in, show login modal and wait; after login pendingCartData will be sent
      if(!window.isLoggedIn){
        showLoginModal();
        return;
      }

      // If logged in, submit via AJAX immediately to avoid full page POST
      fetch('ajax_add_to_cart.php', {
        method: 'POST',
        body: pendingCartData,
        credentials: 'same-origin'
      }).then(function(r){ return r.json(); }).then(function(j){
          // update badge if server provided items_count (handle both success and failure cases)
          var updateBadge = function(count){
            var cartIcon = document.querySelector('.fa-shopping-cart');
            if(!cartIcon) return;
            // Try to find any existing badge (server-rendered or previously JS-created)
            var badge = cartIcon.parentElement.querySelector('.cart-badge')
                       || cartIcon.querySelector('.badge')
                       || cartIcon.parentElement.querySelector('.badge');

            if(!badge){
              badge = document.createElement('span');
              badge.className = 'cart-badge position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger';
              badge.style.fontSize = '12px';
              // append to the icon so positioning matches server-rendered placement
              cartIcon.appendChild(badge);
            }

            count = parseInt(count,10) || 0;
            if(count > 0){
              badge.textContent = count;
              badge.style.display = '';
            } else {
              try{ badge.parentElement.removeChild(badge); }catch(e){}
            }
          };

          if(j && typeof j.items_count !== 'undefined'){
            updateBadge(j.items_count);
          }

          if(j && j.success){
            showToast(j.message || 'Đã thêm vào giỏ hàng', true);
            pendingCartData = null;
          } else {
            showToast(j && j.message ? j.message : 'Không thể thêm vào giỏ hàng', false);
          }
      }).catch(function(){ showToast('Lỗi mạng khi thêm vào giỏ hàng.', false); });
    }, true);

    // No inline AJAX login form: the modal now shows a simple notification with buttons.
  })();
  </script>

  <script>
    // When the user clicks the modal 'Đăng nhập' button, redirect to signin page.
    document.addEventListener('DOMContentLoaded', function(){
      var btn = document.getElementById('ajaxLoginGoBtn');
      if(!btn) return;
      btn.addEventListener('click', function(){
        // If there was a pending navigation (link), prefer that; otherwise use current page
        var target = window.pendingNavigation || window.location.href;

        // If there is a pending cart payload, save it to session on the server before redirecting
        if(window.pendingCartPayload){
          var fd = new FormData();
          fd.append('action', 'add_to_cart');
          fd.append('product_id', window.pendingCartPayload.product_id);
          fd.append('quantity', window.pendingCartPayload.quantity);
          fd.append('product_color', window.pendingCartPayload.product_color);
          // Best-effort POST, then redirect regardless of result
          fetch('save_pending_action.php', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          }).catch(function(){ /* ignore */ }).finally(function(){
            var signinUrl = 'signin.php?redirect=' + encodeURIComponent(target);
            window.location.href = signinUrl;
          });
          return;
        }

        var signinUrl = 'signin.php?redirect=' + encodeURIComponent(target);
        window.location.href = signinUrl;
      });
    });
  </script>

  <script>
    // Show Bootstrap toast for AJAX messages
    function showToast(message, success){
      var toastEl = document.getElementById('ajaxToast');
      var toastBody = document.getElementById('ajaxToastBody');
      if(!toastEl || !toastBody){
        alert(message);
        return;
      }
      toastBody.textContent = message;
      if(success){
        toastEl.classList.remove('bg-danger'); toastEl.classList.add('bg-success');
        toastEl.classList.remove('text-white');
      } else {
        toastEl.classList.remove('bg-success'); toastEl.classList.add('bg-danger');
        toastEl.classList.add('text-white');
      }
      var bs = new bootstrap.Toast(toastEl, { delay: 3500 });
      bs.show();
    }
  </script>

  <script>
  // Intercept clicks on anchors that require login (class .needs-login)
  (function(){
    document.addEventListener('click', function(e){
      var el = e.target.closest && e.target.closest('a.needs-login');
      if(!el) return;
      // If already logged in, allow default navigation
      if(window.isLoggedIn) return;
      e.preventDefault();
      // store desired navigation and show login modal
      try{ window.pendingNavigation = el.getAttribute('href'); }catch(err){ window.pendingNavigation = el.href; }
      var modalEl = document.getElementById('ajaxLoginModal');
      var bs = new bootstrap.Modal(modalEl);
      bs.show();
    }, true);
  })();
  </script>

<?php
// Xử lý like / unlike sản phẩm
if(isset($_POST['like_product'])){
  if(isset($_SESSION['customer_id'])){
    $customer_id = $_SESSION['customer_id'];
    $product_id = isset($_POST['favorite_product_id']) ? (int)$_POST['favorite_product_id'] : 0;
    $product_color_id = isset($_POST['product_color_id']) ? (int)$_POST['product_color_id'] : 0;
    $path = $_SERVER['SCRIPT_NAME'];
    $queryString = $_SERVER['QUERY_STRING'];

    $check_favorite = "SELECT * FROM favorite_product WHERE customer_id = '$customer_id' AND product_id = '$product_id' AND product_color_id = '$product_color_id'";
    $run_check_favorite = mysqli_query($conn, $check_favorite);
    $count_check_favorite = $run_check_favorite ? mysqli_num_rows($run_check_favorite) : 0;

    if($count_check_favorite == 0){
      $insert_favorite_product = "INSERT INTO favorite_product(customer_id, product_id, product_color_id) VALUES ('$customer_id', '$product_id', '$product_color_id')";
      $run_favorite_product = mysqli_query($conn, $insert_favorite_product);
      if($run_favorite_product){
        echo "<script>alert('Một sản phẩm vừa được thêm vào Yêu thích')</script>";
        echo "<script>window.open('".$path.(strlen($queryString)?('?'.$queryString):'')."','_self')</script>";
      }
    } else {
      $delete_favorite_product = "DELETE FROM favorite_product WHERE customer_id = '$customer_id' AND product_id = '$product_id' AND product_color_id = '$product_color_id'";
      $run_delete_favorite_product = mysqli_query($conn, $delete_favorite_product);
      if($run_delete_favorite_product){
        echo "<script>alert('Đã xóa sản phẩm khỏi Yêu thích')</script>";
        echo "<script>window.open('".$path.(strlen($queryString)?('?'.$queryString):'')."','_self')</script>";
      }
    }
  } else {
    echo "<script>alert('Bạn cần đăng nhập để thực hiện chức năng này')</script>";
  }
}

// Xử lý xóa trong offcanvas
if(isset($_POST['delete_favorite'])){
  $customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
  $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
  $product_color_id = isset($_POST['product_color_id']) ? (int)$_POST['product_color_id'] : 0;
  $path = $_SERVER['SCRIPT_NAME'];
  $queryString = $_SERVER['QUERY_STRING'];

  $delete_favorite_product = "DELETE FROM favorite_product WHERE customer_id = '$customer_id' AND product_id = '$product_id' AND product_color_id = '$product_color_id'";
  $run_delete_favorite = mysqli_query($conn, $delete_favorite_product);
  if($run_delete_favorite){
    echo "<script>alert('Đã xóa sản phẩm khỏi Yêu thích')</script>";
    echo "<script>window.open('".$path.(strlen($queryString)?('?'.$queryString):'')."','_self')</script>";
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
        var signoutUrl;
        try{
          signoutUrl = new URL(href, window.location.href).href;
        }catch(err){
          signoutUrl = href;
        }
        fetch(signoutUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'confirm_logout=1'
        }).then(function(){
          // After logout, redirect to homepage. Adjust path if your app is in a subfolder.
          var base = window.location.origin;
          // nếu app nằm trong /phonestoree/
          window.location.href = base + '/phonestoree/index.php';
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
