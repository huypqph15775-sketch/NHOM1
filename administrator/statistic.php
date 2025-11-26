
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Thống kê</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                
                <div class="row mt-3">
                    <div class="col-8 border border-primary mx-3">
                        <b>Hoạt động</b>
                        <hr class="dropdown-divider text-primary">
                        <form action="" method="post">
                            <button name="filter_all" class="btn btn-primary mb-3">Tổng doanh thu</button>
                            <button name="filter_now" class="btn btn-primary mb-3">Ngày hôm nay</button>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <input type="date" name="date_start" class="form-control" id="">
                                </div>
                                <div class="col-1">
                                    <input type="button" disabled="disabled" class="form-control" value="đến"></input>
                                </div>
                                <div class="col-3">
                                    <input type="date" name="date_end" class="form-control" id="">
                                </div>
                                <div class="col-1">
                                    <button name="filter1" class="btn btn-primary">Duyệt</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <input type="text" name="day" class="form-control" placeholder="Ngày" id="">
                                </div>
                                <div class="col-2">
                                    <input type="text" name="month" class="form-control" placeholder="Tháng" id="">
                                </div>
                                <div class="col-2">
                                    <input type="text" name="year" class="form-control" placeholder="Năm" id="">
                                </div>
                                <div class="col-1">
                                </div>
                                <div class="col-1">
                                    <button name="filter2" class="btn btn-primary">Duyệt</button>
                                </div>
                            </div>
                        </form>
                        <?php
                            $date = date("Y/m/d");
                            if(isset($_POST['filter_all'])){
                                $select_order = "select * from customer_orders";
                            }
                            else if(isset($_POST['filter_now'])){
                                $select_order = "select * from customer_orders where received_date='$date'";
                            }
                            else if(isset($_POST['filter1'])){
                                $date_start=$_POST['date_start'];
                                $date_end=$_POST['date_end'];
                                $select_order = "select * from customer_orders where received_date between '$date_start' and '$date_end'";
                            }
                            else if(isset($_POST['filter2'])){
                                $day = $_POST['day'];
                                $month = $_POST['month'];
                                $year = $_POST['year'];
                                if(empty($day)&&empty($month)){
                                    $select_order = "select * from customer_orders where YEAR(received_date)='$year'";
                                }
                                else if(empty($day)){
                                    $select_order = "select * from customer_orders where YEAR(received_date)='$year' and MONTH(received_date)='$month'";
                                }
                                else{
                                    $select_order = "select * from customer_orders where YEAR(received_date)='$year' and MONTH(received_date)='$month' and DAY(received_date)='$day'";
                                }
                            }
                            else{
                                $select_order = "select * from customer_orders";
                            }
                            $run_select = mysqli_query($conn, $select_order);
                            $count_select = mysqli_num_rows($run_select);
                            $count_order_price = 0;
                            $count_order_quantity = 0;
                            while($row_select = mysqli_fetch_array($run_select)){
                                $order_id = $row_select['order_id'];
                                $count_order_price += $row_select['total_price'];
                                $select_order_quantity = "select * from customer_order_products where order_id = '$order_id'";
                                $run_order_quantity = mysqli_query($conn, $select_order_quantity);
                                while($row_order_quantity = mysqli_fetch_array($run_order_quantity)){
                                    $count_order_quantity += $row_order_quantity['quantity'];
                                }
                            }
                        ?>
                        <p class="text-primary mt-3">
                        <?php
                            if(isset($_POST['filter_all'])){
                                echo "Tổng doanh thu";
                            }
                            else if(isset($_POST['filter_now'])){
                                echo "Ngày hôm nay";
                            }
                            else if(isset($_POST['filter1'])){
                                $date_start=$_POST['date_start'];
                                $date_end=$_POST['date_end'];
                                echo "Từ $date_start đến $date_end";
                            }
                            else if(isset($_POST['filter2'])){
                                $day = $_POST['day'];
                                $month = $_POST['month'];
                                $year = $_POST['year'];
                                if(empty($day)&&empty($month)){
                                    echo"Năm $year";
                                }
                                else if(empty($day)){
                                    echo "Tháng $month/$year";
                                }
                                else{
                                    echo "Ngày $day/$month/$year";
                                }
                            }
                            else{
                                echo "Tổng doanh thu";
                            }
                        ?>
                        </p>
                        <div class="row">
                            <div class="col-6">
                                <div class="row mb-3">
                                    <div class="col-8">Số đơn hàng: </div>
                                    <div class="col 4 text-end fw-bold"><?= $count_select; ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-8">Số lượng hàng: </div>
                                    <div class="col 4 text-end fw-bold"><?= $count_order_quantity; ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-8">Doanh thu: </div>
                                    <div class="col 4 text-end fw-bold"><?= currency_format($count_order_price); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 border border-warning mx-3">
                        <b>Thông tin sản phẩm</b>
                        <hr class="dropdown-divider text-warning">
                        <?php
                            $select_total = "select * from product_img";
                            $run_total = mysqli_query($conn, $select_total);
                            $total = mysqli_num_rows($run_total);
                            // while($row_total = mysqli_fetch_array($run_total)){
                            //     $total += $row_total['product_quantity'];
                            // }
                            $select_total1 = "select * from product_img where product_status='Đang bán'";
                            $run_total1 = mysqli_query($conn, $select_total1);
                            $total1 = mysqli_num_rows($run_total1);
                            // while($row_total1 = mysqli_fetch_array($run_total1)){
                            //     $total1 += $row_total1['product_quantity'];
                            // }
                            $select_total2 = "select * from product_img where product_quantity=0";
                            $run_total2 = mysqli_query($conn, $select_total2);
                            $total2 = mysqli_num_rows($run_total2);
                            // while($row_total2 = mysqli_fetch_array($run_total2)){
                            //     $total2 += $row_total2['product_quantity'];
                            // }
                        ?>
                        <div class="row mb-3">
                            <div class="col-8">Số lượng: </div>
                            <div class="col 4 text-end fw-bold"><?= $total; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-8">Đang bán: </div>
                            <div class="col 4 text-end fw-bold"><?= $total1; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-8">Ngừng bán: </div>
                            <div class="col 4 text-end fw-bold"><?= $total-$total1; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-8">Hết hàng: </div>
                            <div class="col 4 text-end fw-bold"><?= $total2; ?></div>
                        </div>
                    </div>
                </div>
            </div>




