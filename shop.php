<?php include('layouts/header.php');?>




<?php
include('server/connection.php');

// Prepare statements to prevent SQL injection
$categoryQuery = $conn->prepare("SELECT DISTINCT product_category FROM products");
$categoryQuery->execute();
$categories = $categoryQuery->get_result();

// Sanitize inputs
$category = isset($_POST['category']) ? filter_var($_POST['category'], FILTER_SANITIZE_STRING) : '';
$sort = isset($_POST['sort']) ? filter_var($_POST['sort'], FILTER_SANITIZE_STRING) : 'newest';
$search = isset($_POST['search']) ? filter_var($_POST['search'], FILTER_SANITIZE_STRING) : '';

// Prepare the base query with placeholders
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND product_category = ?";
    $params[] = $category;
}

if ($search) {
    $sql .= " AND (product_name LIKE ? OR product_description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Add sorting
switch ($sort) {
    case 'oldest':
        $sql .= " ORDER BY product_id ASC";
        break;
    case 'cheapest':
        $sql .= " ORDER BY product_price ASC";
        break;
    case 'expensive':
        $sql .= " ORDER BY product_price DESC";
        break;
    default:
        $sql .= " ORDER BY product_id DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$products = $stmt->get_result();
?>

<!-- Code Line Stats Here -->

    <div class="container my-5 pt-5">
        <div class="row">
        <!-- Products Section (Left Side) -->
<div class="col-lg-9 order-2 order-lg-1">
    <div class="text-center mb-5">
        <h3>Our Products</h3>
        <hr class="mx-auto" style="width: 90px;">
        <p>Happy Shopping!</p>
    </div>

    <div class="row g-4 product-grid">
        <?php while ($row = $products->fetch_assoc()) { ?>
            <div class="product text-center col-lg-3 col-md-4">
                <img class="img-fluid mb-3" src="assets/imgs/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>"/>
                <div class="star mb-2">
                    <?php for($i = 0; $i < 5; $i++) { ?>
                        <i class="fas fa-star"></i>
                    <?php } ?>
                </div>
                <h5 class="p-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
                <h4 class="p-price">$<?php echo number_format($row['product_price'], 2); ?></h4>
                <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>"><button class="buy-btn">Buy Now</button></a>
            </div>
        <?php } ?>
    </div>
</div>


            <!-- Filter Section (Right Side) -->
            <div class="col-lg-3 order-1 order-lg-2 mb-4 mb-lg-0">
                <div class="filter-sidebar">
                    <h4 class="mb-4">Filters</h4>
                    <form action="shop.php" method="POST">
                        <!-- Search Box -->
                        <div class="search-box">
                            <label class="form-label">Search Products</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php while ($cat = $categories->fetch_assoc()) { ?>
                                    <option value="<?php echo htmlspecialchars($cat['product_category']); ?>" 
                                        <?php echo $category === $cat['product_category'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($cat['product_category'])); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Sort Filter -->
                        <div class="mb-4">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                                <option value="cheapest" <?php echo $sort === 'cheapest' ? 'selected' : ''; ?>>Cheapest</option>
                                <option value="expensive" <?php echo $sort === 'expensive' ? 'selected' : ''; ?>>Most Expensive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-custom w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <?php include('layouts/footer.php');?>
