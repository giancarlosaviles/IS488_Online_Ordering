<?php
// Connect to MySQL
$host = 'localhost';
$db = 'campus_food';
$user = 'root';
$pass = ''; // use your actual password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$pickup_time = $conn->real_escape_string($_POST['pickup_time']);
$payment_method = $conn->real_escape_string($_POST['payment_method']);
$card_number = $conn->real_escape_string($_POST['card_number']);

// Save to orders table
$sql = "INSERT INTO orders (pickup_time, payment_method, card_number)
        VALUES ('$pickup_time', '$payment_method', '$card_number')";

if ($conn->query($sql) === TRUE) {
  header("Location: confirmation.html");
  exit();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
