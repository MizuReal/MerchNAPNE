<?php
include('server/connection.php');

// Check if a comment has been submitted
if (isset($_POST['submit_comment'])) {
    $product_id = $_POST['product_id'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments (product_id, comment) VALUES (?, ?)");
    $stmt->bind_param("is", $product_id, $comment);
    $stmt->execute();
    $stmt->close();
}

// Fetch comments for the given product
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM comments WHERE product_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $comments = $stmt->get_result();
}
?>

<div class="comments-section">
    <h3>Comments</h3>
    <form method="POST" action="comments.php?product_id=<?php echo $product_id; ?>">
        <textarea name="comment" required></textarea>
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
        <button type="submit" name="submit_comment">Submit Comment</button>
    </form>

    <div class="comments-list">
        <?php while ($row = $comments->fetch_assoc()) { ?>
            <div class="comment">
                <p><?php echo htmlspecialchars($row['comment']); ?></p>
                <small><?php echo $row['created_at']; ?></small>
            </div>
        <?php } ?>
    </div>
</div>
