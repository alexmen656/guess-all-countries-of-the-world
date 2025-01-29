<?php
$servername = "**servername**";
$username = "**username**";
$password = "**password**";
$dbname = "cityscope";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>