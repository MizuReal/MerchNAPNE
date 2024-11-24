<?php include('layouts/header.php');?>

<?php
session_start();

// Check if payment form is submitted
if (isset($_POST['order_pay_button'])) {
    // Retrieve order details from POST data
    $order_id = $_POST['order_id'];
    $order_total_price = $_POST['order_total_price'];

    // Store the total price in the session
    $_SESSION['total'] = $order_total_price; // Update session with the correct total
}
?>

<!-- Payment Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Payment</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">
        <?php if (isset($_SESSION['total']) && $_SESSION['total'] != 0) { ?>
            <p>Total Payment: $<?php echo $_SESSION['total']; ?> </p>
            <form action="payment_processor.php" method="POST">
                <input class="btn btn-primary" type="submit" name="pay_now" value="Pay Now" />
            </form>
        <?php } else if (isset($_POST['order_total_price']) && $_POST['order_total_price'] != 0) { ?>
            <p>Total Payment: $<?php echo $_POST['order_total_price']; ?> </p>
        <?php } else { ?>
            <p>You did not purchase anything.</p>
        <?php } ?>
    </div>
</section>

<?php include('layouts/footer.php'); ?>
