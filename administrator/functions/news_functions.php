<?php
/**
 * News Management Functions
 */

// ============================================
// ADMIN FUNCTIONS
// ============================================

/**
 * Get all news (with filter)
 */
function getAllNews($status = null, $limit = null) {
    global $conn;
    
    $query = "
        SELECT n.*, a.admin_name as author_name
        FROM news n
        LEFT JOIN admin a ON n.author_id = a.admin_id
    ";
    
    if ($status) {
        $query .= " WHERE n.status = '$status'";
    }
    
    $query .= " ORDER BY n.created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $query);
    return $result;
}

/**
 * Get single news
 */
function getNewsById($news_id) {
    global $conn;
    
    $query = "
        SELECT n.*, a.admin_name as author_name
        FROM news n
        LEFT JOIN admin a ON n.author_id = a.admin_id
        WHERE n.news_id = ?
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $news_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_assoc();
        $stmt->close();
        return $news;
    }
    
    return null;
}

/**
 * Get news by slug
 */
function getNewsBySlug($slug) {
    global $conn;
    
    $query = "
        SELECT n.*, a.admin_name as author_name
        FROM news n
        LEFT JOIN admin a ON n.author_id = a.admin_id
        WHERE n.slug = ?
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $news = $result->fetch_assoc();
        $stmt->close();
        
        // Increase view count
        if ($news) {
            $update_views = "UPDATE news SET views = views + 1 WHERE news_id = ?";
            $stmt_views = $conn->prepare($update_views);
            if ($stmt_views) {
                $stmt_views->bind_param('i', $news['news_id']);
                $stmt_views->execute();
                $stmt_views->close();
            }
        }
        
        return $news;
    }
    
    return null;
}

/**
 * Create news
 */
function createNews($title, $content, $category, $author_id, $thumbnail = '', $status = 'draft') {
    global $conn;
    
    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Check slug uniqueness
    $check_slug = "SELECT news_id FROM news WHERE slug = ? LIMIT 1";
    $stmt_check = $conn->prepare($check_slug);
    if ($stmt_check) {
        $stmt_check->bind_param('s', $slug);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $slug = $slug . '-' . time();
        }
        $stmt_check->close();
    }
    
    $query = "
        INSERT INTO news (title, slug, content, thumbnail, category, author_id, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        // types: title(s), slug(s), content(s), thumbnail(s), category(s), author_id(i), status(s)
        $stmt->bind_param('sssssis', $title, $slug, $content, $thumbnail, $category, $author_id, $status);
        $result = $stmt->execute();
        $news_id = $conn->insert_id;
        $stmt->close();
        
        return $result ? $news_id : false;
    }
    
    return false;
}

/**
 * Update news
 */
function updateNews($news_id, $title, $content, $category, $thumbnail = null, $status = null) {
    global $conn;
    
    // Generate slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Check if slug exists (for other news)
    $check_slug = "SELECT news_id FROM news WHERE slug = ? AND news_id != ? LIMIT 1";
    $stmt_check = $conn->prepare($check_slug);
    if ($stmt_check) {
        $stmt_check->bind_param('si', $slug, $news_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $slug = $slug . '-' . time();
        }
        $stmt_check->close();
    }
    
    // Determine which query to use based on what parameters are provided
    if (!empty($thumbnail) && !empty($status)) {
        // Update with new thumbnail and status
        $query = "
            UPDATE news 
            SET title = ?, slug = ?, content = ?, category = ?, thumbnail = ?, status = ?
            WHERE news_id = ?
        ";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssssssi', $title, $slug, $content, $category, $thumbnail, $status, $news_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    } elseif (!empty($thumbnail)) {
        // Update with new thumbnail only
        $query = "
            UPDATE news 
            SET title = ?, slug = ?, content = ?, category = ?, thumbnail = ?
            WHERE news_id = ?
        ";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sssssi', $title, $slug, $content, $category, $thumbnail, $news_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    } elseif (!empty($status)) {
        // Update with status only
        $query = "
            UPDATE news 
            SET title = ?, slug = ?, content = ?, category = ?, status = ?
            WHERE news_id = ?
        ";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('sssssi', $title, $slug, $content, $category, $status, $news_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    } else {
        // Update without thumbnail and status
        $query = "
            UPDATE news 
            SET title = ?, slug = ?, content = ?, category = ?
            WHERE news_id = ?
        ";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('ssssi', $title, $slug, $content, $category, $news_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    }
    
    return false;
}

/**
 * Delete news
 */
function deleteNews($news_id) {
    global $conn;
    
    // Get news to delete image
    $news = getNewsById($news_id);
    if ($news && !empty($news['thumbnail'])) {
        $image_path = 'images/news/' . $news['thumbnail'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $query = "DELETE FROM news WHERE news_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $news_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

/**
 * Get published news with pagination
 */
function getPublishedNews($page = 1, $per_page = 10) {
    global $conn;
    
    $offset = ($page - 1) * $per_page;
    
    $query = "
        SELECT n.*, a.admin_name as author_name
        FROM news n
        LEFT JOIN admin a ON n.author_id = a.admin_id
        WHERE n.status = 'published'
        ORDER BY n.created_at DESC
        LIMIT ?, ?
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('ii', $offset, $per_page);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    
    return null;
}

/**
 * Count total published news
 */
function countPublishedNews() {
    global $conn;
    
    $query = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

// ============================================
// COMMENT FUNCTIONS
// ============================================

/**
 * Add comment
 */
function addComment($news_id, $customer_id, $content) {
    global $conn;
    
    $query = "
        INSERT INTO news_comments (news_id, customer_id, content, status)
        VALUES (?, ?, ?, 'pending')
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('iis', $news_id, $customer_id, $content);
        $result = $stmt->execute();
        $comment_id = $conn->insert_id;
        $stmt->close();
        
        return $result ? $comment_id : false;
    }
    
    return false;
}

/**
 * Get approved comments for news
 */
function getNewsComments($news_id) {
    global $conn;
    
    $query = "
        SELECT nc.*, c.customer_name, c.customer_img
        FROM news_comments nc
        LEFT JOIN customer c ON nc.customer_id = c.customer_id
        WHERE nc.news_id = ? AND nc.status = 'approved'
        ORDER BY nc.created_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $news_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
    
    return null;
}

/**
 * Get pending comments for admin
 */
function getPendingComments() {
    global $conn;
    
    $query = "
        SELECT nc.*, n.title, c.customer_name
        FROM news_comments nc
        LEFT JOIN news n ON nc.news_id = n.news_id
        LEFT JOIN customer c ON nc.customer_id = c.customer_id
        WHERE nc.status = 'pending'
        ORDER BY nc.created_at DESC
    ";
    
    $result = mysqli_query($conn, $query);
    return $result;
}

/**
 * Approve comment
 */
function approveComment($comment_id) {
    global $conn;
    
    $query = "UPDATE news_comments SET status = 'approved' WHERE comment_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $comment_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

/**
 * Reject comment
 */
function rejectComment($comment_id) {
    global $conn;
    
    $query = "UPDATE news_comments SET status = 'rejected' WHERE comment_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $comment_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

/**
 * Delete comment
 */
function deleteComment($comment_id) {
    global $conn;
    
    $query = "DELETE FROM news_comments WHERE comment_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $comment_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

?>
