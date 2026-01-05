<?php
require_once __DIR__ . '/functions/news_functions.php';

$news_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$error = '';
$success = false;

if (!$news_id) {
    $error = 'Bài viết không tồn tại!';
} else {
    $news = getNewsById($news_id);
    if (!$news) {
        $error = 'Bài viết không tồn tại!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_news'])) {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $category = trim($_POST['category'] ?? 'Tin tức');
    $status = $_POST['status'] ?? 'draft';
    
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
        $thumbnail = $news['thumbnail'];
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024;
            
            $file_type = mime_content_type($_FILES['thumbnail']['tmp_name']);
            $file_size = $_FILES['thumbnail']['size'];
            $file_ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WebP!';
            } elseif ($file_size > $max_size) {
                $error = 'Ảnh không được vượt quá 5MB!';
            } else {
                // Delete old image
                if (!empty($news['thumbnail'])) {
                    $old_path = __DIR__ . '/../images/news/' . $news['thumbnail'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                
                $upload_dir = __DIR__ . '/../images/news';
                $thumbnail = 'news_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $file_ext;
                $upload_path = $upload_dir . '/' . $thumbnail;
                
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                    $error = 'Lỗi khi upload ảnh!';
                }
            }
        }
        
        if (empty($error)) {
            if (updateNews($news_id, $title, $content, $category, $thumbnail, $status)) {
                $success = true;
                $news = getNewsById($news_id);
            } else {
                $error = 'Lỗi khi cập nhật bài viết!';
            }
        }
    }
}
?>

<div class="container-lg mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-edit"></i> Chỉnh sửa bài viết
            </h2>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Thành công!</strong> Bài viết đã được cập nhật.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi:</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($news): ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               placeholder="Nhập tiêu đề bài viết"
                               value="<?php echo htmlspecialchars($news['title']); ?>">
                        <small class="form-text text-muted">Tối thiểu 5 ký tự</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Danh mục</label>
                            <input type="text" name="category" class="form-control"
                                   value="<?php echo htmlspecialchars($news['category']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?php echo $news['status'] === 'draft' ? 'selected' : ''; ?>>Nháp</option>
                                <option value="published" <?php echo $news['status'] === 'published' ? 'selected' : ''; ?>>Xuất bản</option>
                                <option value="archived" <?php echo $news['status'] === 'archived' ? 'selected' : ''; ?>>Lưu trữ</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh đại diện</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*"
                               id="thumbnailInput">
                        <small class="form-text text-muted">Định dạng: JPG, PNG, GIF, WebP. Tối đa 5MB</small>
                        
                        <?php if (!empty($news['thumbnail'])): ?>
                            <div style="margin-top: 10px;">
                                <p>Ảnh hiện tại:</p>
                                <img src="../images/news/<?php echo htmlspecialchars($news['thumbnail']); ?>" 
                                     style="max-width: 200px; border-radius: 5px;">
                            </div>
                        <?php endif; ?>
                        <div id="imagePreview" style="margin-top: 10px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="12" required><?php echo htmlspecialchars($news['content']); ?></textarea>
                        <small class="form-text text-muted">Tối thiểu 50 ký tự</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="update_news" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="index.php?news_list" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            <?php endif; ?>
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
