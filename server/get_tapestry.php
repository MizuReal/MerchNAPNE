<?php

include('connection.php');

//not yet codedq
//fetching products
$stmt = $conn->prepare("SELECT * FROM products WHERE product_category='tapestry'LIMIT 4");

$stmt->execute();

$tapestry_products = $stmt->get_result();

?>