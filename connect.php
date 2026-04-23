<?php
$servername = "localhost";
$username   = "root";
$password   = "root";
$database   = "internship_management_system";
//create connection
$conn = new mysqli($servername, $username, $password, $database);
//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>