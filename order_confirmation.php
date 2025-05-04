<?php
include 'db_connect.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
    die("Invalid order ID.");
}

// Get order info
$order_result = $conn->query("SELECT * FROM orders WHERE order_id = $order_id");
if ($order_result->num_rows == 0) {
    die("Order not found.");
}
$order = $order_result->fetch_assoc();

// Get order items
$items_result = $conn->query("
    SELECT oi.*, p.product_name 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
");

echo "<h1>Order Confirmation</h1>";
echo "<p>Order ID: {$order['order_id']}</p>";
echo "<p>Pickup Time: {$order['pickup_time']}</p>";
echo "<p>Payment Method: {$order['payment_method']}</p>";
echo "<hr>";
echo "<h3>Items:</h3>";

while ($item = $items_result->fetch_assoc()) {
    echo "<p>{$item['product_name']} - Qty: {$item['quantity']} @ \${$item['price']}</p>";
}

$conn->close();
?>
