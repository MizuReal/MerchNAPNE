<?php

session_start();
include('connection.php');

// If user is not logged in
if (!isset($_SESSION['logged_in'])) {
    header('location: ../login.php?message=Please login/register to place an order');
    exit();  // Ensure the script stops after the redirect
}

// If user is logged in
if (isset($_POST['place_order'])) {
    // Step 1: Get user info and store it in database
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $order_cost = $_SESSION['total'];
    $order_status = "on hold";
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    // Insert the order into the database
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isiisss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);  // i = int, s = string
    $stmt->execute();

    // Step 2: Issue new order and store order info in the database
    $order_id = $stmt->insert_id;

    // Step 3: Get products from cart (from session)
    foreach ($_SESSION['cart'] as $key => $product) {
        $product_id = $product['product_id'];
        $product_name = $product['product_name'];
        $product_image = $product['product_image'];
        $product_price = $product['product_price'];
        $product_quantity = $product['product_quantity'];

        // Step 4: Store each single item in the order_items database
        $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param('iissiiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
        $stmt1->execute();
    }

    // Step 5: Optionally clear the cart (uncomment when ready)
    // unset($_SESSION['cart']);

    // Step 6: Inform the user whether everything is fine or there is a problem
    header('Location: ../payment.php?order_status=Order placed successfully');
    exit();
}
?>
