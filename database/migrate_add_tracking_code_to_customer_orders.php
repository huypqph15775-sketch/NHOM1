<?php
// Migration: add `tracking_code` column to `customer_orders` if not exists.
include('../includes/database.php');
$res = mysqli_query($conn, "SHOW COLUMNS FROM customer_orders LIKE 'tracking_code'");
if(mysqli_num_rows($res) === 0){
    $alter = "ALTER TABLE customer_orders ADD COLUMN tracking_code VARCHAR(255) DEFAULT NULL AFTER status";
    if(mysqli_query($conn, $alter)){
        echo "Added tracking_code column to customer_orders.\n";
    } else {
        echo "Failed to add tracking_code: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "tracking_code column already exists.\n";
}
?>