<?php
// Ensure the notifications table has an `is_admin` column.
// Usage: open this file in your browser via XAMPP (e.g. http://localhost/phonestoree/database/ensure_notifications_is_admin.php)

include __DIR__ . '/../includes/database.php';

if (!isset($conn)) {
    echo "Database connection not available. Check includes/database.php\n";
    exit;
}

$table_check = "SHOW TABLES LIKE 'notifications'";
$res = @mysqli_query($conn, $table_check);
if(!$res || mysqli_num_rows($res) == 0){
    echo "Table `notifications` does not exist. You may need to run migrations first.<br>";
    exit;
}

$col_check = "SHOW COLUMNS FROM `notifications` LIKE 'is_admin'";
$res2 = @mysqli_query($conn, $col_check);
if($res2 && mysqli_num_rows($res2) > 0){
    echo "Column `is_admin` already exists. Nothing to do.<br>";
    exit;
}

$alter = "ALTER TABLE `notifications` ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT 0, ADD INDEX (`is_admin`)";
if(@mysqli_query($conn, $alter)){
    echo "Successfully added `is_admin` column to `notifications`.<br>";
} else {
    echo "Failed to add column: " . mysqli_error($conn) . "<br>";
}

?>