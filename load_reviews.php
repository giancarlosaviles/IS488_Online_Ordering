<?php
$servername = "studentdb-maria.gl.umbc.edu";
$username = "mvijay1";
$password = "mvijay1";
$dbname = "mvijay1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "Database connection failed.";
    exit();
}

$sql = "SELECT name, restaurant, review, stars FROM reviews ORDER BY id DESC LIMIT 10";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div class='review-box'>";
    echo "<strong>" . htmlspecialchars($row['name']) . "</strong> on <em>" . htmlspecialchars($row['restaurant']) . "</em><br>";
    echo str_repeat("⭐", $row['stars']) . "<br>";
    echo "<p>" . nl2br(htmlspecialchars($row['review'])) . "</p>";
    echo "</div>";
}

$conn->close();
?>