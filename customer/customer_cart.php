<?php
// customer/customer_cart.php
// Rebuilt clean cart + checkout fragment. Assumes $conn, helper functions (total_price, items, currency_format) and session are available.

// --- Early AJAX handlers: handle voucher apply/remove before any HTML output ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // apply voucher via AJAX
  if (isset($_POST['apply_voucher_ajax'])) {
    header('Content-Type: application/json');
    if (empty(trim($_POST['voucher_code'])) || !isset($_SESSION['customer_id'])) {
      echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ hoặc bạn chưa đăng nhập.']);
      exit;
    }
    $voucher_code = trim($_POST['voucher_code']);
    $voucher_code_esc = mysqli_real_escape_string($conn, $voucher_code);
    $today = date('Y-m-d');
  // Normalize matching: trim & case-insensitive, tolerate NULL quantity, compare DATE() to avoid time-part issues
  $customer_filter = isset($_SESSION['customer_id']) ? "(allowed_customer_id IS NULL OR allowed_customer_id = '".intval($_SESSION['customer_id'])."')" : "(allowed_customer_id IS NULL)";
  $sql_voucher = "SELECT * FROM vouchers WHERE TRIM(LOWER(code)) = LOWER('$voucher_code_esc') AND status = 'active' AND $customer_filter AND (quantity IS NULL OR quantity > 0) AND (start_date IS NULL OR DATE(start_date) <= '$today') AND (end_date IS NULL OR DATE(end_date) >= '$today') LIMIT 1";
    $run_voucher = mysqli_query($conn, $sql_voucher);
    $cart_total = function_exists('total_price') ? total_price() : 0;
    if ($run_voucher && mysqli_num_rows($run_voucher) > 0) {
      $row_voucher = mysqli_fetch_assoc($run_voucher);
      $discount_percent = (int)$row_voucher['discount_percent'];
      $discount_amount = (int)$row_voucher['discount_amount'];
      $min_order = (int)$row_voucher['min_order'];
      $max_discount = (int)$row_voucher['max_discount'];
      if ($cart_total >= $min_order) {
        $discount_value = 0;
        if ($discount_percent > 0) {
          $discount_value = (int) floor($cart_total * $discount_percent / 100);
          if ($max_discount > 0 && $discount_value > $max_discount) $discount_value = $max_discount;
        } elseif ($discount_amount > 0) {
          $discount_value = $discount_amount;
        }
        if ($discount_value > $cart_total) $discount_value = $cart_total;
        $total_after = $cart_total - $discount_value;
        // store in session
        $_SESSION['applied_voucher'] = $voucher_code;
        $_SESSION['applied_voucher_id'] = (int)$row_voucher['voucher_id'];
        $_SESSION['applied_discount_value'] = $discount_value;
        $_SESSION['applied_total_after'] = $total_after;

        $formatted_cart_total = function_exists('currency_format') ? currency_format($cart_total) : number_format($cart_total);
        $formatted_discount = function_exists('currency_format') ? currency_format($discount_value) : number_format($discount_value);
        $formatted_after = function_exists('currency_format') ? currency_format($total_after) : number_format($total_after);

        echo json_encode([
          'success' => true,
          'code' => $voucher_code,
          'formatted_cart_total' => $formatted_cart_total,
          'formatted_discount_value' => $formatted_discount,
          'formatted_total_after' => $formatted_after,
          'discount_value' => $discount_value,
          'total_after' => $total_after
        ]);
        exit;
      } else {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá này.']);
        exit;
      }
    } else {
      // Provide helpful debug info when voucher not found to aid diagnosis
      $debug = ['exists' => false];
  $check_any = mysqli_query($conn, "SELECT * FROM vouchers WHERE code = '$voucher_code_esc' LIMIT 1");
      if ($check_any && mysqli_num_rows($check_any) > 0) {
        $rv = mysqli_fetch_assoc($check_any);
        $debug['exists'] = true;
        // include relevant fields only
        $debug['row'] = [
          'status' => $rv['status'] ?? null,
          'quantity' => (int)($rv['quantity'] ?? 0),
          'start_date' => $rv['start_date'] ?? null,
          'end_date' => $rv['end_date'] ?? null,
          'discount_percent' => (int)($rv['discount_percent'] ?? 0),
          'discount_amount' => (int)($rv['discount_amount'] ?? 0),
          'min_order' => (int)($rv['min_order'] ?? 0),
          'max_discount' => (int)($rv['max_discount'] ?? 0)
        ];
      }
      // write a short server-side debug log for easier diagnosis when users report failures
      try {
        $logdir = __DIR__ . '/../logs';
        if (!is_dir($logdir)) @mkdir($logdir, 0755, true);
        $logfile = $logdir . '/voucher_debug.log';
        $logLine = date('c') . " - IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . " - code=" . $voucher_code . " - customer_id=" . ($_SESSION['customer_id'] ?? 'guest') . " - debug=" . json_encode($debug) . PHP_EOL;
        @file_put_contents($logfile, $logLine, FILE_APPEND | LOCK_EX);
      } catch (Exception $e) {
        // ignore logging errors
      }
      echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.', 'debug' => $debug]);
      exit;
    }
  }

  // remove voucher via AJAX
  if (isset($_POST['remove_voucher_ajax'])) {
    header('Content-Type: application/json');
    unset($_SESSION['applied_voucher']);
    unset($_SESSION['applied_voucher_id']);
    unset($_SESSION['applied_discount_value']);
    unset($_SESSION['applied_total_after']);
    $cart_total = function_exists('total_price') ? total_price() : 0;
    $formatted_cart_total = function_exists('currency_format') ? currency_format($cart_total) : number_format($cart_total);
    echo json_encode(['success' => true, 'formatted_cart_total' => $formatted_cart_total]);
    exit;
  }
}

// allow removal via GET for UI fallback (only for logged-in customer)
if (isset($_GET['remove_item']) && isset($_SESSION['customer_id'])) {
  $remove_pid = intval($_GET['remove_item']);
  $remove_color = mysqli_real_escape_string($conn, $_GET['color'] ?? '');
  $del_q = "DELETE FROM cart WHERE product_id = '$remove_pid' AND color = '" . $remove_color . "' AND customer_id = '" . intval($_SESSION['customer_id']) . "'";
  mysqli_query($conn, $del_q);
  // redirect back to this page
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

?>

<!-- small quantity helpers (fallback if external JS not loaded) -->
<script>
if (typeof increaseCount === 'undefined') {
  function increaseCount(e, btn){
    try{
      if(e && e.preventDefault) e.preventDefault();
    }catch(err){}
    var container = btn && btn.parentElement;
    if(!container) return;
    var input = container.querySelector('input[name="quantity"]');
    if(!input) return;
    var v = parseInt(input.value,10);
    if(isNaN(v)) v = 0;
    input.value = v + 1;
    return false;
  }
}
if (typeof decreaseCount === 'undefined') {
  function decreaseCount(e, btn){
    try{
      if(e && e.preventDefault) e.preventDefault();
    }catch(err){}
    var container = btn && btn.parentElement;
    if(!container) return;
    var input = container.querySelector('input[name="quantity"]');
    if(!input) return;
    var v = parseInt(input.value,10);
    if(isNaN(v)) v = 0;
    if(v>1) input.value = v - 1;
    return false;
  }
}
// helper to submit remove form with confirmation (fix missing delete behavior)
function submitRemove(form){
  try{
    if(!form) return false;
    if(!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return false;
    form.submit();
  }catch(e){
    console.error('submitRemove error', e);
  }
  return false;
}
</script>

<script>
// apply a voucher via AJAX and update totals on the page
async function applyVoucher(code){
  // optimistic: fill the voucher input only when an explicit code is passed
  try{ var vcImmediate = document.getElementById('voucher_code'); if(typeof code !== 'undefined' && code !== null && vcImmediate) vcImmediate.value = code; }catch(e){}
    try{
    console.log('applyVoucher called with code:', code);
    // allow calling without code (use input value)
    if (!code) {
      var iv = document.getElementById('voucher_code');
      code = iv ? iv.value.trim() : '';
    }
    var data = new FormData();
    data.append('voucher_code', code);
    data.append('apply_voucher_ajax', '1');
  // Use the current path (without querystring/hash) to ensure we hit the cart handler
  const reqUrl = window.location.pathname || window.location.href;
  console.log('applyVoucher: sending AJAX to', reqUrl, 'with code', code);
  const resp = await fetch(reqUrl, {method: 'POST', body: data, credentials: 'same-origin'});
    if (!resp.ok) {
      const txt = await resp.text().catch(()=>null);
      console.error('applyVoucher non-ok response', resp.status, txt);
      throw new Error('Network response was not ok');
    }
    let json;
    try{ json = await resp.json(); } catch(e){ const txt = await resp.text().catch(()=>null); console.error('applyVoucher invalid JSON', txt); throw e; }
    console.log('applyVoucher: server response', json);
    if (json.success) {
      // Update both displayed subtotal (Tạm tính) and total to reflect the discounted amount
      // Use formatted_total_after if available (server returns both pre and post values)
      var newAmount = json.formatted_total_after || json.formatted_cart_total;
      if (document.getElementById('cart_total')) document.getElementById('cart_total').textContent = newAmount;
      if (document.getElementById('discount_value')) {
        document.getElementById('discount_value').textContent = '- ' + json.formatted_discount_value;
        var dr = document.getElementById('discount_row'); if(dr){ dr.classList.remove('d-none'); dr.style.removeProperty('display'); dr.style.display = ''; }
      }
  if (document.getElementById('after_total')) document.getElementById('after_total').textContent = json.formatted_total_after;
  if (document.getElementById('checkout_total')) document.getElementById('checkout_total').textContent = json.formatted_total_after;
      // ensure remove buttons are visible (fix case where CSS hid them)
      Array.from(document.querySelectorAll('.remove-button')).forEach(function(b){ b.style.removeProperty('display'); b.style.display = 'inline-block'; });
      var vc = document.getElementById('voucher_code'); if(vc) vc.value = json.code || code;
            // hide selected voucher area after applying
            var sel = document.getElementById('selected_voucher'); if(sel) sel.style.display = 'none';
      return;
    } else {
      // show a clear message to the user and log for debugging
      console.warn('applyVoucher failed:', json.message);
      alert(json.message || 'Không thể áp mã');
    }
  } catch(err){
    console.error('applyVoucher error', err);
    // fallback to form submit flow
    var f = document.getElementById('checkout_form');
    if(f){
      document.getElementById('voucher_code').value = code;
      var inp = document.createElement('input'); inp.type='hidden'; inp.name='apply_voucher'; inp.value='1'; f.appendChild(inp);
      f.submit();
    } else {
      alert('Không thể áp mã (lỗi JS). Vui lòng thử lại.');
    }
  }
}

// remove voucher via AJAX and update totals
async function removeVoucher(){
  try{
    var data = new FormData();
    data.append('remove_voucher_ajax','1');
    const resp = await fetch(window.location.href, {method:'POST', body: data, credentials: 'same-origin'});
    if (!resp.ok) {
      const txt = await resp.text().catch(()=>null);
      console.error('removeVoucher non-ok response', resp.status, txt);
      throw new Error('Network response was not ok');
    }
    let json;
    try{ json = await resp.json(); } catch(e){ const txt = await resp.text().catch(()=>null); console.error('removeVoucher invalid JSON', txt); throw e; }
    if (json.success) {
      if (document.getElementById('discount_row')) { document.getElementById('discount_row').classList.add('d-none'); document.getElementById('discount_row').style.display = 'none'; }
      if (document.getElementById('discount_value')) document.getElementById('discount_value').textContent = '';
      if (document.getElementById('after_total')) document.getElementById('after_total').textContent = json.formatted_cart_total;
      if (document.getElementById('cart_total')) document.getElementById('cart_total').textContent = json.formatted_cart_total;
      if (document.getElementById('checkout_total')) document.getElementById('checkout_total').textContent = json.formatted_cart_total;
      var vc = document.getElementById('voucher_code'); if(vc) vc.value = '';
      return;
    } else {
      alert(json.message || 'Không thể bỏ mã');
    }
  } catch(err){
    console.error('removeVoucher error', err);
    var f = document.getElementById('checkout_form');
    if(f){
      var inp = document.createElement('input'); inp.type='hidden'; inp.name='remove_voucher'; inp.value='1'; f.appendChild(inp);
      f.submit();
    } else {
      alert('Không thể bỏ mã (lỗi JS). Vui lòng thử lại.');
    }
  }
}
</script>

<script>
// select a voucher (fill input and show apply/cancel) — user must press Apply to actually apply
function selectVoucher(el, code){
  // el: the clicked voucher button element
  console.log('selectVoucher called, code=', code, 'el=', el);
  var iv = document.getElementById('voucher_code'); if(iv) iv.value = code;
  var sel = document.getElementById('selected_voucher');
  if(!sel){
    return;
  }
  var label = document.getElementById('selected_voucher_code'); if(label) label.textContent = code;
  // show the selection area (remove bootstrap d-none if present)
  sel.style.display = '';
  sel.classList.remove('d-none');

  // visually mark the selected voucher button
  try{
    document.querySelectorAll('.voucher-btn').forEach(function(b){ b.classList.remove('selected-voucher'); });
    if(el && el.classList) el.classList.add('selected-voucher');
  } catch(e){ console.error('selectVoucher highlight error', e); }

  // do not auto-apply — wait for user to press Áp dụng
}

function cancelVoucherSelection(){
  console.log('cancelVoucherSelection called');
  var iv = document.getElementById('voucher_code'); if(iv) iv.value = '';
  var sel = document.getElementById('selected_voucher'); if(sel){ sel.style.display = 'none'; sel.classList.add('d-none'); }
}

// show/hide apply button when user types a manual voucher code
document.addEventListener('DOMContentLoaded', function(){
  try{
    var iv = document.getElementById('voucher_code');
    var applyBtn = document.getElementById('apply_voucher_input_btn');
    function toggleApply(){
      if(!iv || !applyBtn) return;
      if(iv.value && iv.value.trim() !== ''){
        applyBtn.classList.remove('d-none'); applyBtn.style.display = '';
        // hide selected_voucher area if user types manually
        var sel = document.getElementById('selected_voucher'); if(sel){ sel.style.display = 'none'; sel.classList.add('d-none'); }
        document.querySelectorAll('.voucher-btn').forEach(function(b){ b.classList.remove('selected-voucher'); });
      } else {
        applyBtn.classList.add('d-none'); applyBtn.style.display = 'none';
      }
    }
    if(iv){ iv.addEventListener('input', toggleApply); iv.addEventListener('keypress', function(e){ if(e.key === 'Enter'){ e.preventDefault(); toggleApply(); applyVoucher(); }}); }
    // initial run
    toggleApply();
  }catch(e){ console.error('voucher input toggle init error', e); }
});
</script>

<style>
.voucher-btn{transition:all .12s}
.voucher-btn.selected-voucher{border-color:#0d6efd;background-color:#e9f5ff;color:#0d6efd}
/* cart quantity and action layout */
.quantity{display:inline-flex;align-items:center;gap:.4rem;white-space:nowrap}
.qtybtn{width:32px;height:32px;padding:0;border-radius:4px;flex:0 0 32px}
.quantity input[type="number"]{flex:0 0 60px;min-width:48px;text-align:center}
.cart-actions{display:flex;align-items:center;gap:.5rem;flex-wrap:nowrap}
.cart-actions form{margin:0;display:flex;align-items:center;gap:.5rem;flex-wrap:nowrap}
.remove-button{padding:.15rem .35rem; display:inline-block !important}
/* Strong override to ensure delete controls are visible in any theme */
.remove-button, .remove-link-visible {
  display: inline-block !important;
  visibility: visible !important;
  opacity: 1 !important;
  pointer-events: auto !important;
}
.cart-actions{overflow:visible}
.remove-button .fas{margin-right:6px}
</style>

<section class="container">
  <div class="row mt-2 cart-top">
    <div class="col-6">
      <a href="../shop.php"><i class="fas fa-chevron-left me-2"></i>Mua thêm sản phẩm khác</a>
    </div>
    <p class="col-6">Giỏ hàng của bạn</p>
  </div>

  <div class="row gx-2">
    <!-- left side: cart items -->
    <div class="col-lg-6 col-12 mx-auto main-cart mb-lg-0 mb-5">
      <?php
      if (!isset($_SESSION['customer_id'])) {
        echo '<p>Vui lòng <a href="../signin.php">đăng nhập</a> để xem giỏ hàng.</p>';
      } else {
        $customer_id = $_SESSION['customer_id'];
        $select_cart = "SELECT * FROM cart WHERE customer_id = '$customer_id'";
        $run_cart = mysqli_query($conn, $select_cart);
        if (!$run_cart || mysqli_num_rows($run_cart) == 0) {
          include_once('no_cart.php');
        } else {
          // select-all control
          echo "<div class='mb-2 d-flex align-items-center justify-content-between'>";
          echo "<div><input type='checkbox' id='select_all_toggle' checked> <label for='select_all_toggle' class='ms-1'>Chọn tất cả</label></div>";
          echo "<div><button type='button' id='invert_selection' class='btn btn-sm btn-outline-secondary'>Đảo chọn</button></div>";
          echo "</div>";
        
          while ($row_cart = mysqli_fetch_assoc($run_cart)) {
            $product_id = $row_cart['product_id'];
            $color = $row_cart['color'];
            $quantity = (int)$row_cart['quantity'];

            // get color id
            $get_color = "SELECT * FROM product_color WHERE product_color_name = '" . mysqli_real_escape_string($conn, $color) . "' LIMIT 1";
            $run_color = mysqli_query($conn, $get_color);
            $row_color = mysqli_fetch_assoc($run_color);
            $product_color_id = $row_color['product_color_id'] ?? 0;

            // product info
            $get_products = "SELECT * FROM products WHERE product_id = '$product_id' LIMIT 1";
            $run_products = mysqli_query($conn, $get_products);
            $row_products = mysqli_fetch_assoc($run_products);
            $product_name = $row_products['product_name'] ?? 'Sản phẩm';

            // product image/pricing
            $get_products_img = "SELECT * FROM product_img WHERE product_id = '$product_id' AND product_color_id = '$product_color_id' LIMIT 1";
            $run_products_img = mysqli_query($conn, $get_products_img);
            $row_products_img = mysqli_fetch_assoc($run_products_img);
            $product_color_img = $row_products_img['product_color_img'] ?? 'default.png';
            $product_price_des = $row_products_img['product_price_des'] ?? 0;
            $product_price_des_format = function_exists('currency_format') ? currency_format($product_price_des) : number_format($product_price_des);
            $product_price = $row_products_img['product_price'] ?? 0;
            $product_price_format = function_exists('currency_format') ? currency_format($product_price) : number_format($product_price);
            ?>
            <div class="card p-4 mb-3">
              <div class="row">
                <div class="col-md-2 col-11 mx-auto justify-content-center product-img">
                  <!-- checkbox to select this item for checkout -->
                  <div class="form-check mb-2 text-center">
                    <input class="form-check-input select-item" type="checkbox" checked
                           name="selected_items[]"
                           value="<?php echo intval($product_id) . '||' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>"
                           form="checkout_form"
                           data-price="<?php echo (int)$product_price_des; ?>"
                           data-qty="<?php echo intval($quantity); ?>"
                    >
                  </div>
                  <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank">
                    <img src="../administrator/product_img/<?php echo htmlspecialchars($product_color_img); ?>" class="img-fluid" alt="">
                  </a>
                  <!-- remove form placed under image for better layout -->
                  <form action="" method="post" class="mt-2 remove-form text-center">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="color" value="<?php echo htmlspecialchars($color); ?>">
                    <input type="hidden" name="remove_cart" value="remove">
                    <button type="button" class="remove-button btn btn-outline-danger btn-sm" onclick="submitRemove(this.closest('form'))"><i class="fas fa-trash-alt"></i> Xóa</button>
                  </form>
                </div>
                <div class="col-md-10 col-11 mx-auto px-4">
                  <div class="row">
                    <div class="col-8 card-title">
                      <a href="../shop-detail.php?product_id=<?php echo $product_id; ?>&color=<?php echo $product_color_id ?>" target="_blank" class="mb-4 fw-bold product-name"><?php echo htmlspecialchars($product_name); ?></a>
                      <span class="mb-4 d-block color">Màu: <?php echo htmlspecialchars($color); ?></span>
                    </div>
                    <div class="col-4">
                      <p class="item-price">
                        <?php
                        if ($product_price_des == $product_price) {
                          echo "<b class='d-block'>$product_price_des_format</b>";
                        } else {
                          echo "<b class='d-block'>$product_price_des_format</b> <strike class='d-block'>$product_price_format</strike>";
                        }
                        ?>
                      </p>
                      <div class="cart-actions">
                        <!-- update quantity form (separate) -->
                        <form action="" method="post" class="d-flex align-items-center mb-0 update-form">
                          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                          <input type="hidden" name="color" value="<?php echo htmlspecialchars($color); ?>">
                          <div class="quantity">
                            <button type="button" class="dec qtybtn btn btn-outline-secondary" onclick="decreaseCount(event,this); this.closest('form').submit();">-</button>
                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1">
                            <button type="button" class="inc qtybtn btn btn-outline-secondary" onclick="increaseCount(event,this); this.closest('form').submit();">+</button>
                          </div>
                          <input type="hidden" name="update_cart" value="update">
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          } // end while cart
        } // end else cart not empty
      } // end if session

      // update cart handler
      if (isset($_POST['update_cart']) && $_POST['update_cart'] === 'update') {
        $remove_id = $_POST['product_id'] ?? '';
        $remove_color = $_POST['color'] ?? '';
        $quantity_new = (int)($_POST['quantity'] ?? 1);
        $get_color = "SELECT * FROM product_color WHERE product_color_name = '" . mysqli_real_escape_string($conn, $remove_color) . "' LIMIT 1";
        $run_color = mysqli_query($conn, $get_color);
        $row_color = mysqli_fetch_assoc($run_color);
        $product_color_id = $row_color['product_color_id'] ?? 0;
        $select_quantity = "SELECT * FROM product_img WHERE product_id='$remove_id' AND product_color_id='$product_color_id' LIMIT 1";
        $run_select_quantity = mysqli_query($conn, $select_quantity);
        $row_select_quantity = mysqli_fetch_assoc($run_select_quantity);
        $product_quantity = (int)($row_select_quantity['product_quantity'] ?? 0);
        if ($quantity_new > $product_quantity && $product_quantity > 0) {
          echo "<script>alert('Số lượng sản phẩm đã vượt quá số lượng cho phép')</script>";
        } else {
          $update_cart_q = "UPDATE cart SET quantity = '$quantity_new' WHERE product_id = '$remove_id' AND color = '" . mysqli_real_escape_string($conn, $remove_color) . "'";
          mysqli_query($conn, $update_cart_q);
          echo "<script>window.open('cart.php', '_self')</script>";
        }
      }

      // remove handler
      if (isset($_POST['remove_cart']) && $_POST['remove_cart'] === 'remove') {
        $remove_id = $_POST['product_id'] ?? '';
        $remove_color = $_POST['color'] ?? '';
        $delete_product = "DELETE FROM cart WHERE product_id = '" . mysqli_real_escape_string($conn, $remove_id) . "' AND color = '" . mysqli_real_escape_string($conn, $remove_color) . "'";
        mysqli_query($conn, $delete_product);
        echo "<script>window.open('cart.php', '_self')</script>";
      }
      ?>

      <?php
      $cart_total = function_exists('total_price') ? total_price() : 0;
      $applied_discount = $_SESSION['applied_discount_value'] ?? 0;
      $applied_code = $_SESSION['applied_voucher'] ?? null;
      $applied_total_after = $_SESSION['applied_total_after'] ?? null;
      ?>
      <div class="card p-4 mb-4">
        <div class="row">
          <p class="col-6">Tạm tính (<?php echo function_exists('items') ? items() : 0; ?> sản phẩm): </p>
          <span id="cart_total" class="col-6 price-total"><?php echo function_exists('currency_format') ? currency_format($cart_total) : number_format($cart_total); ?></span>
        </div>
        <?php if ($applied_discount > 0): ?>
        <div id="discount_row" class="row mt-2">
          <p class="col-6 small text-success">Đã áp dụng: <?php echo htmlspecialchars($applied_code); ?></p>
          <span id="discount_value" class="col-6 small text-success">- <?php echo function_exists('currency_format') ? currency_format($applied_discount) : number_format($applied_discount); ?></span>
        </div>
        <div class="row">
          <p class="col-6"><strong>Sau giảm:</strong></p>
          <span id="after_total" class="col-6 price-total"><strong><?php echo function_exists('currency_format') ? currency_format($applied_total_after) : number_format($applied_total_after); ?></strong></span>
        </div>
        <?php else: ?>
        <div id="discount_row" class="row mt-2 d-none" style="display:none"></div>
        <?php endif; ?>
      </div>
    </div>

    <!-- right side: checkout -->
    <div class="col-lg-6 col-12 mx-auto mb-lg-0 mb-5 right-side">
      <div class="card p-4">
  <form id="checkout_form" action="" method="post">
          <h6>Thông tin khách hàng</h6>
          <?php
          $customer_name = $customer_phone = $customer_sex = '';
          if (isset($_SESSION['customer_id'])) {
            $customer_id = $_SESSION['customer_id'];
            $select_customer = "SELECT * FROM customer WHERE customer_id = '$customer_id' LIMIT 1";
            $run_customer = mysqli_query($conn, $select_customer);
            if ($row_customer = mysqli_fetch_assoc($run_customer)) {
              $customer_name = $row_customer['customer_name'] ?? '';
              $customer_phone = $row_customer['customer_phone'] ?? '';
              $customer_sex = $row_customer['customer_sex'] ?? '';
            }
          }
          ?>
          <div class="row mb-3">
            <div class="col-lg-6 col-12 mb-2">
              <input type="text" class="form-control" placeholder="Họ tên" value="<?php echo htmlspecialchars($customer_name); ?>" name="receiver">
            </div>
            <div class="col-lg-6 col-12">
              <input type="text" class="form-control" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($customer_phone); ?>" name="receiver_phone">
            </div>
          </div>

          <h6>Chọn địa chỉ nhận hàng</h6>
          <div class="card card-body m-2">
            <div class="d-flex gap-2 mb-2">
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addrCollapse">Chọn địa chỉ đã lưu</button>
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addAddrCollapse">Thêm địa chỉ mới</button>
            </div>

            <div class="collapse" id="addrCollapse">
              <?php
              // list saved addresses (inside collapse)
              if (isset($customer_id)) {
                $addr_q = mysqli_query($conn, "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' ORDER BY is_default DESC, address_id DESC");
                if ($addr_q && mysqli_num_rows($addr_q) > 0) {
                  echo "<div class='list-group mb-2'>";
                  while ($a = mysqli_fetch_assoc($addr_q)) {
                    $aid = (int)$a['address_id'];
                    $rec = htmlspecialchars($a['receiver_name'] ?: $customer_name);
                    $ph = htmlspecialchars($a['phone'] ?: $customer_phone);
                    $det = htmlspecialchars($a['address_detail']);
                    $checked = $a['is_default'] ? 'checked' : '';
                    echo "<label class='list-group-item'><input type='radio' name='selected_address' value='$aid' class='form-check-input me-2' $checked><strong>$rec</strong> — $ph<br><small>$det</small>";
                    if ($a['is_default']) echo " <span class='badge bg-success ms-2'>Mặc định</span>";
                    echo "</label>";
                  }
                  echo "</div>";
                  echo "<div class='mb-2'><button type='submit' name='use_saved_address' class='btn btn-outline-primary btn-sm'>Sử dụng địa chỉ đã chọn</button></div>";
                } else {
                  echo "<p class='text-muted'>Bạn chưa có địa chỉ lưu nào.</p>";
                }
              }
              ?>
            </div>

            <div class="collapse" id="addAddrCollapse">
              <div class="mt-2">
                <label class="form-label">Nhập địa chỉ mới</label>
                <input type="text" class="form-control mb-2" name="new_address" placeholder="Địa chỉ mới">
                <div class="row mb-2">
                  <div class="col-6"><input type="text" name="new_receiver" class="form-control" placeholder="Người nhận (tùy chọn)"></div>
                  <div class="col-6"><input type="text" name="new_phone" class="form-control" placeholder="SĐT (tùy chọn)"></div>
                </div>
                <div><button type="submit" name="add_address" class="btn btn-outline-success btn-sm">Lưu địa chỉ</button></div>
              </div>
            </div>

            <?php
            // prefill delivery input from session if user just selected a saved address
            $delivery_pref = $_SESSION['prefill_delivery'] ?? ($customer_address ?? '');
            if (isset($_SESSION['prefill_delivery'])) unset($_SESSION['prefill_delivery']);
            ?>
            <div class="mt-2">
              <label class="form-label">Hoặc nhập địa chỉ giao (nhập tay)</label>
              <input type="text" class="form-control" name="delivery_location" placeholder="Địa chỉ nhận hàng" value="<?php echo htmlspecialchars($delivery_pref); ?>">
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="form-label fw-bold">Nhập mã giảm giá</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="text" class="form-control" name="voucher_code" id="voucher_code" placeholder="Nhập mã..." value="<?php echo isset($_SESSION['applied_voucher']) ? htmlspecialchars($_SESSION['applied_voucher']) : ''; ?>">
              <!-- Apply button for manual code entry (hidden until user types) -->
              <button type="button" id="apply_voucher_input_btn" class="btn btn-primary btn-sm d-none" onclick="applyVoucher()">Áp dụng</button>
              <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#voucherCollapse">Chọn mã</button>
              <?php if (isset($_SESSION['applied_voucher'])): ?>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVoucher()">Xóa mã</button>
              <?php endif; ?>
            </div>
            <div id="selected_voucher" class="mt-2" style="display:none">
              <div class="d-flex gap-2 align-items-center">
                <div><strong>Mã đã chọn:</strong> <span id="selected_voucher_code"></span></div>
                <div><button type="button" class="btn btn-primary btn-sm" onclick="applyVoucher()">Áp dụng</button></div>
                <div><button type="button" class="btn btn-secondary btn-sm" onclick="cancelVoucherSelection()">Hủy</button></div>
              </div>
            </div>
            <div class="collapse mt-2" id="voucherCollapse">
            <?php
            $today = date('Y-m-d');
            // Only show vouchers that are active, within date range, have remaining quantity and
            // are either global (allowed_customer_id IS NULL) or specifically assigned to this customer.
            $customer_filter = isset($_SESSION['customer_id']) ? "(allowed_customer_id IS NULL OR allowed_customer_id = '" . intval($_SESSION['customer_id']) . "')" : "(allowed_customer_id IS NULL)";
            $vq = @mysqli_query($conn, "SELECT * FROM vouchers WHERE status='active' AND (start_date IS NULL OR start_date <= '$today') AND (end_date IS NULL OR end_date >= '$today') AND (quantity IS NULL OR quantity > 0) AND $customer_filter ORDER BY voucher_id DESC LIMIT 12");
            if ($vq && mysqli_num_rows($vq) > 0) {
              echo "<div class='mt-2 d-flex gap-2 flex-wrap'>";
              while ($vv = mysqli_fetch_assoc($vq)) {
                $raw_code = $vv['code'];
                $code = htmlspecialchars($raw_code);
                $label = ($vv['discount_percent'] ? $vv['discount_percent'] . '%' : number_format($vv['discount_amount']) . 'đ');
                echo '<button type="button" class="btn btn-outline-secondary btn-sm voucher-btn" onclick="selectVoucher(this,' . json_encode($raw_code) . ')">' . $code . ' <small class="text-muted">(' . $label . ')</small></button>';
              }
              echo "</div>";
            } else {
              echo "<p class='text-muted'>Không có mã giảm giá khả dụng.</p>";
            }
            ?>
            </div>
          </div>

          <input type="hidden" name="total_price" value="<?php echo function_exists('total_price') ? total_price() : 0; ?>">

          <div class="form-check mt-2 mb-4">
            <input class="form-check-input" type="checkbox" id="check1" name="call_receiver_new" value="Gọi người khác" data-bs-toggle="collapse" data-bs-target="#collapse2">
            <label class="form-check-label">Gọi người khác nhận hàng (nếu có)</label>
            <div class="collapse" id="collapse2">
              <div class="card card-body m-2">
                <p>Thông tin người nhận</p>
                <div class="row mb-3">
                  <div class="col-lg-6 col-12 mb-2"><input type="text" class="form-control" placeholder="Họ tên" name="receiver_new"></div>
                  <div class="col-lg-6 col-12"><input type="text" class="form-control" placeholder="Số điện thoại" name="receiver_phone_new"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <p class="col-6">Tổng tiền: </p>
            <span id="checkout_total" class="col-6 price-total fw-bold"><?php echo function_exists('currency_format') ? currency_format($applied_total_after ?? $cart_total) : number_format($applied_total_after ?? $cart_total); ?></span>
          </div>

          <div class="mt-3">
            <button type="submit" class="submit-order mb-3 btn btn-primary" name="order"><b>Đặt hàng</b></button>
          </div>
        </form>
        <script>
          // Recalculate displayed totals based on checked items
          (function(){
            function recalc(){
              try{
                var items = document.querySelectorAll('.select-item');
                var subtotal = 0;
                items.forEach(function(cb){
                  if(cb.checked){
                    var price = parseInt(cb.getAttribute('data-price')||0,10);
                    var qty = parseInt(cb.getAttribute('data-qty')||0,10);
                    subtotal += (price * (isNaN(qty)?1:qty));
                  }
                });
                // format number as currency (simple fallback)
                function fmt(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '₫'; }
                var cartTotalEl = document.getElementById('cart_total');
                var checkoutTotalEl = document.getElementById('checkout_total');
                var afterTotalEl = document.getElementById('after_total');

                // update subtotal display
                if(cartTotalEl) cartTotalEl.textContent = fmt(subtotal);

                // If a discount is currently shown, try to respect it by subtracting the displayed discount value
                var discountEl = document.getElementById('discount_value');
                var displayedDiscount = 0;
                if (discountEl && discountEl.textContent) {
                  // discount text like "- 1.000₫" or similar; extract digits
                  var digits = discountEl.textContent.replace(/[^0-9]/g,'');
                  displayedDiscount = digits ? parseInt(digits,10) : 0;
                }

                var after = subtotal - displayedDiscount;
                if (after < 0) after = 0;

                if(checkoutTotalEl) checkoutTotalEl.textContent = fmt(after);
                if(afterTotalEl) afterTotalEl.textContent = fmt(after);

                var hiddenTotal = document.querySelector('form#checkout_form input[name="total_price"]');
                if(hiddenTotal) hiddenTotal.value = subtotal;
              }catch(e){ console.error('recalc error', e); }
            }
            document.addEventListener('change', function(e){ if(e.target && e.target.classList && e.target.classList.contains('select-item')) recalc(); });
            // select all toggle
            var selectAllEl = document.getElementById('select_all_toggle');
            if(selectAllEl){
              selectAllEl.addEventListener('change', function(){
                var all = document.querySelectorAll('.select-item');
                all.forEach(function(cb){ cb.checked = selectAllEl.checked; });
                recalc();
              });
            }
            // invert selection button
            var invertBtn = document.getElementById('invert_selection');
            if(invertBtn){
              invertBtn.addEventListener('click', function(){
                var all = document.querySelectorAll('.select-item');
                var anyChecked = false; var allChecked = true;
                all.forEach(function(cb){ cb.checked = !cb.checked; if(cb.checked) anyChecked = true; if(!cb.checked) allChecked = false; });
                if(selectAllEl) selectAllEl.checked = allChecked;
                recalc();
              });
            }
            // initial run
            document.addEventListener('DOMContentLoaded', recalc);
            // also run immediately in case DOMContentLoaded already fired
            setTimeout(recalc,200);
          })();
        </script>
      </div>
    </div>
  </div>
</section>

<?php
// handle address add
if (isset($_POST['add_address']) && !empty(trim($_POST['new_address'])) && isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
  $new_addr = mysqli_real_escape_string($conn, trim($_POST['new_address']));
  $receiver_name = mysqli_real_escape_string($conn, trim($_POST['new_receiver'] ?? ''));
  $phone = mysqli_real_escape_string($conn, trim($_POST['new_phone'] ?? ''));
  $insert_addr = "INSERT INTO customer_addresses (customer_id, receiver_name, phone, address_detail, is_default) VALUES ('$customer_id', '$receiver_name', '$phone', '$new_addr', 0)";
  mysqli_query($conn, $insert_addr);
  echo "<script>window.open('cart.php','_self')</script>";
  exit;
}


// handle apply voucher (store discount in session and reload to show updated totals)
if (isset($_POST['apply_voucher']) && !empty(trim($_POST['voucher_code'])) && isset($_SESSION['customer_id'])) {
  $voucher_code = trim($_POST['voucher_code']);
  $voucher_code_esc = mysqli_real_escape_string($conn, $voucher_code);
  $today = date('Y-m-d');
  // Normalize matching for manual form submission as well
  $sql_voucher = "SELECT * FROM vouchers WHERE TRIM(LOWER(code)) = LOWER('$voucher_code_esc') AND status = 'active' AND (quantity IS NULL OR quantity > 0) AND (start_date IS NULL OR DATE(start_date) <= '$today') AND (end_date IS NULL OR DATE(end_date) >= '$today') LIMIT 1";
  $run_voucher = mysqli_query($conn, $sql_voucher);
  if ($run_voucher && mysqli_num_rows($run_voucher) > 0) {
    $row_voucher = mysqli_fetch_assoc($run_voucher);
    $discount_percent = (int)$row_voucher['discount_percent'];
    $discount_amount = (int)$row_voucher['discount_amount'];
    $min_order = (int)$row_voucher['min_order'];
    $max_discount = (int)$row_voucher['max_discount'];
    $total_price = function_exists('total_price') ? total_price() : 0;
    if ($total_price >= $min_order) {
      $discount_value = 0;
      if ($discount_percent > 0) {
        $discount_value = (int) floor($total_price * $discount_percent / 100);
        if ($max_discount > 0 && $discount_value > $max_discount) $discount_value = $max_discount;
      } elseif ($discount_amount > 0) {
        $discount_value = $discount_amount;
      }
      if ($discount_value > $total_price) $discount_value = $total_price;
      $_SESSION['applied_voucher'] = $voucher_code;
      $_SESSION['applied_voucher_id'] = (int)$row_voucher['voucher_id'];
      $_SESSION['applied_discount_value'] = $discount_value;
      $_SESSION['applied_total_after'] = $total_price - $discount_value;
    } else {
      echo "<script>alert('Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá này.');</script>";
    }
  } else {
    echo "<script>alert('Mã giảm giá không tồn tại hoặc đã hết hạn / hết lượt sử dụng.');</script>";
  }
  echo "<script>window.open('cart.php','_self')</script>";
  exit;
}


// handle remove voucher (clear session-applied voucher)
if (isset($_POST['remove_voucher']) && isset($_SESSION['customer_id'])) {
  unset($_SESSION['applied_voucher']);
  unset($_SESSION['applied_voucher_id']);
  unset($_SESSION['applied_discount_value']);
  unset($_SESSION['applied_total_after']);
  echo "<script>window.open('cart.php','_self')</script>";
  exit;
}

// handle use saved address (prefill) - reloads page
if (isset($_POST['use_saved_address']) && !empty($_POST['selected_address']) && isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
  $sel_pref = intval($_POST['selected_address']);
  $res_pref = mysqli_query($conn, "SELECT address_detail FROM customer_addresses WHERE address_id = '$sel_pref' AND customer_id = '$customer_id' LIMIT 1");
  if ($res_pref && mysqli_num_rows($res_pref) > 0) {
    $row_pref = mysqli_fetch_assoc($res_pref);
    $_SESSION['prefill_delivery'] = $row_pref['address_detail'];
  }
  echo "<script>window.open('cart.php','_self')</script>";
  exit;
}

// process order
if (isset($_POST['order']) && isset($_SESSION['customer_id'])) {
  $customer_id = $_SESSION['customer_id'];
  $order_no = mt_rand();
  $status = 'Đang chờ';

  // receiver
  if (!isset($_POST['call_receiver_new'])) {
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver'] ?? '');
    $receiver_phone = mysqli_real_escape_string($conn, $_POST['receiver_phone'] ?? '');
  } else {
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver_new'] ?? '');
    $receiver_phone = mysqli_real_escape_string($conn, $_POST['receiver_phone_new'] ?? '');
  }

  // delivery location
  if (!empty($_POST['selected_address'])) {
    $sel_addr_id = intval($_POST['selected_address']);
    $res_addr = mysqli_query($conn, "SELECT address_detail FROM customer_addresses WHERE address_id = '$sel_addr_id' AND customer_id = '$customer_id' LIMIT 1");
    if ($res_addr && mysqli_num_rows($res_addr) > 0) {
      $row_addr = mysqli_fetch_assoc($res_addr);
      $delivery_location = $row_addr['address_detail'];
    } else {
      $delivery_location = mysqli_real_escape_string($conn, $_POST['delivery_location'] ?? '');
    }
  } else {
    $delivery_location = mysqli_real_escape_string($conn, $_POST['delivery_location'] ?? '');
  }

  $total_price = isset($_POST['total_price']) ? (int)$_POST['total_price'] : (function_exists('total_price') ? total_price() : 0);

  // voucher handling (basic)
  $voucher_code = trim($_POST['voucher_code'] ?? '');
  $discount_value = 0;
  $total_after = $total_price;
  if ($voucher_code !== '') {
    $voucher_code_esc = mysqli_real_escape_string($conn, $voucher_code);
    $today = date('Y-m-d');
  $customer_filter = isset($customer_id) ? "(allowed_customer_id IS NULL OR allowed_customer_id = '".intval($customer_id)."')" : "(allowed_customer_id IS NULL)";
  $sql_voucher = "SELECT * FROM vouchers WHERE code = '$voucher_code_esc' AND status = 'active' AND $customer_filter AND quantity > 0 AND (start_date IS NULL OR start_date <= '$today') AND (end_date IS NULL OR end_date >= '$today') LIMIT 1";
    $run_voucher = mysqli_query($conn, $sql_voucher);
    if ($run_voucher && mysqli_num_rows($run_voucher) > 0) {
      $row_voucher = mysqli_fetch_assoc($run_voucher);
      $discount_percent = (int)$row_voucher['discount_percent'];
      $discount_amount = (int)$row_voucher['discount_amount'];
      $min_order = (int)$row_voucher['min_order'];
      $max_discount = (int)$row_voucher['max_discount'];
      if ($total_price >= $min_order) {
        if ($discount_percent > 0) {
          $discount_value = (int) floor($total_price * $discount_percent / 100);
          if ($max_discount > 0 && $discount_value > $max_discount) $discount_value = $max_discount;
        } elseif ($discount_amount > 0) {
          $discount_value = $discount_amount;
        }
        if ($discount_value > $total_price) $discount_value = $total_price;
        $total_after = $total_price - $discount_value;
        // decrement voucher
        mysqli_query($conn, "UPDATE vouchers SET quantity = quantity - 1 WHERE voucher_id = " . (int)$row_voucher['voucher_id']);
      } else {
        echo "<script>alert('Đơn hàng chưa đạt giá trị tối thiểu để sử dụng mã giảm giá này.');</script>";
      }
    } else {
      echo "<script>alert('Mã giảm giá không tồn tại hoặc đã hết hạn / hết lượt sử dụng.');</script>";
    }
  }

  // insert order
  $insert_customer_order = "INSERT INTO customer_orders (customer_id, order_date, total_price, status, order_no, receiver, receiver_phone, delivery_location) VALUES ('$customer_id', NOW(), '$total_after', '$status', '$order_no', '" . mysqli_real_escape_string($conn, $receiver) . "', '" . mysqli_real_escape_string($conn, $receiver_phone) . "', '" . mysqli_real_escape_string($conn, $delivery_location) . "')";
  $run_customer_order = mysqli_query($conn, $insert_customer_order);
  if ($run_customer_order) {
    $order_id = mysqli_insert_id($conn);
    // Determine which cart items to include: if selected_items[] submitted, honor it; otherwise include all
    $selected = isset($_POST['selected_items']) && is_array($_POST['selected_items']) ? $_POST['selected_items'] : null;
    // normalize selected keys into an associative map for quick lookup
    $selected_map = null;
    if ($selected) {
      $selected_map = array();
      foreach ($selected as $s) {
        // expected format: product_id||color
        $parts = explode('||', $s);
        if (count($parts) >= 2) {
          $pid = intval($parts[0]);
          $col = mysqli_real_escape_string($conn, $parts[1]);
          $selected_map[$pid . '||' . $col] = true;
        }
      }
    }
    // fetch cart rows for this customer
    $select_cart = "SELECT * FROM cart WHERE customer_id = '$customer_id'";
    $run_cart = mysqli_query($conn, $select_cart);

    // Runtime check: some installations may not have the `unit_price` column yet.
    // Detect and adapt so we don't throw an SQL error. Prefer running the migration
    // `database/migrate_add_unit_price_to_order_products.php` to persist the column.
    $res_col = mysqli_query($conn, "SHOW COLUMNS FROM customer_order_products LIKE 'unit_price'");
    $has_unit_price = ($res_col && mysqli_num_rows($res_col) > 0);

    while ($row_cart = mysqli_fetch_assoc($run_cart)) {
      $product_id = $row_cart['product_id'];
      $color = mysqli_real_escape_string($conn, $row_cart['color']);
      $quantity = (int)$row_cart['quantity'];

      // if selection provided, skip items not selected
      if ($selected_map !== null) {
        if (!isset($selected_map[$product_id . '||' . $color])) {
          continue;
        }
      }

      // Fetch current unit price from product_img (prefer matching color)
      $unit_price = 0;
      $res_price = mysqli_query($conn, "SELECT product_price FROM product_img WHERE product_id = '" . (int)$product_id . "' AND product_color_id = (SELECT product_color_id FROM product_color WHERE product_color_name = '" . mysqli_real_escape_string($conn, $color) . "' LIMIT 1) LIMIT 1");
      if ($res_price && mysqli_num_rows($res_price) > 0) {
        $rp = mysqli_fetch_assoc($res_price);
        $unit_price = (int)$rp['product_price'];
      } else {
        $res_price_fallback = mysqli_query($conn, "SELECT product_price FROM product_img WHERE product_id = '" . (int)$product_id . "' LIMIT 1");
        if ($res_price_fallback && mysqli_num_rows($res_price_fallback) > 0) {
          $rp = mysqli_fetch_assoc($res_price_fallback);
          $unit_price = (int)$rp['product_price'];
        }
      }
      // Insert with or without unit_price depending on schema
      if ($has_unit_price) {
        $insert_prod = "INSERT INTO customer_order_products (order_id, product_id, color, quantity, unit_price) VALUES ('$order_id', '$product_id', '$color', '$quantity', '$unit_price')";
      } else {
        $insert_prod = "INSERT INTO customer_order_products (order_id, product_id, color, quantity) VALUES ('$order_id', '$product_id', '$color', '$quantity')";
      }
      mysqli_query($conn, $insert_prod);
      // remove this item from cart only if it was included in the order
      mysqli_query($conn, "DELETE FROM cart WHERE customer_id = '$customer_id' AND product_id = '" . intval($product_id) . "' AND color = '" . mysqli_real_escape_string($conn, $color) . "'");
    }
    // Note: remaining cart items (not selected) will stay in cart
    echo "<script>window.open('order_success.php?customer_id=$customer_id&order_id=$order_id','_self')</script>";
    exit;
  }
}
?>
