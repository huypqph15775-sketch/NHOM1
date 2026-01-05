<?php
session_start();

// If the user confirmed logout via POST, destroy the session and redirect to homepage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    // Clear session array
    $_SESSION = [];

    // If session uses cookies, remove session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    // Destroy the session
    session_destroy();

    // Redirect to homepage after logout
    header('Location: /phonestoree/index.php');
    exit();
}
?>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng xuất</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; }
        .confirm-container { max-width:420px; margin:80px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); text-align:center; }
        .confirm-title { font-size:18px; margin-bottom:12px; }
        .confirm-actions { margin-top:18px; display:flex; gap:12px; justify-content:center; }
        .btn { padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-size:14px; }
        .btn-cancel { background:#e0e0e0; }
        .btn-confirm { background:#d32f2f; color:#fff; }
    </style>
</head>
<body>
    <div class="confirm-container">
        <div class="confirm-title">Bạn có chắc muốn đăng xuất?</div>
        <div>Nhấn "Xác nhận" để đăng xuất và quay về trang chủ. Nhấn "Hủy" để ở lại trang hiện tại.</div>

        <div class="confirm-actions">
            <form method="post" style="margin:0">
                <input type="hidden" name="confirm_logout" value="1">
                <button type="submit" class="btn btn-confirm">Xác nhận</button>
            </form>

            <button class="btn btn-cancel" onclick="history.back(); return false;">Hủy</button>
        </div>
    </div>
</body>
</html>
