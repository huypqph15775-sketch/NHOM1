<?php
include("includes/database.php");
require_once "administrator/functions/news_functions.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$total_news = countPublishedNews();
$total_pages = ceil($total_news / $per_page);

if ($page < 1 || $page > $total_pages) {
    $page = 1;
}

$news_list = getPublishedNews($page, $per_page);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - PhoneStore</title>
    <link rel="icon" href="images/phone.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="css/index.css">
    <style>
        .news-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .news-image {
            height: 200px;
            object-fit: cover;
            overflow: hidden;
        }
        .news-meta {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
        }
        .news-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
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
                    <li class="breadcrumb-item active">Tin tức</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- News Section -->
    <section class="py-5">
        <div class="container">
            <h1 class="mb-5 text-center">
                <i class="fas fa-newspaper text-primary"></i> Tin tức - Công nghệ
            </h1>

            <?php if ($total_news == 0): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Hiện chưa có tin tức nào. Vui lòng quay lại sau!
                </div>
            <?php else: ?>
                <div class="row g-4 mb-4">
                    <?php
                    while ($news = mysqli_fetch_assoc($news_list)):
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card news-card h-100 shadow-sm">
                                <?php if (!empty($news['thumbnail'])): ?>
                                    <img src="images/news/<?php echo htmlspecialchars($news['thumbnail']); ?>" 
                                         class="card-img-top news-image" alt="<?php echo htmlspecialchars($news['title']); ?>">
                                <?php else: ?>
                                    <div class="news-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="news-detail.php?slug=<?php echo htmlspecialchars($news['slug']); ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($news['title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <div class="news-meta">
                                        <span><i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($news['created_at'])); ?></span>
                                        <span><i class="fas fa-eye"></i> <?php echo $news['views']; ?> lượt xem</span>
                                    </div>
                                    
                                    <p class="card-text mt-3">
                                        <?php 
                                        $excerpt = strip_tags($news['content']);
                                        echo htmlspecialchars(substr($excerpt, 0, 100)) . '...';
                                        ?>
                                    </p>
                                    
                                    <div class="mt-3">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($news['category']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <a href="news-detail.php?slug=<?php echo htmlspecialchars($news['slug']); ?>" 
                                       class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-arrow-right"></i> Đọc tiếp
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="news.php?page=<?php echo $page - 1; ?>">
                                        <i class="fas fa-chevron-left"></i> Trước
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            if ($start > 1):
                            ?>
                                <li class="page-item">
                                    <a class="page-link" href="news.php?page=1">1</a>
                                </li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="news.php?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end < $total_pages): ?>
                                <?php if ($end < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="news.php?page=<?php echo $total_pages; ?>">
                                        <?php echo $total_pages; ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="news.php?page=<?php echo $page + 1; ?>">
                                        Sau <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php
        include("includes/footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
