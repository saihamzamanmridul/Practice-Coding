<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "freelancing_birds"; // ✅ Correct DB Name
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
