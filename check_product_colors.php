<?php
$conn = mysqli_connect('localhost', 'root', '', 'smartphone');
$product_id = 30;

$sql = "SELECT pi.product_color_img_id, pi.product_color_id, pc.product_color_name 
        FROM product_img pi 
        JOIN product_color pc ON pi.product_color_id = pc.product_color_id 
        WHERE pi.product_id = $product_id 
        ORDER BY pi.product_color_img_id";

$result = mysqli_query($conn, $sql);
if ($result) {
    echo "Product ID 30 Colors:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['product_color_img_id'] . " | Color ID: " . $row['product_color_id'] . " | Color Name: " . $row['product_color_name'] . "\n";
    }
} else {
    echo 'Error: ' . mysqli_error($conn) . "\n";
}
mysqli_close($conn);
?>
