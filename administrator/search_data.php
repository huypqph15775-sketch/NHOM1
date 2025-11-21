<?php
include("includes/database.php");
if(isset($_POST["query"])){  
    $output = '';  
    $query = "SELECT * FROM products WHERE product_name LIKE '%".$_POST["query"]."%'";  
    $result = mysqli_query($conn, $query);  
    $output = '<ul class="list-group">';  
    
    if(mysqli_num_rows($result) > 0){  
        while($row = mysqli_fetch_array($result)){  
            $output .= '<li class="list-group-item list-group-item-action" style="cursor: pointer">'.$row["product_name"].'</li>';  
        }  
    }else{  
        $output .= '<li class="list-group-item list-group-item-action disabled" style="cursor: pointer">Không có sản phẩm này</li>';  
    }  

$output .= '</ul>';  
echo $output;  
}
?>