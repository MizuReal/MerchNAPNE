<?php

include('connection.php');

//fetching products
$stmt = $conn->prepare("SELECT * FROM products LIMIT 4");

$stmt->execute();

$featured_products = $stmt->get_result();

?>