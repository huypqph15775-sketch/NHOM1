<?php
if(!isset($_SESSION['admin_id'])){
    echo "<script>window.open('signin.php', '_self')</script>";
} else {
    require_once "functions/news_functions.php";

    // Handle approve comment
    if(isset($_GET['approve'])){
        $comment_id = $_GET['approve'];
        if(approveComment($comment_id)){
            echo "<script>alert('Duyệt bình luận thành công!')</script>";
            echo "<script>window.open('index.php?news_comments', '_self')</script>";
        }
    }

    // Handle reject comment
    if(isset($_GET['reject'])){
        $comment_id = $_GET['reject'];
        if(rejectComment($comment_id)){
            echo "<script>alert('Từ chối bình luận thành công!')</script>";
            echo "<script>window.open('index.php?news_comments', '_self')</script>";
        }
    }

    // Handle delete comment
    if(isset($_GET['delete'])){
        $comment_id = $_GET['delete'];
        if(deleteComment($comment_id)){
            echo "<script>alert('Xóa bình luận thành công!')</script>";
            echo "<script>window.open('index.php?news_comments', '_self')</script>";
        }
    }
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-comments"></i> Quản lý bình luận tin tức
            </h2>
        </div>
    </div>

    <!-- Tabs for filtering -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                <i class="fas fa-list"></i> Tất cả bình luận
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                <i class="fas fa-clock"></i> Chờ duyệt
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                <i class="fas fa-check-circle"></i> Đã duyệt
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                <i class="fas fa-times-circle"></i> Từ chối
            </button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content">
        <!-- ALL COMMENTS -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <?php
            $all_comments = mysqli_query($conn, "
                SELECT nc.*, n.title, c.customer_name
                FROM news_comments nc
                LEFT JOIN news n ON nc.news_id = n.news_id
                LEFT JOIN customer c ON nc.customer_id = c.customer_id
                ORDER BY nc.created_at DESC
            ");
            
            if(mysqli_num_rows($all_comments) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Bài viết</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while($row = mysqli_fetch_assoc($all_comments)):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></strong>
                                    </td>
                                    <td>
                                        <a href="../news-detail.php?slug=<?php echo urlencode($row['title']); ?>" target="_blank" title="<?php echo htmlspecialchars($row['title']); ?>">
                                            <?php echo htmlspecialchars(substr($row['title'], 0, 30)); ?>...
                                        </a>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars(substr($row['content'], 0, 50)); ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge <?php 
                                            if($row['status'] == 'pending') echo 'bg-warning text-dark';
                                            elseif($row['status'] == 'approved') echo 'bg-success';
                                            else echo 'bg-danger';
                                        ?>">
                                            <?php 
                                            if($row['status'] == 'pending') echo 'Chờ duyệt';
                                            elseif($row['status'] == 'approved') echo 'Đã duyệt';
                                            else echo 'Từ chối';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if($row['status'] != 'approved'): ?>
                                                <a href="index.php?news_comments&approve=<?php echo $row['comment_id']; ?>" class="btn btn-success" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if($row['status'] != 'rejected'): ?>
                                                <a href="index.php?news_comments&reject=<?php echo $row['comment_id']; ?>" class="btn btn-warning" title="Từ chối">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?news_comments&delete=<?php echo $row['comment_id']; ?>" class="btn btn-danger" title="Xóa" onclick="return confirm('Xác nhận xóa bình luận này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Không có bình luận nào
                </div>
            <?php endif; ?>
        </div>

        <!-- PENDING COMMENTS -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            <?php
            $pending_comments = mysqli_query($conn, "
                SELECT nc.*, n.title, c.customer_name
                FROM news_comments nc
                LEFT JOIN news n ON nc.news_id = n.news_id
                LEFT JOIN customer c ON nc.customer_id = c.customer_id
                WHERE nc.status = 'pending'
                ORDER BY nc.created_at DESC
            ");
            
            if(mysqli_num_rows($pending_comments) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-warning">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Bài viết</th>
                                <th>Nội dung</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while($row = mysqli_fetch_assoc($pending_comments)):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></strong></td>
                                    <td>
                                        <a href="../news-detail.php?slug=<?php echo urlencode($row['title']); ?>" target="_blank">
                                            <?php echo htmlspecialchars(substr($row['title'], 0, 30)); ?>...
                                        </a>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($row['content']); ?></small></td>
                                    <td><small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small></td>
                                    <td>
                                        <a href="index.php?news_comments&approve=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Duyệt
                                        </a>
                                        <a href="index.php?news_comments&reject=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-times"></i> Từ chối
                                        </a>
                                        <a href="index.php?news_comments&delete=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-check-circle"></i> Không có bình luận chờ duyệt
                </div>
            <?php endif; ?>
        </div>

        <!-- APPROVED COMMENTS -->
        <div class="tab-pane fade" id="approved" role="tabpanel">
            <?php
            $approved_comments = mysqli_query($conn, "
                SELECT nc.*, n.title, c.customer_name
                FROM news_comments nc
                LEFT JOIN news n ON nc.news_id = n.news_id
                LEFT JOIN customer c ON nc.customer_id = c.customer_id
                WHERE nc.status = 'approved'
                ORDER BY nc.created_at DESC
            ");
            
            if(mysqli_num_rows($approved_comments) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Bài viết</th>
                                <th>Nội dung</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while($row = mysqli_fetch_assoc($approved_comments)):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></strong></td>
                                    <td>
                                        <a href="../news-detail.php?slug=<?php echo urlencode($row['title']); ?>" target="_blank">
                                            <?php echo htmlspecialchars(substr($row['title'], 0, 30)); ?>...
                                        </a>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($row['content']); ?></small></td>
                                    <td><small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small></td>
                                    <td>
                                        <a href="index.php?news_comments&reject=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-times"></i> Từ chối
                                        </a>
                                        <a href="index.php?news_comments&delete=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Không có bình luận đã duyệt
                </div>
            <?php endif; ?>
        </div>

        <!-- REJECTED COMMENTS -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            <?php
            $rejected_comments = mysqli_query($conn, "
                SELECT nc.*, n.title, c.customer_name
                FROM news_comments nc
                LEFT JOIN news n ON nc.news_id = n.news_id
                LEFT JOIN customer c ON nc.customer_id = c.customer_id
                WHERE nc.status = 'rejected'
                ORDER BY nc.created_at DESC
            ");
            
            if(mysqli_num_rows($rejected_comments) > 0):
            ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>Khách hàng</th>
                                <th>Bài viết</th>
                                <th>Nội dung</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while($row = mysqli_fetch_assoc($rejected_comments)):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?></strong></td>
                                    <td>
                                        <a href="../news-detail.php?slug=<?php echo urlencode($row['title']); ?>" target="_blank">
                                            <?php echo htmlspecialchars(substr($row['title'], 0, 30)); ?>...
                                        </a>
                                    </td>
                                    <td><small><?php echo htmlspecialchars($row['content']); ?></small></td>
                                    <td><small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small></td>
                                    <td>
                                        <a href="index.php?news_comments&approve=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Duyệt
                                        </a>
                                        <a href="index.php?news_comments&delete=<?php echo $row['comment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Không có bình luận bị từ chối
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f5f5f5;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
}
</style>

<?php
}
?>
