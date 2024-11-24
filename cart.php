<?php include('layouts/header.php');?>


<?php
session_start();
// Function to show message
function showMessage($message, $type = 'info') {
    $_SESSION['message'] = array(
        'text' => $message,
        'type' => $type
    );
}

if (isset($_POST['add_to_cart'])) {
    // Validate quantity before adding to cart
    if (!isset($_POST['product_quantity']) || $_POST['product_quantity'] <= 0) {
        showMessage("Please enter a valid quantity greater than 0", "error");
    } else {
        if (isset($_SESSION['cart'])) {
            $product_array_ids = array_column($_SESSION['cart'], "product_id");
            
            if (!in_array($_POST['product_id'], $product_array_ids)) {
                $product_id = $_POST['product_id'];
                $product_array = array(
                    'product_id' => $product_id,
                    'product_name' => $_POST['product_name'],
                    'product_price' => $_POST['product_price'],
                    'product_image' => $_POST['product_image'],
                    'product_quantity' => (int)$_POST['product_quantity']
                );
                
                $_SESSION['cart'][$product_id] = $product_array;
            } else {
                showMessage("Product is already added to the cart", "info");
            }
        } else {
            $product_id = $_POST['product_id'];
            $product_array = array(
                'product_id' => $product_id,
                'product_name' => $_POST['product_name'],
                'product_price' => $_POST['product_price'],
                'product_image' => $_POST['product_image'],
                'product_quantity' => (int)$_POST['product_quantity']
            );
            
            $_SESSION['cart'][$product_id] = $product_array;
        }
        calculateTotalCart();
    }

} else if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
            showMessage("Please Choose a Product", "info");
        }
        
        calculateTotalCart();
    }

} else if (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = (int)$_POST['product_quantity'];
    
    // Validate the new quantity
    if ($product_quantity <= 0) {
        showMessage("Please enter a valid quantity greater than 0", "error");
    } else if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;
        calculateTotalCart();
    }

} else {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        showMessage("Cart is empty", "info");
    }
}

function calculateTotalCart() {
    $total = 0;

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $product) {
            $price = (float)$product['product_price'];
            $quantity = (int)$product['product_quantity'];
            
            if ($price > 0 && $quantity > 0) {
                $total += ($price * $quantity);
            }
        }
    }

    $_SESSION['total'] = number_format($total, 2, '.', '');
    return $_SESSION['total'];
}

if (isset($_POST['empty_cart'])) {
    // Clear the cart
    unset($_SESSION['cart']);
    // Optionally clear the total as well
    unset($_SESSION['total']);
    showMessage("Your cart has been emptied", "info");
    header("Location: cart.php"); // Redirect to refresh the page
    exit;
}
?>

   <!-- Cart Section -->
<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Your Cart</h2>
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $_SESSION['message']['type'] == 'error' ? 'alert-danger' : 'alert-info'; ?> mt-3">
                <?php 
                echo $_SESSION['message']['text'];
                unset($_SESSION['message']); // Clear the message after displaying
                ?>
            </div>
        <?php endif; ?>
        <hr>
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>

        <?php 
        if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach($_SESSION['cart'] as $key => $value) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assets/imgs/<?php echo $value['product_image']; ?>"/>
                            <div>
                                <p><?php echo $value['product_name']; ?></p>
                                <small><span>$</span><?php echo $value['product_price']; ?></small>
                                <br>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>"/>
                                    <input type="submit" name="remove_product" class="remove-btn" value="remove" />
                                </form>
                            </div>
                        </div>
                    </td>


                    <!--To Prevent User having Negative and 0 Quantity-->
                    <td>
                        <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>"/>
                        <input type="number" 
                            name="product_quantity" 
                            value="<?php echo $value['product_quantity']; ?>"
                            min="1" 
                            step="1" 
                            required 
                            oninput="validity.valid||(value='1');"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"/>
                        <input type="submit" class="edit-btn" value="edit" name="edit_quantity"/>
                        </form>
                    </td>
                    <td>
                        <span>$</span>
                        <span class="product-price"><?php echo $value['product_quantity'] * $value['product_price'];?></span>
                    </td>
                </tr>
            <?php }
        } else {
            echo '<tr><td colspan="3" class="text-center">Your cart is empty</td></tr>';
        }
        ?>
    </table>

    <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <div class="cart-total">
            <table>
                <tr>
                    <td>Total Amount</td>
                    <td>$<?php echo $_SESSION['total'];?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form method ="POST" action="checkout.php">
                <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
            </form>
  <!-- Empty Cart Button -->
  <form method="POST" action="cart.php" style="display:inline;">
            <input type="submit" class="btn btn-danger" value="Empty Cart" name="empty_cart">
        </form>



        </div>
    <?php } ?>
</section>

<?php include('layouts/footer.php');?>