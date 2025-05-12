<?php
$servername = "studentdb-maria.gl.umbc.edu";
$username = "mvijay1";
$password = "mvijay1";
$dbname = "mvijay1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed: " . $conn->connect_error;
    exit();
}

$name = $_POST['name'];
$restaurant = $_POST['restaurant'];
$review = $_POST['review'];
$stars = $_POST['stars'];

$sql = "INSERT INTO reviews (name, restaurant, review, stars) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $name, $restaurant, $review, $stars);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "redirect" => "goodbye.html"]);
} else {
    http_response_code(500);
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
