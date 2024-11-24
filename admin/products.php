<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('location: login.php?error=Please login first');
    exit;
}

include('../server/connection.php');


// Set the number of products per page
$productsPerPage = 6;

// Determine the current page and calculate the offset
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;

// Handle product deletion
if (isset($_POST['delete'])) {
    $product_id = $_POST['product_id'];

    // Delete the product from the database
    $delete_query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        // Get the new total number of products
        $totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
        $totalPages = ceil($totalProducts / $productsPerPage);
        
        // If current page is greater than total pages, redirect to last page
        // Otherwise stay on current page
        if ($page > $totalPages && $totalPages > 0) {
            header("Location: products.php?page=" . $totalPages);
        } else {
            header("Location: products.php?page=" . $page);
        }
        exit();
    } else {
        echo "Error deleting product.";
    }
}

// Fetch products with pagination
$stmt = $conn->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $productsPerPage, $offset);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get the total number of products for pagination
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$totalPages = ceil($totalProducts / $productsPerPage);

?>

<?php
// Fetch all categories for the dropdown
$categories = $conn->query("SELECT DISTINCT product_category FROM products")->fetch_all(MYSQLI_ASSOC);

// Capture the selected category
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Modify the SQL query based on the selected category
if ($selectedCategory) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_category = ? LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $selectedCategory, $productsPerPage, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $productsPerPage, $offset);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get the total number of products for pagination (filtered or all)
if ($selectedCategory) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_category = ?");
    $stmt->bind_param("s", $selectedCategory);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products");
}
$stmt->execute();
$totalProducts = $stmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalProducts / $productsPerPage);
$stmt->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Products</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include('sidemenu.php'); ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include('header.php'); ?>

                <div class="sticky-header">
                    <h2>Manage Products</h2>
                    <div class="controls-container">
                        <form action="products.php" method="GET" class="filter-form">
                            <label for="category">Filter by Category:</label>
                            <select name="category" id="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['product_category']); ?>" 
                                        <?php echo ($selectedCategory == $category['product_category']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['product_category']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Filter</button>
                        </form>

                        <div class="pagination">
                            <!-- Previous arrow -->
                            <a href="products.php?page=<?php echo max(1, $page - 1); ?>&category=<?php echo urlencode($selectedCategory); ?>" 
                                class="pagination-arrow <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    ←
                            </a>

                            <?php
                                $paginationRange = 1; // Number of pages to show on each side of current page
                                
                                for ($i = 1; $i <= $totalPages; $i++):
                                    // Always show first page, last page, current page, and pages within range
                                    if ($i == 1 || $i == $totalPages || 
                                        abs($page - $i) <= $paginationRange):
                            ?>
                                    <a href="products.php?page=<?php echo $i; ?>&category=<?php echo urlencode($selectedCategory); ?>" 
                                        class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                                            <?php echo $i; ?>
                                    </a>
                            <?php
                                // Add ellipsis after first page if there's a gap
                                elseif ($i == 2 && $page - $paginationRange > 2):
                            ?>
                                    <span class="pagination-ellipsis">...</span>
                            <?php
                                // Add ellipsis before last page if there's a gap
                                elseif ($i == $totalPages - 1 && $page + $paginationRange < $totalPages - 1):
                            ?>
                                    <span class="pagination-ellipsis">...</span>
                            <?php
                                endif;
                                endfor;
                            ?>

                            <!-- Next arrow -->
                            <a href="products.php?page=<?php echo min($totalPages, $page + 1); ?>&category=<?php echo urlencode($selectedCategory); ?>" 
                            class="pagination-arrow <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                →
                            </a>
                        </div>
                    </div>
                </div>

            <div class="product-management">

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Color</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product) : ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars('../assets/imgs/' . $product['product_image']); ?>" 
                                            alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                            class="product-img">   
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_description']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                                    
                                    <td>
                                        <div class="button-container">
                                            <a href="edit_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn edit-btn">Edit</a>
                                            
                                            <!-- Form for Delete Button -->
                                            <form action="products.php?page=<?php echo $page; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                <input type="hidden" name="page" value="<?php echo $page; ?>">
                                                <button type="submit" name="delete" class="btn delete-btn">Delete</button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
