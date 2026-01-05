<?php
require_once __DIR__ . '/../includes/auth.php';
checkAdminLogin();
include __DIR__ . '/../includes/database.php';

// simple admin debug page to inspect notifications table
$filter_customer = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$filter_related  = isset($_GET['related_id']) ? intval($_GET['related_id']) : 0;

// get columns
$cols = [];
$cres = @mysqli_query($conn, "SHOW COLUMNS FROM `notifications`");
if($cres){ while($c = mysqli_fetch_assoc($cres)){ $cols[] = $c['Field']; } }

$where = array();
if($filter_customer){
    // check either user_id or customer_id
    $where[] = "(user_id = $filter_customer OR customer_id = $filter_customer)";
}
if($filter_related){
    $where[] = "(related_id = $filter_related)";
}
$where_sql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT * FROM notifications $where_sql ORDER BY created_at DESC LIMIT 0,200";
$res = @mysqli_query($conn, $sql);

?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Debug Notifications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h3 class="mb-3">Debug: Notifications table</h3>
    <p class="small text-muted">Use this page to inspect columns and recent rows. Temporary admin diagnostic tool.</p>

    <div class="card mb-3">
      <div class="card-body">
        <form class="row g-2" method="get">
          <div class="col-auto">
            <label class="form-label">Customer ID</label>
            <input type="number" name="customer_id" class="form-control" value="<?php echo htmlspecialchars($filter_customer); ?>">
          </div>
          <div class="col-auto">
            <label class="form-label">Related ID (voucher/order)</label>
            <input type="number" name="related_id" class="form-control" value="<?php echo htmlspecialchars($filter_related); ?>">
          </div>
          <div class="col-auto align-self-end">
            <button class="btn btn-primary">Filter</button>
            <a class="btn btn-secondary" href="debug_notifications.php">Clear</a>
          </div>
        </form>
      </div>
    </div>

    <div class="mb-3">
      <h5>Columns</h5>
      <?php if(count($cols)): ?>
        <pre><?php echo htmlspecialchars(implode(', ', $cols)); ?></pre>
      <?php else: ?>
        <div class="alert alert-warning">Could not read columns or table does not exist.</div>
      <?php endif; ?>
    </div>

    <div>
      <h5>Recent rows (limit 200)</h5>
      <?php if($res && mysqli_num_rows($res) > 0): ?>
        <div class="table-responsive">
          <table class="table table-sm table-bordered">
            <thead>
              <tr>
                <?php foreach($cols as $c): ?><th><?php echo htmlspecialchars($c); ?></th><?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <?php while($r = mysqli_fetch_assoc($res)): ?>
                <tr>
                  <?php foreach($cols as $c): ?>
                    <td style="max-width:300px; white-space:normal; word-break:break-word"><?php echo nl2br(htmlspecialchars(isset($r[$c]) ? $r[$c] : '')); ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No rows found<?php echo ($res ? '' : ' (query error: '.htmlspecialchars(mysqli_error($conn)).')'); ?></div>
      <?php endif; ?>
    </div>

    <div class="mt-4">
      <a href="index.php?voucher_list" class="btn btn-secondary">Back to vouchers</a>
    </div>
  </div>
</body>
</html>
<?php
?>