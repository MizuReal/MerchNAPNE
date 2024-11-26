<?php 
session_start();
include('server/connection.php');
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_POST['user_id'];
    $review_text = $_POST['review_text'];

    // Verify user purchased the product
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders INNER JOIN order_items ON orders.order_id = order_items.order_id WHERE orders.user_id = ? AND order_items.product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->bind_result($purchase_count);
    $stmt->fetch();
    $stmt->close();

    if ($purchase_count > 0) {
        // Save the review
        $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, review_text, review_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $product_id, $user_id, $review_text);
        $stmt->execute();
        $stmt->close();

        header("Location: single_product.php?product_id=$product_id&message=Review submitted successfully.");
        exit();
    } else {
        header("Location: single_product.php?product_id=$product_id&error=You must purchase the product to leave a review.");
        exit();
    }
}
?>