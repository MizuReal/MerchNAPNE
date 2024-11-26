<?php include('layouts/header.php');
session_start()
?>

<?php
include('server/connection.php');
include('reviews.php');
include('edit_review.php');


if(isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    
    $product = $stmt->get_result(); // This is an array
} else {
    header('location: index.php');
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT reviews.review_text, reviews.review_date, users.user_name 
                            FROM reviews 
                            INNER JOIN users ON reviews.user_id = users.user_id 
                            WHERE reviews.product_id = ? 
                            ORDER BY reviews.review_date DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
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

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    // Updated query to include reviews.user_id in the SELECT statement
    $stmt = $conn->prepare("SELECT reviews.review_id, reviews.review_text, reviews.review_date, 
                           reviews.user_id, users.user_name 
                           FROM reviews 
                           INNER JOIN users ON reviews.user_id = users.user_id 
                           WHERE reviews.product_id = ? 
                           ORDER BY reviews.review_date DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
}

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

<!-- Reviews Section -->
<div class="container my-5">
    <div class="row">
        <!-- Reviews Container -->
        <div class="col-md-8">
            <div class="product-reviews">
                <h4>Reviews</h4>
                <hr>
                <?php
                $bad_words = array('fuck', 'shit', 'bitch');
                $pattern = '/(' . implode('|', array_map('preg_quote', $bad_words)) . ')/i';

                if ($reviews && $reviews->num_rows > 0): ?>
                    <div class="reviews-container">
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review card mb-3">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">
                                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                                    <small class="text-muted ms-2">
                                        <?php echo date('F j, Y', strtotime($review['review_date'])); ?>
                                    </small>
                                </h6>
                                <p class="card-text mt-2">
                                    <?php
                                    $filtered_review_text = preg_replace_callback($pattern, function ($matches) {
                                        return str_replace($matches[0], '[filtered]', $matches[0]);
                                    }, $review['review_text']);
                                    echo htmlspecialchars($filtered_review_text);
                                    ?>
                                </p>
                                <!-- Edit Button -->
                                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && $_SESSION['user_id'] == $review['user_id']): ?>
                                    <div class="review-actions">
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="d-inline">
                                            <input type="hidden" name="edit_review_id" value="<?php echo $review['review_id']; ?>">
                                            <input type="hidden" name="review_text" value="<?php echo htmlspecialchars($filtered_review_text); ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No reviews yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column for Add/Edit Review -->
        <div class="col-md-4">
            <?php if (isset($_GET['edit_review_id'])): ?>
                <!-- Edit Review Form -->
                <div class="edit-review-form">
                    <h4>Edit Your Review</h4>
                    <hr>
                    <div class="card">
                        <div class="card-body">
                            <form id="edit-review-form" action="" method="POST">
                                <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($_GET['edit_review_id']); ?>">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['product_id']); ?>">
                                <textarea class="form-control" name="review_text" rows="3" required><?php echo htmlspecialchars($_GET['review_text']); ?></textarea>
                                <div class="mt-3">
                                    <button type="submit" name="submit_review" class="btn btn-review">Update Review</button>
                                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?product_id=<?php echo $product_id; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                                </div>
                            </form>
                            <!-- Delete Review Form -->
                            <form id="delete-review-form" action="" method="POST" class="mt-3">
                                <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($_GET['edit_review_id']); ?>">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['product_id']); ?>">
                                <button type="submit" name="delete_review" class="btn btn-danger w-100">Delete Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Add Review Form -->
                <div class="add-review-form">
                    <h4>Leave a Review</h4>
                    <hr>
                    <?php if (!isset($_SESSION['logged_in'])): ?>
                        <div class="card">
                            <div class="card-body">
                                <a href="login.php" class="btn btn-review">Log in to leave a review</a>
                            </div>
                        </div>
                    <?php elseif (isset($_SESSION['user_id']) && $purchase_count > 0): ?>
                        <div class="card">
                            <div class="card-body">
                                <form id="submit-review-form" action="submit_review.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                    <textarea name="review_text" required class="form-control" rows="3" placeholder="Write a review..."></textarea>
                                    <button type="submit" class="btn btn-review mt-3">Submit Review</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <p>You must purchase this product to leave a review.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


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

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const productID = "<?php echo htmlspecialchars($product_id); ?>"; // Embed PHP product ID
        const savedProductID = sessionStorage.getItem("productID");
        const savedScrollPosition = sessionStorage.getItem("scrollPosition");

        // Restore scroll position only if the product ID matches
        if (savedProductID === productID && savedScrollPosition !== null) {
            window.scrollTo(0, parseInt(savedScrollPosition, 10));
        } else {
            // Reset scroll position for different product IDs
            window.scrollTo(0, 0);
        }

        // Clear session storage for a fresh state
        sessionStorage.removeItem("productID");
        sessionStorage.removeItem("scrollPosition");

        // Save scroll position before self-redirects (same product)
        const forms = document.querySelectorAll("form");
        const links = document.querySelectorAll("a");

        forms.forEach(form => {
            form.addEventListener("submit", () => {
                sessionStorage.setItem("scrollPosition", window.scrollY);
                sessionStorage.setItem("productID", productID);
            });
        });

        links.forEach(link => {
            link.addEventListener("click", (e) => {
                // Save scroll only for internal links pointing to the same product
                if (link.hostname === window.location.hostname && link.href.includes(`product_id=${productID}`)) {
                    sessionStorage.setItem("scrollPosition", window.scrollY);
                    sessionStorage.setItem("productID", productID);
                }
            });
        });
    });
</script>



<?php include('layouts/footer.php');?>