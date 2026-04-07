<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "timehouse";

$conn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8mb4"); // Quan trọng: Hiển thị tiếng Việt

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>