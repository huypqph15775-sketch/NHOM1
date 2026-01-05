
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="index.php?dashboard">Trang chủ</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Danh sách khách hàng</li>
                        </ol>
                      </nav>
                </div>
                <hr class="dropdown-divider">
                <table class="table table-striped table-hover">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tên KH</th>
                    <th scope="col">Ảnh</th>
                    <th scope="col">Giới tính</th>
                    <th scope="col">Email</th>
                    <th scope="col">SĐT</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col" class="text-center"></th>
                </tr>
                <?php
                    $get_customer = "select * from customer ORDER BY customer_id DESC";
                    $run_customer = mysqli_query($conn, $get_customer);
                    $i=0;
                    while($row_customer = mysqli_fetch_array($run_customer)){
                        $customer_id = $row_customer['customer_id'];
                        $customer_name = $row_customer['customer_name'];
                        $customer_img = $row_customer['customer_img'];
                        $customer_sex = $row_customer['customer_sex'];
                        $customer_email = $row_customer['customer_email'];
                        $customer_phone = $row_customer['customer_phone'];
                        $customer_address = $row_customer['customer_address'];
                        $account_status = $row_customer['account_status'];
                        $i++;
                    
                ?>
                <tr>
                    <th scope="row"><?php echo $i; ?></th>
                    <td><?php echo $customer_name ?></td>
                    <td><img src="../customer/customer_img/<?php echo $customer_img ?>" width="100px" style="object-fit: contain;" alt=""></td>
                    <td><?php echo $customer_sex ?></td>
                    <td><?php echo $customer_email ?></td>
                    <td><?php echo $customer_phone ?></td>
                    <td><?php echo $customer_address ?></td>
                    <td class="text-center"><button onclick="del_customer(<?php echo $customer_id; ?>)" class="btn btn-danger text-white">
                    <?php
                        if($account_status == "Active") echo "Khóa";
                        if($account_status == "Locked") echo "Mở khóa";
                    ?>
                    </button></td>
                </tr>
                <?php
                    }
                ?>

            <script>
            function del_customer(id){
                var result = confirm("Bạn có chắc chắn muốn thực hiện hành động này? ");
                if(result==true){
                    document.location = 'index.php?customer_delete='+id;
                }
            }
            </script>
                </table>
            </div>




