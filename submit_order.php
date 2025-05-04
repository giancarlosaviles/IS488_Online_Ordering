<?php
session_start();
include 'db_connect.php'; // assumes db_connect.php defines $conn

// Check if cart and user info exist
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Cart is empty.");
}

$pickup_time = $conn->real_escape_string($_POST['pickup_time']);
$payment_method = $conn->real_escape_string($_POST['payment_method']);
$card_number = $conn->real_escape_string($_POST['card_number']);
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0; // optional

// Insert into orders table
$order_sql = "INSERT INTO orders (user_id, pickup_time, payment_method, card_number, order_date)
              VALUES ($user_id, '$pickup_time', '$payment_method', '$card_number', NOW())";

if ($conn->query($order_sql) === TRUE) {
    $order_id = $conn->insert_id;

    // Insert each cart item into order_items
    foreach ($_SESSION['cart'] as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        $price = floatval($item['price']);

        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                     VALUES ($order_id, $product_id, $quantity, $price)";
        $conn->query($item_sql);
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to confirmation
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
