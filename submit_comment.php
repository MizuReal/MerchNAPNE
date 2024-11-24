<?php
include('server/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['user_id'], $_POST['comment_text'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_POST['user_id']; // Get the user ID from the form
    $comment_text = $_POST['comment_text'];

    // Prepare the SQL statement to insert the comment
    $stmt = $conn->prepare("INSERT INTO comments (product_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $product_id, $user_id, $comment_text); // Bind the user_id here

    if ($stmt->execute()) {
        header("Location: single_product.php?product_id=" . $product_id); // Redirect back to the product page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
