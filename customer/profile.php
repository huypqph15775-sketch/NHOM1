
<?php
// Handle setting default address
if(isset($_POST['set_default_address']) && isset($_POST['selected_address'])){
    $address_id = intval($_POST['selected_address']);
    // clear existing defaults for this customer
    mysqli_query($conn, "UPDATE customer_addresses SET is_default = 0 WHERE customer_id = '$customer_id'");
    mysqli_query($conn, "UPDATE customer_addresses SET is_default = 1 WHERE address_id = '$address_id' AND customer_id = '$customer_id'");
    // update customer's main address (customer.customer_address) to the selected address_detail
    $res = mysqli_query($conn, "SELECT address_detail FROM customer_addresses WHERE address_id = '$address_id' AND customer_id = '$customer_id' LIMIT 1");
    if($row = mysqli_fetch_assoc($res)){
        $address_detail = mysqli_real_escape_string($conn, $row['address_detail']);
        mysqli_query($conn, "UPDATE customer SET customer_address = '$address_detail' WHERE customer_id = '$customer_id'");
        echo "<script>alert('Đã cập nhật địa chỉ mặc định'); window.open('myaccount.php?profile','_self');</script>";
    }
}

// Handle applying a voucher (store in session so payment step can use it)
if(isset($_POST['apply_voucher']) && isset($_POST['voucher_code'])){
    $voucher_code = mysqli_real_escape_string($conn, $_POST['voucher_code']);
    $today = date('Y-m-d');
    $check = mysqli_query($conn, "SELECT * FROM vouchers WHERE code = '$voucher_code' AND status = 'active' AND (allowed_customer_id IS NULL OR allowed_customer_id = '".intval(
        $customer_id
    )."') AND (start_date IS NULL OR start_date <= '$today') AND (end_date IS NULL OR end_date >= '$today')");
    if(mysqli_num_rows($check) > 0){
        $_SESSION['applied_voucher'] = $voucher_code;
        echo "<script>alert('Mã giảm giá $voucher_code đã được chọn'); window.open('myaccount.php?profile','_self');</script>";
    }else{
        echo "<script>alert('Mã giảm giá không hợp lệ hoặc hết hạn'); window.open('myaccount.php?profile','_self');</script>";
    }
}
?>

<?php
// Manage addresses: add / edit / delete from profile
$editing_id = 0;
if(isset($_POST['add_address_profile']) && !empty(trim($_POST['new_address_profile'] ?? ''))){
    $new_addr = mysqli_real_escape_string($conn, trim($_POST['new_address_profile']));
    $new_receiver = mysqli_real_escape_string($conn, trim($_POST['new_receiver_profile'] ?? $customer_name));
    $new_phone = mysqli_real_escape_string($conn, trim($_POST['new_phone_profile'] ?? $customer_phone));
    $insert = "INSERT INTO customer_addresses (customer_id, receiver_name, phone, address_detail, is_default) VALUES ('$customer_id', '$new_receiver', '$new_phone', '$new_addr', 0)";
    mysqli_query($conn, $insert);
    echo "<script>alert('Đã thêm địa chỉ mới'); window.open('myaccount.php?profile','_self');</script>";
    exit;
}

if(isset($_POST['delete_address'])){
    $aid = intval($_POST['delete_address']);
    // delete only if belongs to this customer
    mysqli_query($conn, "DELETE FROM customer_addresses WHERE address_id = '$aid' AND customer_id = '$customer_id'");
    echo "<script>alert('Đã xóa địa chỉ'); window.open('myaccount.php?profile','_self');</script>";
    exit;
}

if(isset($_POST['edit_address'])){
    $editing_id = intval($_POST['edit_address']);
}

if(isset($_POST['save_edited_address']) && isset($_POST['address_id_edit'])){
    $aid = intval($_POST['address_id_edit']);
    $er = mysqli_real_escape_string($conn, trim($_POST['edited_receiver'] ?? ''));
    $ep = mysqli_real_escape_string($conn, trim($_POST['edited_phone'] ?? ''));
    $ed = mysqli_real_escape_string($conn, trim($_POST['edited_detail'] ?? ''));
    mysqli_query($conn, "UPDATE customer_addresses SET receiver_name = '$er', phone = '$ep', address_detail = '$ed' WHERE address_id = '$aid' AND customer_id = '$customer_id'");
    echo "<script>alert('Đã cập nhật địa chỉ'); window.open('myaccount.php?profile','_self');</script>";
    exit;
}
?>

<div>
    <div class="myaccount-content">
        <h3><?php echo htmlspecialchars($customer_name); ?></h3>
        <div class="row">
            <div class="table-responsive col-8">
                <table class="table table-striped table-hover">
                    <tr>
                        <td>Giới tính: </td>
                        <td><strong><?php echo htmlspecialchars($customer_sex); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Tên tài khoản: </td>
                        <td><strong><?php echo htmlspecialchars($customer_user_name); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Email: </td>
                        <td><strong><?php echo htmlspecialchars($customer_email); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Số điện thoại: </td>
                        <td><strong><?php echo htmlspecialchars($customer_phone); ?></strong></td>
                    </tr>
                    <tr>
                        <td>Địa chỉ hiện tại: </td>
                        <td><strong><?php echo htmlspecialchars($customer_address); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Saved addresses -->
        <div class="mt-4">
            <h5>Địa chỉ đã lưu</h5>
            <?php
                $get_addresses = "SELECT * FROM customer_addresses WHERE customer_id = '$customer_id' ORDER BY is_default DESC, address_id DESC";
                $run_addresses = mysqli_query($conn, $get_addresses);
            ?>
            <div class="row">
                <div class="col-12">
                <?php if(mysqli_num_rows($run_addresses) == 0): ?>
                    <p>Chưa có địa chỉ lưu nào. Bạn có thể thêm địa chỉ mới bên dưới.</p>
                <?php else: ?>
                    <!-- Addresses form: single form to avoid nested forms and ensure predictable behavior -->
                    <div class="list-group">
                    <form method="post" id="addressesForm">
                        <?php while($addr = mysqli_fetch_assoc($run_addresses)):
                            $aid = (int)$addr['address_id'];
                            $receiver = htmlspecialchars($addr['receiver_name'] ?: $customer_name);
                            $phone = htmlspecialchars($addr['phone'] ?: $customer_phone);
                            $detail = htmlspecialchars($addr['address_detail']);
                        ?>
                                <div class="list-group-item">
                                    <?php if(!empty($editing_id) && $editing_id === $aid):
                                        // fetch fresh data for this address (single edit form inside the main form)
                                        $e = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer_addresses WHERE address_id = '$aid' AND customer_id = '$customer_id' LIMIT 1"));
                                        $er = htmlspecialchars($e['receiver_name']);
                                        $ep = htmlspecialchars($e['phone']);
                                        $ed = htmlspecialchars($e['address_detail']);
                                    ?>
                                        <div class="row g-2">
                                            <input type="hidden" name="address_id_edit" value="<?php echo $aid; ?>">
                                            <div class="col-md-4"><input type="text" name="edited_receiver" class="form-control" value="<?php echo $er; ?>" placeholder="Người nhận"></div>
                                            <div class="col-md-4"><input type="text" name="edited_phone" class="form-control" value="<?php echo $ep; ?>" placeholder="SĐT"></div>
                                            <div class="col-12"><textarea name="edited_detail" class="form-control" rows="2"><?php echo $ed; ?></textarea></div>
                                            <div class="col-12 mt-2">
                                                <button type="submit" name="save_edited_address" class="btn btn-primary btn-sm">Lưu</button>
                                                <button type="button" onclick="window.location.href='myaccount.php?profile'" class="btn btn-secondary btn-sm">Hủy</button>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <input class="form-check-input me-2" type="radio" name="selected_address" value="<?php echo $aid; ?>" <?php if($addr['is_default']) echo 'checked'; ?>>
                                                <strong><?php echo $receiver; ?></strong> — <?php echo $phone; ?><br><small><?php echo $detail; ?></small>
                                                <?php if($addr['is_default']): ?><span class="badge bg-success ms-2">Mặc định</span><?php endif; ?>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="submit" name="edit_address" value="<?php echo $aid; ?>" class="btn btn-outline-secondary">Sửa</button>
                                                <button type="submit" name="delete_address" value="<?php echo $aid; ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?');">Xóa</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                        <?php endwhile; ?>
                    </form>
                    </div>
                    <div class="mt-2">
                        <button type="submit" form="addressesForm" name="set_default_address" class="btn btn-primary btn-sm">Chọn làm địa chỉ mặc định</button>
                    </div>
                <?php endif; ?>
                </div>
            </div>

            <!-- Add new address form -->
            <div class="mt-3">
                <h6>Thêm địa chỉ mới</h6>
                <form method="post" class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="new_receiver_profile" class="form-control" placeholder="Người nhận (tùy chọn)">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="new_phone_profile" class="form-control" placeholder="SĐT (tùy chọn)">
                    </div>
                    <div class="col-12">
                        <textarea name="new_address_profile" class="form-control" rows="2" placeholder="Địa chỉ đầy đủ" required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_address_profile" class="btn btn-success btn-sm">Lưu địa chỉ</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Vouchers removed as requested -->

        <div class="welcome mt-5">
            <p>Xin chào,  <strong><?php echo htmlspecialchars($customer_name); ?></strong> (Không phải là <strong><?php echo htmlspecialchars($customer_name); ?> ? </strong><a href="../signout.php" class="signup">Đăng xuất</a>)</p>
        </div>
    </div>
</div>