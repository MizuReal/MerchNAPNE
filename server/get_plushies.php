<?php

include('connection.php');

//fetching productsq
$stmt = $conn->prepare("SELECT * FROM products WHERE product_category='plushies'LIMIT 4");

$stmt->execute();

$plushies_products = $stmt->get_result();

?>