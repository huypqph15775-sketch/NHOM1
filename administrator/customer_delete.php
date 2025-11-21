<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
}
else{
    if(isset($_GET['customer_delete'])){
            $customer_id = $_GET['customer_delete'];
            $get_account_status = "select * from customer where customer_id = '$customer_id'";
            $run_account_status = mysqli_query($conn, $get_account_status);
            $row_account_status = mysqli_fetch_array($run_account_status);
            $account_status = $row_account_status['account_status'];
            if($account_status == "Active"){
                $update_customer = "update customer set account_status = 'Locked' where customer_id = '$customer_id'";
                $run_update = mysqli_query($conn, $update_customer);
                if($run_update){
                    echo "<script>alert('Khóa tài khoản thành công')</script>";
                    echo "<script>window.open('index.php?customer_list', '_self')</script>";
                }
            }
            else{
                $update_customer = "update customer set account_status = 'Active' where customer_id = '$customer_id'";
                $run_update = mysqli_query($conn, $update_customer);
                if($run_update){
                    echo "<script>alert('Mở khóa tài khoản thành công')</script>";
                    echo "<script>window.open('index.php?customer_list', '_self')</script>";
                }
            }
    }
}
?>