<?php
// administrator/mailbox.php


require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/includes/database.php';



// Chỉ cho phép admin (level >= 3, không phải nvkho) truy cập
if ($user_level < 3 || $is_nvkho) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này.</div>';
    exit;
}

// Lấy danh sách mail từ bảng notifications, type = 'contact'
$sql = "SELECT notify_id, title, content, is_read, created_at FROM notifications WHERE type = 'contact' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<?php
require_once __DIR__ . '/includes/nav.php';
require_once __DIR__ . '/includes/database.php';

// Chỉ cho phép admin (level >= 3, không phải nvkho) truy cập
if ($user_level < 3 || $is_nvkho) {
        echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này.</div>';
        exit;
}

$sql = "SELECT notify_id, title, content, is_read, created_at FROM notifications WHERE type = 'contact' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<div class="container-fluid mt-5 pt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="mb-4 text-center fw-bold" style="font-size:2.2rem;letter-spacing:1px;">
                <i class="fas fa-envelope"></i> Quản lý mail liên hệ
            </h3>
            <div class="card shadow rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Liên hệ</th>
                                    <th class="text-center">Gmail</th>
                                    <th class="text-center">SĐT</th>
                                    <th class="text-center">Nội dung</th>
                                    <th class="text-center">Ngày giờ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)):
                                    $contact_name = $row['title'];
                                    $content = $row['content'];
                                    $gmail = '';
                                    $phone = '';
                                    // Tách tên, SĐT, Gmail
                                    if (preg_match('/Liên hệ từ: (.*?) \| SĐT: (.*?) \| Email: (.*?)\s*\\n?\\n?Nội dung:/u', $content, $matches)) {
                                        $contact_name = $matches[1];
                                        $phone = $matches[2];
                                        $gmail = $matches[3];
                                    } else {
                                        // Trường hợp không đúng định dạng, thử tách riêng từng phần
                                        if (preg_match('/Liên hệ từ: (.*?) \|/u', $content, $m)) $contact_name = $m[1];
                                        if (preg_match('/SĐT: (.*?) \|/u', $content, $m)) $phone = $m[1];
                                        if (preg_match('/Email: (.*?)\\n/u', $content, $m)) $gmail = $m[1];
                                    }
                                    $main_content = $content;
                                    if (preg_match('/Nội dung: (.*)$/us', $content, $matches)) {
                                            $main_content = $matches[1];
                                    }
                                    $datetime = date('d/m/Y H:i', strtotime($row['created_at']));
                                ?>
                                <tr style="border-bottom: 2px solid #e0e0e0;">
                                    <td class="text-center fw-bold text-success"> <?= htmlspecialchars($row['notify_id']) ?> </td>
                                    <td class="fw-semibold text-dark"> <?= htmlspecialchars($contact_name) ?> </td>
                                    <td class="text-center"> <?= htmlspecialchars($gmail) ?> </td>
                                    <td class="text-center"> <?= htmlspecialchars($phone) ?> </td>
                                    <td style="white-space: pre-line; max-width: 400px; word-break: break-word; color:#444;"> <?= nl2br(htmlspecialchars($main_content)) ?> </td>
                                    <td class="text-center text-secondary"> <?= htmlspecialchars($datetime) ?> </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <thead>
