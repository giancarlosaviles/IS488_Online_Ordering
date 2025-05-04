<?php
$host = 'studentdb-maria.gl.umbc.edu';
$db = 'mvijay1';
$user = 'mvijay1';
$pass = 'mvijay1';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
