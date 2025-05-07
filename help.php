<?php
// Author: Giancarlos Aviles - Help/FAQ Use Case
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help / FAQ</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .faq-container { max-width: 700px; margin: auto; }
        .faq-item { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        h1, h3 { color: #333; }
    </style>
</head>
<body>
    <h1>Frequently Asked Questions</h1>
    <div class="faq-container">
        <?php
        $sql = "SELECT question, answer FROM FAQ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='faq-item'>";
                echo "<h3>" . htmlspecialchars($row['question']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['answer']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No FAQs available.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
