<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "accommodationfinder";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get property ID from the request
$id = $_GET['id'];

// Update status column to 'rejected' for the given property ID
$sql = "UPDATE advertisements SET status='rejected' WHERE advertisement_id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Status updated successfully";
} else {
    echo "Error updating status: " . $conn->error;
}

$conn->close();
?>
