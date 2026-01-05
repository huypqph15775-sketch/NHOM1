<?php
// This file is intended to be included by `administrator/index.php`
// which already includes admin header, nav, DB and session setup.
// Provide a safe fallback so the page can also be opened directly for testing.
// Ensure DB connection and helper are available when opened directly.
// Ensure DB connection and helper are available when opened directly.
if(!isset($conn)){
  $db_path = __DIR__ . '/../includes/database.php';
  if(file_exists($db_path)) require_once $db_path;
}
if(!function_exists('add_notification')){
  $func_path = __DIR__ . '/../functions/functions.php';
  if(file_exists($func_path)) require_once $func_path;
}
// If still missing, include admin nav for layout (it will also attempt to include DB/functions).
if(!function_exists('add_notification') || !isset($conn)){
  require_once __DIR__ . '/includes/nav.php';
}

// handle form submission
$success = '';
$error = '';
if(isset($_POST['send_notification'])){
  $title = trim($_POST['title'] ?? '');
  $message = trim($_POST['message'] ?? '');
  $target = $_POST['target'] ?? 'all';
  $customers = $_POST['customers'] ?? [];

  if($title === '' || $message === ''){
    $error = 'Vui lòng nhập tiêu đề và nội dung thông báo.';
  } else {
    // use the existing helper add_notification()
    $sent = 0;
    $num_customers_found = 0;
    $sql_error = '';
    if($target === 'all'){
      $get_all = "SELECT customer_id FROM customer";
      $run_all = mysqli_query($conn, $get_all);
      if($run_all){
        $num_customers_found = mysqli_num_rows($run_all);
        while($r = mysqli_fetch_assoc($run_all)){
          $to_id = isset($r['customer_id']) ? intval($r['customer_id']) : 0;
          if($to_id > 0 && function_exists('add_notification')){
            $ok = add_notification($to_id, 0, 'admin_message', $title, $message, null);
            if($ok){
              $sent++;
            } else {
              // collect non-fatal note and include DB error if available
              $db_err = isset($conn) ? mysqli_error($conn) : '';
              $error .= " Không gửi được tới ID=$to_id." . ($db_err ? ' Lỗi DB: '.htmlspecialchars($db_err) : '');
            }
          }
        }
      } else {
        $sql_error = mysqli_error($conn);
      }
    } else {
      // selected customers
      $num_customers_found = is_array($customers) ? count($customers) : 0;
      foreach($customers as $cid){
        $cid = intval($cid);
        if($cid>0 && function_exists('add_notification')){
          $ok = add_notification($cid, 0, 'admin_message', $title, $message, null);
          if($ok){
            $sent++;
          } else {
            $db_err = isset($conn) ? mysqli_error($conn) : '';
            $error .= " Không gửi được tới ID=$cid." . ($db_err ? ' Lỗi DB: '.htmlspecialchars($db_err) : '');
          }
        }
      }
    }

    // Basic verification: count notifications with same title+content inserted recently
    $inserted_count = 0;
    if($sent > 0){
      $safe_title = mysqli_real_escape_string($conn, $title);
      $safe_message = mysqli_real_escape_string($conn, $message);
      $check_sql = "SELECT COUNT(*) as cnt FROM notifications WHERE title = '$safe_title' AND content = '$safe_message'";
      $rchk = @mysqli_query($conn, $check_sql);
      if($rchk){
        $rowc = mysqli_fetch_assoc($rchk);
        $inserted_count = intval($rowc['cnt']);
      }
    }

    $success = "Đã gửi thông báo tới $sent khách hàng.";
    if($sent == 0){
      if(!empty($sql_error)){
        $error .= ' Lỗi SQL: '.htmlspecialchars($sql_error);
      } elseif($target === 'all' && $num_customers_found === 0){
        $error .= ' Không tìm thấy khách hàng trong bảng `customer`.';
      } elseif($target === 'selected' && $num_customers_found === 0){
        $error .= ' Không có khách hàng được chọn.';
      } else {
        $error .= ' Không thể gửi thông báo (không có quyền gọi add_notification hoặc lỗi không xác định).';
        // Diagnostic info for admin: help identify why add_notification failed
        $diag = '';
        if(!function_exists('add_notification')){
          $diag .= ' Hàm add_notification không tồn tại.';
        }
        if(!isset($conn)){
          $diag .= ' Kết nối DB ($conn) chưa được thiết lập.';
        } else {
          // check if notifications table exists
          $tbl = @mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
          if(!$tbl || @mysqli_num_rows($tbl) === 0){
            $diag .= " Bảng 'notifications' không tồn tại.";
          } else {
            // list columns for visibility
            $cols = @mysqli_query($conn, "SHOW COLUMNS FROM notifications");
            if($cols){
              $col_names = [];
              while($cc = mysqli_fetch_assoc($cols)){
                $col_names[] = $cc['Field'];
              }
              $diag .= ' Columns: '.implode(', ', $col_names).'.';
            }
          }
          $last = mysqli_error($conn);
          if($last){
            $diag .= ' Lỗi DB gần nhất: '. $last;
          }
        }
        if($diag !== ''){
          $error .= ' Chi tiết: '.htmlspecialchars($diag);
        }
      }
    } else {
      // if inserted_count is different from sent, show note
      if($inserted_count !== 0 && $inserted_count < $sent){
        $success .= " (ghi nhận $inserted_count bản ghi trong bảng notifications).";
      } elseif($inserted_count === 0){
        $success .= " (Không xác nhận bản ghi trong bảng notifications).";
      }
    }
  }
}
?>
<div class="container mt-5 pt-4">
  <h3>Tạo thông báo cho khách hàng</h3>
  <?php if($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <?php if($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Tiêu đề</label>
      <input type="text" name="title" class="form-control" required>
    </div>
      <div class="mb-3">
        <label class="form-label">Nội dung</label>
        <div style="max-width:100%; width:100%; margin:auto;">
          <textarea name="message" class="form-control" rows="6" required style="white-space:pre-wrap; width:100%; resize:vertical; border-radius:0 !important;"><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>
    <div class="mb-3">
      <label class="form-label">Đối tượng</label>
      <div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="target_all" value="all" checked>
          <label class="form-check-label" for="target_all">Tất cả khách hàng</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="target" id="target_selected" value="selected">
          <label class="form-check-label" for="target_selected">Chọn khách hàng</label>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Danh sách khách hàng</label>
      <input type="text" id="customerFilter" class="form-control mb-2" placeholder="Tìm kiếm khách hàng (tên hoặc email)">
      <div id="customerList" class="border p-2" style="max-height:300px; overflow:auto;">
        <?php
          $get_customers = "SELECT customer_id, customer_name, customer_email FROM customer ORDER BY customer_id DESC";
          $run_customers = @mysqli_query($conn, $get_customers);
          if($run_customers){
            while($c = mysqli_fetch_assoc($run_customers)){
              $cid = (int)$c['customer_id'];
              $cname = $c['customer_name'];
              $cemail = $c['customer_email'];
              // preserve previously checked values after submit
              $checked = '';
              if(!empty($_POST['customers']) && in_array($cid, array_map('intval', (array)$_POST['customers']))){ $checked = 'checked'; }
              echo "<div class=\"form-check\">";
              echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"customers[]\" value=\"$cid\" id=\"cust_$cid\" $checked> ";
              echo "<label class=\"form-check-label\" for=\"cust_$cid\">".htmlspecialchars($cname, ENT_QUOTES, 'UTF-8')." (".htmlspecialchars($cemail, ENT_QUOTES, 'UTF-8').")</label>";
              echo "</div>";
            }
          } else {
            echo "<div>Không tìm thấy khách hàng.</div>";
          }
        ?>
      </div>
    </div>

    <script>
      (function(){
        var input = document.getElementById('customerFilter');
        var list = document.getElementById('customerList');
        if(!input || !list) return;
        input.addEventListener('input', function(){
          var q = input.value.trim().toLowerCase();
          var items = list.querySelectorAll('.form-check');
          items.forEach(function(it){
            var label = it.querySelector('label');
            var text = label ? label.textContent.toLowerCase() : '';
            if(q === '' || text.indexOf(q) !== -1){
              it.style.display = '';
            } else {
              it.style.display = 'none';
            }
          });
        });
      })();
    </script>

    <script>
      (function(){
        var targetAll = document.getElementById('target_all');
        var targetSelected = document.getElementById('target_selected');
        var list = document.getElementById('customerList');
        if(!list || !targetAll || !targetSelected) return;

        var checkboxes = Array.prototype.slice.call(list.querySelectorAll('input[type="checkbox"][name="customers[]"]'));
        var suppress = false;

        function setAllChecked(val){
          suppress = true;
          checkboxes.forEach(function(cb){ cb.checked = val; });
          suppress = false;
        }

        // On load: if 'all' is selected and there are no pre-checked boxes, check all
        var anyChecked = checkboxes.some(function(cb){ return cb.checked; });
        if(targetAll.checked && !anyChecked){ setAllChecked(true); }

        targetAll.addEventListener('change', function(){
          if(targetAll.checked){ setAllChecked(true); }
        });

        targetSelected.addEventListener('change', function(){
          if(targetSelected.checked){
            // switch to manual selection: clear all so admin can pick
            setAllChecked(false);
          }
        });

        // clicking a customer row toggles its checkbox (except when clicking the checkbox itself)
        var items = list.querySelectorAll('.form-check');
        items.forEach(function(it){
          var cb = it.querySelector('input[type="checkbox"]');
          if(!cb) return;
          it.addEventListener('click', function(e){
            if(e.target === cb) return; // native toggle will handle
            cb.checked = !cb.checked;
            if(!suppress){ targetSelected.checked = true; }
          });
          // Also listen to direct checkbox changes (keyboard etc.)
          cb.addEventListener('change', function(){ if(!suppress) targetSelected.checked = true; });
        });
      })();
    </script>

    <button name="send_notification" class="btn btn-primary">Gửi thông báo</button>
    <a href="index.php?notifications" class="btn btn-secondary ms-2">Quay lại</a>
  </form>
</div>

<?php
// EOF
