<?php
// Assuming you have a database connection established
// Replace database credentials and connection code with your actual database details

$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve username from the form submission
$username = $_POST['username'];

// Find user ID by username
$sql = "SELECT id FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the user ID
    $row = $result->fetch_assoc();
    $userId = $row['id'];

    // Retrieve advertisements by user ID
    $sql = "SELECT * FROM advertisements WHERE user_id = '$userId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display advertisements
        while($row = $result->fetch_assoc()) {
            // Display advertisement details
            echo "Title: " . $row["title"] . "<br>";
            echo "Location: " . $row["location"] . "<br>";
            echo "Rent: " . $row["rent"] . "<br>";
            // Add more details as needed
            echo "<br>";
        }
    } else {
        echo "No advertisements found for this user.";
    }
} else {
    echo "User not found.";
}

// Close connection
$conn->close();
?>
