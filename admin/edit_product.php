<?php
session_start();

// Check if admin is not logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('location: login.php?error=Please login first');
    exit;
}

include('../server/connection.php');

// Fetch product details for editing
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        header('location: products.php?error=Product not found');
        exit;
    }
} else {
    header('location: products.php?error=Invalid product ID');
    exit;
}

// Handle form submission for updating the product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_special_offer = $_POST['product_special_offer'];
    $product_color = $_POST['product_color'];

    // Handle image uploads
    $uploaded_images = [];
    for ($i = 1; $i <= 4; $i++) {
        $file_key = "product_image$i";
        if (!empty($_FILES[$file_key]['name'])) {
            $target_dir = "../assets/imgs/";
            $target_file = $target_dir . basename($_FILES[$file_key]['name']);
            move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file);
            $uploaded_images[] = $_FILES[$file_key]['name'];
        } else {
            $uploaded_images[] = $product["product_image$i"];
        }
    }

    // Update product details in the database
    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_category = ?, product_description = ?, product_image = ?, product_image2 = ?, product_image3 = ?, product_image4 = ?, product_price = ?, product_special_offer = ?, product_color = ? WHERE product_id = ?");
    $stmt->bind_param(
        "ssssssssssi",
        $product_name,
        $product_category,
        $product_description,
        $uploaded_images[0],
        $uploaded_images[1],
        $uploaded_images[2],
        $uploaded_images[3],
        $product_price,
        $product_special_offer,
        $product_color,
        $product_id
    );

    if ($stmt->execute()) {
        header('location: products.php?success=Product updated successfully');
        exit;
    } else {
        $error = "Failed to update product";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <div class="edit-product-form">
                <h2>Edit Product</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-container">
                                <div class="form-main-content">
                                    <div class="form-group">
                                        <label for="product_name">Product Name</label>
                                        <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="product_category">Category</label>
                                        <input type="text" name="product_category" id="product_category" value="<?php echo htmlspecialchars($product['product_category']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="product_description">Description</label>
                                        <textarea name="product_description" id="product_description" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="product_price">Price</label>
                                        <input type="number" name="product_price" id="product_price" value="<?php echo htmlspecialchars($product['product_price']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="product_special_offer">Special Offer</label>
                                        <input type="number" name="product_special_offer" id="product_special_offer" value="<?php echo htmlspecialchars($product['product_special_offer']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="product_color">Color</label>
                                        <input type="text" name="product_color" id="product_color" value="<?php echo htmlspecialchars($product['product_color']); ?>" required>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn save-btn">Save Changes</button>
                                        <a href="products.php" class="btn cancel-btn">Cancel</a>
                                    </div>
                                </div>
                        
                                <div class="form-image-section">
                                    <div class="image-preview-container">
                                        <label for="product_image1">Main Image</label>
                                        <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Current Image" class="current-img">
                                        <input type="file" name="product_image1" id="product_image1">
                                    </div>
                                    <div class="image-preview-container">
                                        <label for="product_image2">Image 2</label>
                                        <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image2']); ?>" alt="Current Image" class="current-img">
                                        <input type="file" name="product_image2" id="product_image2">
                                    </div>
                                    <div class="image-preview-container">
                                        <label for="product_image3">Image 3</label>
                                        <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image3']); ?>" alt="Current Image" class="current-img">
                                        <input type="file" name="product_image3" id="product_image3">
                                    </div>
                                    <div class="image-preview-container">
                                        <label for="product_image4">Image 4</label>
                                        <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image4']); ?>" alt="Current Image" class="current-img">
                                        <input type="file" name="product_image4" id="product_image4">
                                    </div>
                                </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
