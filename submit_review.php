<?php
$servername = "studentdb-maria.gl.umbc.edu";
$username = "mvijay1";
$password = "mvijay1";
$dbname = "mvijay1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$create_table_sql = "
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    campus_id VARCHAR(100),
    food_item VARCHAR(100),
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table_sql);

$customer_name = $_POST['name'] ?? '';
$campus_id = $_POST['restaurant'] ?? '';
$food_item = '';
$rating = (int)($_POST['stars'] ?? 0);
$comment = $_POST['review'] ?? '';

$stmt = $conn->prepare("INSERT INTO reviews (customer_name, campus_id, food_item, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $customer_name, $campus_id, $food_item, $rating, $comment);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: goodbye.html");
exit();
?>
