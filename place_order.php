<?php
// place_order.php

// Include database configuration
require_once "config.php"; // Uses the $link variable from config.php

// Set content type to JSON for the response
header('Content-Type: application/json');

// --- Error Reporting (for development, disable or log to file in production) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Get Data from Request Body ---
$jsonPayload = file_get_contents('php://input');
$orderData = json_decode($jsonPayload, true); // true for associative array

// --- Basic Validation ---
if (!$orderData) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data received.']);
    exit;
}

// Check for essential data points from the frontend
if (empty($orderData['items']) || !isset($orderData['total']) ||
    empty($orderData['pickupTime']) || empty($orderData['paymentMethod']) ||
    empty($orderData['userType'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required order data from client.']);
    exit;
}

// --- User Identification ---
$user_id = null; // Default to NULL for guests
$guest_name = null;
$guest_email = null;
$customer_name_for_confirmation = 'Student'; // Default for logged-in users

if ($orderData['userType'] === 'student') {
    // In a real app, get user_id from PHP session after login.
    // Using placeholder for now, assuming Users.user_id = 1 exists.
    $user_id = 1; // Placeholder for logged-in student
    // You might fetch student's name from Users table if user_id is available
    // For now, $customer_name_for_confirmation remains "Student"
} elseif ($orderData['userType'] === 'guest' && isset($orderData['guestDetails'])) {
    $guest_name = $orderData['guestDetails']['name'] ?? null;
    $guest_email = $orderData['guestDetails']['email'] ?? null;
    $customer_name_for_confirmation = $guest_name; // Use guest's name for confirmation
    
    if (empty($guest_name)) {
        echo json_encode(['success' => false, 'message' => 'Guest name is required.']);
        exit;
    }
    // Optional: Add more validation for guest_email if it's mandatory
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user type specified.']);
    exit;
}

// --- Prepare Other Data for Database ---
$order_time = date('Y-m-d H:i:s'); // Current timestamp for the order
$status = 'Pending'; // Default order status
$delivery_method = 'Pickup'; // Assuming all orders are for pickup

// Data from JavaScript to be stored
$total_price = $orderData['total'];
$pickup_time_from_js = $orderData['pickupTime'];
$payment_method_from_js = $orderData['paymentMethod'];
$payment_instrument_details = $orderData['paymentInstrument'] ?? null;
$restaurant_name = $orderData['restaurant'] ?? 'Chick-fil-A @ The Commons'; // Default or from JS
$estimated_prep_time = $orderData['estimatedPrepTime'] ?? '15-20 minutes'; // Default or from JS


// --- Database Operations ---
mysqli_autocommit($link, false); // Start transaction for atomicity
$success = true;
$new_order_id = null;
$db_error = ''; // To store any database error messages

// 1. Insert into Orders table
// Ensure your `Orders` table has these columns:
// user_id (INT, NULLABLE), order_time (TIMESTAMP), status (VARCHAR/ENUM), delivery_method (VARCHAR/ENUM),
// total_price (DECIMAL), pickup_time (VARCHAR), payment_method (VARCHAR),
// payment_instrument_details (VARCHAR, NULLABLE), restaurant_name (VARCHAR, NULLABLE),
// estimated_prep_time (VARCHAR, NULLABLE), guest_name (VARCHAR, NULLABLE), guest_email (VARCHAR, NULLABLE)
$sql_order = "INSERT INTO Orders (user_id, order_time, status, delivery_method, total_price, pickup_time, payment_method, payment_instrument_details, restaurant_name, estimated_prep_time, guest_name, guest_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt_order = mysqli_prepare($link, $sql_order)) {
    // Bind parameters: i for integer, d for double/decimal, s for string
    mysqli_stmt_bind_param($stmt_order, "isssdsssssss", 
        $user_id, 
        $order_time,
        $status,
        $delivery_method,
        $total_price,
        $pickup_time_from_js,
        $payment_method_from_js,
        $payment_instrument_details,
        $restaurant_name,
        $estimated_prep_time,
        $guest_name,
        $guest_email
    );

    if (mysqli_stmt_execute($stmt_order)) {
        $new_order_id = mysqli_insert_id($link); // Get the ID of the newly inserted order
    } else {
        $success = false;
        $db_error = "Order insert failed: " . mysqli_stmt_error($stmt_order);
    }
    mysqli_stmt_close($stmt_order);
} else {
    $success = false;
    $db_error = "Order statement prepare failed: " . mysqli_error($link);
}

// 2. Insert into Order_Items table (if order insertion was successful)
if ($success && $new_order_id) {
    $sql_order_item = "INSERT INTO Order_Items (order_id, item_id, quantity, customization) VALUES (?, ?, ?, ?)";
    
    foreach ($orderData['items'] as $item) {
        if ($stmt_item = mysqli_prepare($link, $sql_order_item)) {
            $item_id_from_js = $item['id']; // This is Menu_Items.item_id
            $quantity = $item['quantity'];
            $customization = $item['customization'] ?? null; // Assuming customization might be added

            mysqli_stmt_bind_param($stmt_item, "iiis",
                $new_order_id,
                $item_id_from_js,
                $quantity,
                $customization
            );

            if (!mysqli_stmt_execute($stmt_item)) {
                $success = false;
                $db_error = "Order item insert failed for item ID {$item_id_from_js}: " . mysqli_stmt_error($stmt_item);
                mysqli_stmt_close($stmt_item);
                break; // Exit loop on first item error
            }
            mysqli_stmt_close($stmt_item);
        } else {
            $success = false;
            $db_error = "Order item statement prepare failed: " . mysqli_error($link);
            break; // Exit loop if statement preparation fails
        }
    }
} elseif ($success && !$new_order_id && $db_error === '') { 
    // This case means order insert seemed to succeed (no error set before) but we didn't get an ID.
    $success = false;
    $db_error = "Failed to retrieve new order ID after order insertion. Order might not have been saved.";
}


// --- Commit or Rollback Transaction & Send Response ---
if ($success) {
    mysqli_commit($link);
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $new_order_id,
        'pickup_time' => $pickup_time_from_js, // Pass back for confirmation page
        'total' => $total_price, // Pass back for confirmation page
        'customerName' => $customer_name_for_confirmation // Pass customer name for confirmation
    ]);
} else {
    mysqli_rollback($link);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order. ' . $db_error,
        'debug_order_data' => $orderData // For debugging on client-side if needed
    ]);
}

// Close connection
mysqli_close($link);
?>
