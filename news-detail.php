<?php
session_start();
include("includes/database.php");
require_once "administrator/functions/news_functions.php";

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: news.php');
    exit();
}

$news = getNewsBySlug($slug);

if (!$news || $news['status'] !== 'published') {
    header('Location: news.php');
    exit();
}

// Handle comment submission
$comment_success = false;
$comment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['customer_id'])) {
        $comment_error = 'Vui lòng <a href="signin.php">đăng nhập</a> để bình luận!';
    } else {
        $content = trim($_POST['comment_content'] ?? '');
        $customer_id = $_SESSION['customer_id'];
        
        if (empty($content)) {
            $comment_error = 'Vui lòng nhập nội dung bình luận!';
        } elseif (strlen($content) < 5) {
            $comment_error = 'Bình luận phải có ít nhất 5 ký tự!';
        } else {
            $comment_id = addComment($news['news_id'], $customer_id, $content);
            if ($comment_id) {
                $comment_success = true;
            } else {
                $comment_error = 'Lỗi khi gửi bình luận!';
            }
        }
    }
}

$comments = getNewsComments($news['news_id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['title']); ?> - PhoneStore</title>
    <link rel="icon" href="images/phone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="css/index.css">
    <style>
        .news-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        .news-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }
        .news-content p {
            margin-bottom: 20px;
        }
        .news-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .comment {
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .comment-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php
        $active = "News";
        include("includes/header.php");
    ?>

    <!-- Breadcrumb -->
    <section class="mt-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-primary" href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a class="text-primary" href="news.php">Tin tức</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($news['title']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- News Header -->
    <div class="news-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($news['title']); ?></h1>
            <p class="mt-3 mb-0">
                <small>
                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                    | <i class="fas fa-user"></i> <?php echo htmlspecialchars($news['author_name']); ?>
                    | <i class="fas fa-eye"></i> <?php echo $news['views']; ?> lượt xem
                </small>
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Article -->
                <div class="col-lg-8">
                    <?php if (!empty($news['thumbnail'])): ?>
                        <img src="images/news/<?php echo htmlspecialchars($news['thumbnail']); ?>" 
                             class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($news['title']); ?>">
                    <?php endif; ?>

                    <div class="news-content">
                        <?php echo $news['content']; ?>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <span class="badge bg-primary mb-3"><?php echo htmlspecialchars($news['category']); ?></span>
                    </div>

                    <!-- Comments Section -->
                    <div class="mt-5 pt-5 border-top">
                        <h3 class="mb-4">
                            <i class="fas fa-comments"></i> Bình luận
                            <?php
                            $comment_count = mysqli_num_rows($comments);
                            echo "(<span id=\"commentCount\">$comment_count</span>)";
                            ?>
                        </h3>

                        <?php if ($comment_success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> Bình luận của bạn đã được gửi. Vui lòng chờ phê duyệt!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif (!empty($comment_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $comment_error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Comment Form -->
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-comment-dots"></i> Để lại bình luận
                                    </h5>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <textarea name="comment_content" class="form-control" rows="4"
                                                      placeholder="Nhập bình luận của bạn..." required></textarea>
                                            <small class="form-text text-muted">Tối thiểu 5 ký tự</small>
                                        </div>
                                        <button type="submit" name="submit_comment" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Gửi bình luận
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Vui lòng <a href="signin.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">đăng nhập</a> 
                                để bình luận!
                            </div>
                        <?php endif; ?>

                        <!-- Comments List -->
                        <div class="mt-4">
                            <?php
                            if ($comment_count > 0):
                                mysqli_data_seek($comments, 0);
                                while ($comment = mysqli_fetch_assoc($comments)):
                            ?>
                                <div class="comment">
                                    <div class="d-flex gap-3">
                                        <?php
                                        $avatar = !empty($comment['customer_img']) 
                                            ? "customer/customer_img/" . htmlspecialchars($comment['customer_img'])
                                            : "images/noavatar.webp";
                                        ?>
                                        <img src="<?php echo $avatar; ?>" class="comment-avatar" alt="">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($comment['customer_name']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                            </small>
                                            <p class="mt-2 mb-0">
                                                <?php echo htmlspecialchars($comment['content']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4 sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-newspaper"></i> Tin tức nổi bật</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $related_news = getAllNews('published', 5);
                            while ($related = mysqli_fetch_assoc($related_news)):
                                if ($related['news_id'] !== $news['news_id']):
                            ?>
                                <div class="border-bottom p-3">
                                    <a href="news-detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" 
                                       class="text-decoration-none text-dark">
                                        <h6><?php echo htmlspecialchars($related['title']); ?></h6>
                                    </a>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i> 
                                        <?php echo date('d/m/Y', strtotime($related['created_at'])); ?>
                                    </small>
                                </div>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
        include("includes/footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
