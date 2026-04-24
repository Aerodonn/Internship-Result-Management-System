<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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