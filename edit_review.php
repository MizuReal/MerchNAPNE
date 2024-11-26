<?php
include('server/connection.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_review'])) {
        // Update review
        $review_id = $_POST['review_id'];
        $product_id = $_POST['product_id'];
        $review_text = $_POST['review_text'];

        $stmt = $conn->prepare("UPDATE reviews SET review_text = ? WHERE review_id = ?");
        $stmt->bind_param("si", $review_text, $review_id);

        if ($stmt->execute()) {
            $update_success = true;
            header("Location: single_product.php?product_id=" . $product_id);
            exit();
        }
    } elseif (isset($_POST['delete_review'])) {
        // Delete review
        $review_id = $_POST['review_id'];
        $product_id = $_POST['product_id'];

        $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
        $stmt->bind_param("i", $review_id);

        if ($stmt->execute()) {
            header("Location: single_product.php?product_id=" . $product_id);
            exit();
        }
    }
}
?>
