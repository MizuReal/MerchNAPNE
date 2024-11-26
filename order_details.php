<?php include('layouts/header.php');?>

<?php
session_start();
include('server/connection.php');

// Function to calculate the total order price
function calculateTotalOrderPrice($order_details) {
    $total = 0;
    while ($row = $order_details->fetch_assoc()) {
        $product_price = $row['product_price'];
        $product_quantity = $row['product_quantity'];
        $total += ($product_price * $product_quantity);
    }
    // Reset the result pointer after iteration
    $order_details->data_seek(0);
    return $total;
}

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    
    // Get order details and status
    $stmt = $conn->prepare("SELECT oi.*, o.order_status 
                            FROM order_items oi 
                            JOIN orders o ON oi.order_id = o.order_id 
                            WHERE oi.order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();

    if ($order_details->num_rows > 0) {
        $order_total_price = calculateTotalOrderPrice($order_details); // Calculate the total order price
        $_SESSION['total'] = $order_total_price; // Store the total in session
        $first_row = $order_details->fetch_assoc();
        $order_status = $first_row['order_status'];
        $order_details->data_seek(0); // Reset pointer after fetching
    } else {
        $order_total_price = 0;
        $order_status = "unknown";
    }
} else {
    header('location: account.php');
    exit;
}
?>

<!-- Orders Details-->
<section id="orders" class="orders container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold text-center">Order Details</h2>
        <hr class="mx-auto">
    </div>

    <table class="mt-5 pt-5 mx-auto">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Order Date</th>
        </tr>

        <?php if ($order_details->num_rows > 0) { ?>
            <?php while ($row = $order_details->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assets/imgs/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" />
                            <div>
                                <p class="mt-3"><?php echo htmlspecialchars($row['product_name']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td> 
                        <span>$<?php echo htmlspecialchars($row['product_price']); ?></span>
                    </td>
                    <td> 
                        <span><?php echo htmlspecialchars($row['product_quantity']); ?></span>
                    </td>
                    <td> 
                        <span><?php echo htmlspecialchars($row['order_date']); ?></span>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4" class="text-center">You don't have any orders yet</td>
            </tr>
        <?php } ?>
    </table>

    <?php if (isset($order_status) && strtolower($order_status) == "on hold") { ?>
        <form style="float: right;" method="POST" action="payment_processor.php">
            <input type="hidden" name="order_total_price" value="<?php echo htmlspecialchars($order_total_price); ?>" />
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>" />
            <input type="submit" name="order_pay_button" class="btn btn-primary" value="Pay Now" />
        </form>
    <?php } ?>
</section>

<?php include('layouts/footer.php'); ?>
