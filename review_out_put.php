<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Reviews</title>
    <link rel="stylesheet" href="design.css">
    <style>
        .review-list {
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
        }
        .review-card {
            background: #fff9db;
            border: 2px solid #d4a500;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .review-card h3 {
            margin: 0;
            color: #8c6b00;
        }
        .review-card .stars {
            color: gold;
            font-size: 1.2rem;
        }
        .review-card p {
            margin: 10px 0;
        }
        .review-card small {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="review-list">
        <h1 style="text-align:center;">All Reviews</h1>
        <?php
        $conn = new mysqli("studentdb-maria.gl.umbc.edu", "mvijay1", "mvijay1", "mvijay1");
        if ($conn->connect_error) {
            die("Database error: " . $conn->connect_error);
        }

        $result = $conn->query("SELECT * FROM reviews ORDER BY review_date DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<div class='review-card'>";
            echo "<h3>" . htmlspecialchars($row['customer_name']) . " at " . htmlspecialchars($row['campus_id']) . "restaurant""</h3>";
            echo "<p class='stars'>" . str_repeat("⭐", $row['rating']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['comment'])) . "</p>";
            echo "<small>Submitted on " . $row['review_date'] . "</small>";
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>