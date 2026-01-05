<?php
require_once __DIR__ . '/functions/news_functions.php';
require_once __DIR__ . '/../includes/security_helper.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $category = trim($_POST['category'] ?? 'Tin tức');
    $status = $_POST['status'] ?? 'draft';
    $author_id = $_SESSION['admin_id'];
    
    // Validate
    if (empty($title)) {
        $error = 'Vui lòng nhập tiêu đề!';
    } elseif (strlen($title) < 5) {
        $error = 'Tiêu đề phải có ít nhất 5 ký tự!';
    } elseif (empty($content)) {
        $error = 'Vui lòng nhập nội dung!';
    } elseif (strlen($content) < 50) {
        $error = 'Nội dung phải có ít nhất 50 ký tự!';
    } else {
        // Handle image upload
        $thumbnail = '';
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $file_type = mime_content_type($_FILES['thumbnail']['tmp_name']);
            $file_size = $_FILES['thumbnail']['size'];
            $file_ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WebP!';
            } elseif ($file_size > $max_size) {
                $error = 'Ảnh không được vượt quá 5MB!';
            } else {
                $upload_dir = __DIR__ . '/../images/news';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $thumbnail = 'news_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file_ext;
                $upload_path = $upload_dir . '/' . $thumbnail;
                
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                    $error = 'Lỗi khi upload ảnh!';
                }
            }
        }
        
        if (empty($error)) {
            $news_id = createNews($title, $content, $category, $author_id, $thumbnail, $status);
            if ($news_id) {
                $success = true;
            } else {
                $error = 'Lỗi khi tạo bài viết!';
            }
        }
    }
}
?>

<div class="container-lg mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-plus"></i> Thêm bài viết
            </h2>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Thành công!</strong> Bài viết đã được tạo.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi:</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required
                           placeholder="Nhập tiêu đề bài viết"
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    <small class="form-text text-muted">Tối thiểu 5 ký tự</small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Danh mục</label>
                        <input type="text" name="category" class="form-control"
                               placeholder="Ví dụ: Tin tức, Công nghệ, Khuyến mãi"
                               value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : 'Tin tức'; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="draft">Nháp</option>
                            <option value="published">Xuất bản</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hình ảnh đại diện</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*"
                           id="thumbnailInput">
                    <small class="form-text text-muted">Định dạng: JPG, PNG, GIF, WebP. Tối đa 5MB</small>
                    <div id="imagePreview" style="margin-top: 10px;"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" required placeholder="Nhập nội dung bài viết (plain text)"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    <small class="form-text text-muted">Tối thiểu 50 ký tự. (Không chấp nhận liên kết tự động; nhập văn bản thuần)</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="save_news" class="btn btn-primary">
                        <i class="fas fa-save"></i> Tạo bài viết
                    </button>
                    <a href="index.php?news_list" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image preview -->
<script>
document.getElementById('thumbnailInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.innerHTML = '<img src="' + event.target.result + '" style="max-width: 200px; border-radius: 5px;">';
        };
        reader.readAsDataURL(file);
    }
});
</script>
