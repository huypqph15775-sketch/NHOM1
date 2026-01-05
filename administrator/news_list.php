
<?php
require_once __DIR__ . '/functions/news_functions.php';


// Xử lý xuất bản tất cả bài viết nháp và lưu trữ
if (isset($_POST['publish_all'])) {
    global $conn;
    $conn->query("UPDATE news SET status = 'published' WHERE status = 'draft' OR status = 'archived'");
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Tất cả bài viết nháp và lưu trữ đã được xuất bản.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Xử lý lưu trữ tất cả bài viết
if (isset($_POST['archive_all'])) {
    global $conn;
    $conn->query("UPDATE news SET status = 'archived' WHERE status != 'archived'");
    echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Tất cả bài viết đã được lưu trữ.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $news_id = (int)$_GET['id'];
    if (deleteNews($news_id)) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Thành công!</strong> Bài viết đã được xóa.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
}

$all_news = getAllNews();
$total_news = mysqli_num_rows($all_news);
?>

<div class="container-lg mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-newspaper"></i> Quản lý Tin tức
            </h2>

            <div class="mb-4 d-flex gap-2">
                <a href="index.php?news_add" class="btn btn-primary">
                 <i class="fas fa-plus"></i> Thêm bài viết
                </a>
                <form method="post" style="display:inline;">
                    <button type="submit" name="publish_all" class="btn btn-success" onclick="return confirm('Bạn có chắc muốn xuất bản tất cả bài viết nháp?');">
                        <i class="fas fa-upload"></i> Xuất bản tất cả
                    </button>
                </form>
                <form method="post" style="display:inline;">
                    <button type="submit" name="archive_all" class="btn btn-secondary" onclick="return confirm('Bạn có chắc muốn lưu trữ tất cả bài viết?');">
                        <i class="fas fa-archive"></i> Lưu trữ tất cả
                    </button>
                </form>
            

            </div>



            <?php if ($total_news == 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có bài viết nào. 
                    <a href="index.php?news_add">Thêm bài viết</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Tác giả</th>
                                <th>Danh mục</th>
                                <th>Trạng thái</th>
                                <th>Lượt xem</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $all_news = getAllNews();
                            while ($news = mysqli_fetch_assoc($all_news)):
                            ?>
                                <tr>
                                    <td><strong><?php echo $news['news_id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                        <br>
                                        <small class="text-muted">/<?php echo $news['slug']; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                                    <td><?php echo htmlspecialchars($news['category']); ?></td>
                                    <td>
                                        <?php
                                        $status_badges = [
                                            'draft' => '<span class="badge bg-secondary">Nháp</span>',
                                            'published' => '<span class="badge bg-success">Đã xuất bản</span>',
                                            'archived' => '<span class="badge bg-warning">Lưu trữ</span>'
                                        ];
                                        echo $status_badges[$news['status']] ?? '<span class="badge bg-secondary">Không xác định</span>';
                                        ?>
                                    </td>
                                    <td><strong><?php echo $news['views']; ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?></td>
                                    <td>
                                        <a href="index.php?news_edit&id=<?php echo $news['news_id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="index.php?news_list&action=delete&id=<?php echo $news['news_id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
