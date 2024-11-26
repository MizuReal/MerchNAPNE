<?php
include('server/connection.php');
session_start();

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Fetch product reviews
    $stmt = $conn->prepare("
        SELECT reviews.review_id, reviews.review_text, reviews.review_date, 
               reviews.user_id, users.user_name 
        FROM reviews 
        INNER JOIN users ON reviews.user_id = users.user_id 
        WHERE reviews.product_id = ? 
        ORDER BY reviews.review_date DESC
    ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviews = $stmt->get_result();

    // Check if user has purchased the product (for leaving reviews)
    if (isset($_SESSION['user_id'])) {
        $logged_in_user_id = $_SESSION['user_id'];
        
        $purchase_check_stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM orders 
            INNER JOIN order_items ON orders.order_id = order_items.order_id 
            WHERE orders.user_id = ? AND order_items.product_id = ?
        ");
        $purchase_check_stmt->bind_param("ii", $logged_in_user_id, $product_id);
        $purchase_check_stmt->execute();
        $purchase_check_stmt->bind_result($purchase_count);
        $purchase_check_stmt->fetch();
        $purchase_check_stmt->close();
    }
}
?>