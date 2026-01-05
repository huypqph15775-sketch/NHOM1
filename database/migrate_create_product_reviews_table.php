<?php
// Migration: create `product_reviews` table if not exists
include('../includes/database.php');
$res = mysqli_query($conn, "SHOW TABLES LIKE 'product_reviews'");
if(mysqli_num_rows($res) === 0){
    $sql = "CREATE TABLE product_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        product_color_id INT DEFAULT NULL,
        customer_id INT NOT NULL,
        order_id INT DEFAULT NULL,
        rating TINYINT NOT NULL DEFAULT 5,
        title VARCHAR(255) DEFAULT NULL,
        message TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (product_id),
        INDEX (customer_id),
        INDEX (order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if(mysqli_query($conn, $sql)){
        echo "Created product_reviews table.\n";
    } else {
        echo "Failed to create product_reviews: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "product_reviews table already exists.\n";
}
?>