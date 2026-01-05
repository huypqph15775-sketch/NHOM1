<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử giao dịch - Phone Store</title>
    <link rel="icon" href="../images/smartphone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/myaccount.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>
<body>
    <?php include("includes/header.php"); ?>

    <?php
        if(!isset($_SESSION['customer_id'])){
            echo "<script>window.open('../signin.php', '_self')</script>";
            exit;
        }
        $customer_id = $_SESSION['customer_id'];
    ?>

    <section class="container" style="min-height:300px;">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Trang chủ</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Lịch sử giao dịch</li>
                    </ol>
                </nav>

                <h4 class="mb-3">Lịch sử giao dịch của bạn</h4>

                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Đơn hàng</th>
                            <th>Ngày</th>
                            <th>Số tiền</th>
                            <th>Hình thức thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Mã vận đơn</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $get_orders = "SELECT * FROM customer_orders WHERE customer_id = '$customer_id' ORDER BY order_id DESC";
                            $run_orders = mysqli_query($conn, $get_orders);
                            if(mysqli_num_rows($run_orders) == 0){
                                echo "<tr><td colspan='7' class='text-center'>Bạn chưa có giao dịch nào.</td></tr>";
                            } else {
                                while($row = mysqli_fetch_array($run_orders)){
                                    $order_id = $row['order_id'];
                                    // choose displayed date: use received_date for delivered orders when available
                                    $order_date_raw = $row['order_date'];
                                    if(strpos($row['status'], 'Đã giao') !== false && !empty($row['received_date'])){
                                        $order_date_raw = $row['received_date'];
                                    }
                                    $order_date = '';
                                    if(!empty($order_date_raw) && strtotime($order_date_raw) !== false){
                                        $order_date = date('d/m/Y H:i', strtotime($order_date_raw));
                                    } else {
                                        $order_date = $order_date_raw;
                                    }
                                    $total_price = $row['total_price'];
                                    $total_format = currency_format($total_price);
                                    $payment_type = $row['payment_type'];
                                    $status = $row['status'];
                                    $tracking = isset($row['tracking_code']) ? $row['tracking_code'] : '';
                                    echo "<tr>";
                                    echo "<td>#".htmlspecialchars($order_id)."</td>";
                                    echo "<td>".htmlspecialchars($order_date)."</td>";
                                    echo "<td class='text-danger'>".htmlspecialchars($total_format)."</td>";
                                    echo "<td>".htmlspecialchars($payment_type)."</td>";
                                    // status badge
                                    $badge_class = 'secondary';
                                    if(strpos($status, 'Đang') !== false) $badge_class = 'primary';
                                    if(strpos($status, 'Đã giao') !== false) $badge_class = 'success';
                                    if(strpos($status, 'Không được xác nhận') !== false || strpos($status, 'Đã hủy') !== false) $badge_class = 'danger';
                                    echo "<td><span class='badge bg-".$badge_class."'>".htmlspecialchars($status)."</span></td>";
                                    echo "<td>".htmlspecialchars($tracking)."</td>";
                                    echo "<td class='text-end'><a href='order_detail.php?order_id=".urlencode($order_id)."' class='btn btn-sm btn-outline-primary'>Xem</a></td>";
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </section>

    <?php include("../includes/footer.php"); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
