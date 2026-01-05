<?php
// Migration: add `unit_price` column to `customer_order_products` if not exists.
// Run this script once from CLI or browser (remove after running).
require_once __DIR__ . '/../includes/database.php';

// Check if column exists
$res = mysqli_query($conn, "SHOW COLUMNS FROM customer_order_products LIKE 'unit_price'");
if ($res && mysqli_num_rows($res) > 0) {
    echo "Column unit_price already exists.\n";
    exit;
}

$alter = "ALTER TABLE customer_order_products ADD COLUMN unit_price INT DEFAULT 0 AFTER quantity";
if (mysqli_query($conn, $alter)) {
    echo "Added unit_price column to customer_order_products.\n";
} else {
    echo "Failed to add column: " . mysqli_error($conn) . "\n";
}

echo "Done.\n";
