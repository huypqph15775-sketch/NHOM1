<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Store</title>
    <!-- favicon -->
    <link rel="icon" href="images/phone.png">
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- bootstrap icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- css -->
    <link rel="stylesheet" href="css/index.css">
    
</head>
<body>
    <!-- Header -->
    <?php
        $active="Contact";
        include("includes/header.php")
    ?>

    <!-- section contact -->
    <section class="head">
        <div class="container pt-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="font-weight-yellow text-center py-4">
                    LIÊN HỆ VỚI CHÚNG TÔI
                    </h2>
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="row pt-3">
                                <div class="col-lg-1 offset-1 col-md-2 col-2">
                                  <span style="font-size: 40px; color: red"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                    <h3>Địa chỉ: </h3>
                                    <p>128A, Hồ Tùng Mậu, 
                                        <br>Mai Dịch, Cầu Giấy,
                                        <br>Tp Hà Nội.</p>
                                </div>
                            </div>
                            <div class="row pt-3">
                                <div class="col-lg-1 offset-1 col-md-2 col-2">
                                    <span style="font-size: 40px; color: orange"><i class="fas fa-phone-volume"></i></span>
                                </div>
                                <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                    <h3>Liên hệ: </h3>
                                    <p>Điện thoại: 9999.9999 
                                        <br>Email: PhoneStore@gmail.com 
                                        <br>(7:00 - 21:00)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                          <form action="" method="post">
                            <div class="form-row">
                                <label for="name" class="form-label">Họ tên:</label>
                                <input type="text" class="form-control" id="name" placeholder="Họ tên" name="name">
                                <label for="phone" class="form-label">Số điện thoại:</label>
                                <input type="text" class="form-control" id="phone" placeholder="Số điện thoại" name="phone">
                                <label for="email" class="form-label">Email:</label>
                                <input type="text" class="form-control" id="name" placeholder="Email" name="email">
                                <label for="content" class="form-label">Nội dung:</label>
                                <textarea class="form-control mb-2" name="content" id="content" cols="10" rows="5" placeholder="Nội dung"></textarea>
                                <input type="checkbox" name="" id="checkbox">
                                <label for="checkbox" class="checkbox">Tôi không phải robot!</label><br>
                                <button name="submit" style="margin-left: 200px; background-color: #31c27c; color: aliceblue" type="submit" class="btn mt-2">Gửi</button>
								<button style="margin-left: 20px; border-color: #31c27c" type="reset" class="btn btn-light mt-2">Hủy</button>
                            </div>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    if(isset($_POST['submit'])){
        $sender_name = trim($_POST['name'] ?? '');
        $sender_phone = trim($_POST['phone'] ?? '');
        $sender_email = trim($_POST['email'] ?? '');
        $sender_content = trim($_POST['content'] ?? '');
        
        // Validate email
        if(empty($sender_name) || empty($sender_email) || empty($sender_content)){
            echo "<h6 class='text-danger'>Vui lòng nhập đầy đủ thông tin.</h6>";
        } else if(!filter_var($sender_email, FILTER_VALIDATE_EMAIL)){
            echo "<h6 class='text-danger'>Email không hợp lệ.</h6>";
        } else {
            $receive_email = "phonestore@gmail.com";
            
            // Send to admin
            $subject_admin = "Tin nhắn liên hệ mới từ: {$sender_name}";
            $message_admin = "Họ tên: {$sender_name}\n";
            $message_admin .= "Số điện thoại: {$sender_phone}\n";
            $message_admin .= "Email: {$sender_email}\n";
            $message_admin .= "Nội dung:\n{$sender_content}";
            $headers_admin = "From: {$sender_email}\r\nReply-To: {$sender_email}\r\nContent-Type: text/plain; charset=UTF-8";
            
            @mail($receive_email, $subject_admin, $message_admin, $headers_admin);
            
            // Auto reply to customer
            $subject_reply = "Chào mừng đến với website";
            $msg_reply = "Cảm ơn bạn đã gửi tin nhắn cho chúng tôi. SmartPhoneStore.com sẽ trả lời bạn sớm.\n\nThông tin của bạn:\n";
            $msg_reply .= "Họ tên: {$sender_name}\n";
            $msg_reply .= "Email: {$sender_email}\n";
            $msg_reply .= "Nội dung: {$sender_content}";
            $headers_reply = "From: {$receive_email}\r\nContent-Type: text/plain; charset=UTF-8";
            
            @mail($sender_email, $subject_reply, $msg_reply, $headers_reply);
            
            // Send notification to admin
            include_once("includes/database.php");
            include_once("functions/functions.php");
            
            $notification_message = "Liên hệ từ: {$sender_name} | SĐT: {$sender_phone} | Email: {$sender_email}\n\nNội dung: {$sender_content}";
            
            // use add_notification() to send to admin
            if(function_exists('add_notification')){
                add_notification(NULL, 1, 'contact', 'Tin nhắn liên hệ mới', $notification_message, NULL);
            }
            
            echo "<h6 class='text-primary'>Tin nhắn của bạn đã được gửi thành công</h6>";
        }
    }
    ?>
        <!-- footer -->
        <?php
            include("includes/footer.php");
        ?>

      <!-- js -->
    <script src="js/index.js"></script>
</body>
</html>