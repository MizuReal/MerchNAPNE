<?php

include('connection.php');


//not yet codedq
//fetching products
$stmt = $conn->prepare("SELECT * FROM products WHERE product_category='games' LIMIT 4");

$stmt->execute();

$game_products = $stmt->get_result();

?>