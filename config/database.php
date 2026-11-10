<?php
$servername = "localhost";
$username = "root";
$password = "03052006";
$dbname = "quanlysieuthi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>