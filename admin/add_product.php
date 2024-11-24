<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('location: login.php?error=Please login first');
    exit;
}

include('../server/connection.php');

// Handle form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_special_offer = $_POST['product_special_offer'] ?? 0;
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
            $uploaded_images[] = null; // No image uploaded
        }
    }

    // Insert new product details into the database
    $stmt = $conn->prepare("INSERT INTO products (product_name, product_category, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssss",
        $product_name,
        $product_category,
        $product_description,
        $uploaded_images[0],
        $uploaded_images[1],
        $uploaded_images[2],
        $uploaded_images[3],
        $product_price,
        $product_special_offer,
        $product_color
    );

    if ($stmt->execute()) {
        $success = "Product added successfully!";
    } else {
        $error = "Failed to add product. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
            <div class="add-product-form">
                <h2>Add New Product</h2>
                <?php if (isset($success)): ?>
                    <p class="success"><?php echo htmlspecialchars($success); ?></p>
                <?php elseif (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-container">
                        <div class="form-main-content">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text" name="product_name" id="product_name" required>
                            </div>
                            <div class="form-group">
                                <label for="product_category">Category</label>
                                <input type="text" name="product_category" id="product_category" required>
                            </div>
                            <div class="form-group">
                                <label for="product_description">Description</label>
                                <textarea name="product_description" id="product_description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="product_price">Price</label>
                                <input type="number" name="product_price" id="product_price" required>
                            </div>
                            <div class="form-group">
                                <label for="product_special_offer">Special Offer</label>
                                <input type="number" name="product_special_offer" id="product_special_offer">
                            </div>
                            <div class="form-group">
                                <label for="product_color">Color</label>
                                <input type="text" name="product_color" id="product_color" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn save-btn">Add Product</button>
                                <a href="products.php" class="btn cancel-btn">Cancel</a>
                            </div>
                        </div>

                        <div class="form-image-section">
                            <div class="image-preview-container">
                                <label for="product_image1">Main Image</label>
                                <input type="file" name="product_image1" id="product_image1">
                            </div>
                            <div class="image-preview-container">
                                <label for="product_image2">Image 2</label>
                                <input type="file" name="product_image2" id="product_image2">
                            </div>
                            <div class="image-preview-container">
                                <label for="product_image3">Image 3</label>
                                <input type="file" name="product_image3" id="product_image3">
                            </div>
                            <div class="image-preview-container">
                                <label for="product_image4">Image 4</label>
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
