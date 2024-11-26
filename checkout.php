<?php include('layouts/header.php'); ?> 

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php'; // Store the page to redirect to after login
    header('Location: login.php?message=Please login to proceed with checkout');
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// Calculate total amount
$totalAmount = isset($_SESSION['total']) ? number_format($_SESSION['total'], 2) : '0.00';
?>

<!-- Checkout Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Checkout</h2>
        <hr class="mx-auto">
    </div>

    <!-- Cart Review Section -->
    <div class="container">
        <h4 class="font-weight-bold">Review Your Cart</h4>
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($_SESSION['cart'] as $key => $product): ?>
                        <tr>
                            <td>
                                <img src="assets/imgs/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>" width="50">
                                <?php echo $product['product_name']; ?>
                            </td>
                            <td class="text-center"><?php echo $product['product_quantity']; ?></td>
                            <td class="text-center">$<?php echo $product['product_quantity'] * $product['product_price']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- Checkout Form -->
    <div class="container mx-auto">
        <form id="checkout-form" method="POST" action="server/place_order.php">
            <p class="text-center" style="color: red;">
                <?php if (isset($_GET['message'])) { echo htmlspecialchars($_GET['message']); } ?>
            </p>

            <!-- User Info Form -->
            <div class="form-group checkout-small-element">
                <label for="checkout-name">Name</label>
                <input type="text" class="form-control" id="checkout-name" name="name" 
                    value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>"
                    placeholder="Name" required />
            </div>
            <div class="form-group checkout-small-element">
                <label for="checkout-email">Email</label>
                <input type="email" class="form-control" id="checkout-email" name="email" 
                    value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>"
                    placeholder="Email" required />
            </div>
            <div class="form-group checkout-small-element">
                <label for="checkout-phone">Phone</label>
                <input type="tel" class="form-control" id="checkout-phone" name="phone" 
                    value="<?php echo isset($_SESSION['user_phone']) ? htmlspecialchars($_SESSION['user_phone']) : ''; ?>"
                    placeholder="Phone" required />
            </div>
            <div class="form-group checkout-small-element">
                <label for="checkout-city">City</label>
                <input type="text" class="form-control" id="checkout-city" name="city" 
                    value="<?php echo isset($_SESSION['user_city']) ? htmlspecialchars($_SESSION['user_city']) : ''; ?>"
                    placeholder="City" required />
            </div>
            <div class="form-group checkout-large-element">
                <label for="checkout-address">Address</label>
                <input type="text" class="form-control" id="checkout-address" name="address" 
                    value="<?php echo isset($_SESSION['user_address']) ? htmlspecialchars($_SESSION['user_address']) : ''; ?>"
                    placeholder="Address" required />
            </div>

            <!-- Cart Summary and Submit Button -->
            <div class="form-group checkout-btn-container">
                <p>Total amount: $<?php echo $totalAmount; ?></p>
                <input type="submit" class="btn btn-primary" id="checkout-btn" name="place_order" value="Place order" />
            </div>
        </form>
    </div>
</section>

<?php include('layouts/footer.php'); ?>
