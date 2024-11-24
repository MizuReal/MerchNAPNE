<?php include('layouts/header.php');?>


<?php 
include('server/connection.php');

if(isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    
    $product = $stmt->get_result(); // This is an array

    $comments_query = "
        SELECT c.*, u.user_name
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.product_id = $product_id
    ";
    $comments = $conn->query($comments_query);
} else {
    header('location: index.php');
}

// Fetch the current product based on the product_id in the URL
$product_id = $_GET['product_id'];
$current_product_query = "SELECT * FROM products WHERE product_id = '$product_id'";
$current_product_result = $conn->query($current_product_query); // Replaced $mysqli with $conn
$current_product = $current_product_result->fetch_assoc();

// Get the product category of the current product
$product_category = $current_product['product_category'];

// Fetch related products from the same category
$related_products_query = "SELECT * FROM products WHERE product_category = '$product_category' AND product_id != '$product_id' LIMIT 4";
$related_products_result = $conn->query($related_products_query); // Replaced $mysqli with $conn
?>


<!--Single Product-->
<section class="container single-product my-5 pt-5">
    <div class="row mt-5">
       <?php while($row = $product->fetch_assoc()){?>
        <div class="col-lg-5 col-md-6 col-sm-12">
            <img class="img-fluid w-100 pb-1" src="assets/imgs/<?php echo $row['product_image'];?>" id="mainImg"/>
            <div class="small-img-group">
                <div class="small-img-col">
                    <img src="assets/imgs/<?php echo $row['product_image'];?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/imgs/<?php echo $row['product_image2'];?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/imgs/<?php echo $row['product_image3'];?>" width="100%" class="small-img"/>
                </div>
                <div class="small-img-col">
                    <img src="assets/imgs/<?php echo $row['product_image4'];?>" width="100%" class="small-img"/>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-md-12 col-sm-12">
            <h6><?php echo $row['product_category']?></h6>
            <h3 class="py-4"><?php echo $row['product_name'];?></h3>
            <h2>$<?php echo $row['product_price'];?></h2>

                    <form method="POST" action="cart.php">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>"/>
            <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>"/>
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>"/> 
            <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>"/>
            <input type="number" name="product_quantity" value="1"/>
            <button class="buy-btn" type="submit" name="add_to_cart">Add to cart</button>
            </form>
                    <h4 class="mt-5 mb-5">Product Details</h4>
            <span> <?php echo $row['product_description'];?>
            </span>
        </div>
        <?php } ?>
    </div>
</section>
<section id="related-products" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Related Products</h3>
        <hr class="mx-auto">
        <p>Check out these products that you might like!</p>
    </div>
    <!-- Updated the row class for centering without overriding your existing classes -->
    <div class="row mx-auto container-fluid d-flex justify-content-center">
        <?php while($row = $related_products_result->fetch_assoc()) { ?>
            <div class="product text-center col-lg-3 col-md-4 col-sm-6 mb-4">
                <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
                <div class="star">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
                <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
                <a href="<?php echo "single_product.php?product_id=" . $row['product_id']; ?>">
                    <button class="buy-btn">Buy Now</button>
                </a>
            </div>
        <?php } ?>
    </div>
</section>


<!-- comments -->
<section class="container product-comments my-5">
    <h4>Comments</h4>
    <hr>

    <?php if ($comments->num_rows > 0): ?>
        <?php while ($comment = $comments->fetch_assoc()): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['user_name']); ?></strong> <small><?php echo $comment['comment_date']; ?></small></p>
                <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                <hr>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet. Be the first to leave a review!</p>
    <?php endif; ?>
</section>

<!-- comment form -->
<section class="container add-comment my-5">
    <h4>Leave a Comment</h4>
    <form action="submit_comment.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <input type="hidden" name="user_id" value="<?php echo $logged_in_user_id; ?>">
        <div class="mb-3">
            <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
        </div>
        <button type="submit" class="buy-btn text-uppercase">Submit</button>
    </form>
</section>


<script>


  // Get main image - fixed getElementsById to getElementById
  var mainImg = document.getElementById("mainImg");
    
    // Get all small images
    var smallImg = document.getElementsByClassName("small-img");
    
    // Add click handlers to all small images
    for (let i = 0; i < smallImg.length; i++) {
        smallImg[i].onclick = function() {
            mainImg.src = smallImg[i].src;
        }
    }
    </script>



<?php include('layouts/footer.php');?>