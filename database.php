<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "gmails";
date_default_timezone_set('Asia/Kolkata'); // Or your actual zone

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("connection failed:" . $conn->connect_error);
}
?>