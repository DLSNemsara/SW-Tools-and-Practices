<?php
session_start(); // Start session if not already started

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_listing'])) {
    // Code for adding a listing
    $title = $_POST["title"];
    $rent = $_POST["rent"];
    $rooms = $_POST["rooms"];
    $beds = $_POST["beds"];
    $longitude = $_POST["longitude"];
    $latitude = $_POST["latitude"];
    $landlord_id = $_SESSION["landlord_id"]; // Retrieve user_id from session

    // Validate inputs
    if (empty($title) || empty($location) || empty($rent) || empty($rooms) || empty($beds) || empty($longitude) || empty($latitude)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Check if image file is uploaded
        if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
            // Check for upload errors
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo "<script>alert('Error uploading image. Please try again.');</script>";
                exit();
            }

            // Read image file
            $image_data = file_get_contents($_FILES['image']['tmp_name']); // Directly read image file contents

            // Connect to your database (replace with your own database credentials)
            $servername = "localhost";
            $db_username = "root";
            $db_password = "";
            $dbname = "accommodationfinder";

            // Create connection
            $conn = new mysqli($servername, $db_username, $db_password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Prepare and execute SQL query to insert listing into database
            $sql = "INSERT INTO advertisements (title, rent, rooms, beds, longitude, latitude, image_data, landlord_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsiiisi", $title, $rent, $rooms, $beds, $longitude, $latitude, $image_data, $landlord_id); 

            if ($stmt->execute()) {
                echo "<script>alert('Listing added successfully!');</script>";
                // Redirect back to landlord page
                header("Location: landlord_prop.html");
                exit(); // Stop further execution
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

            // Close statement
            $stmt->close();

            // Close connection
            $conn->close();
        } else {
            echo "<script>alert('Please select an image.');</script>";
        }
    }
}

// Check if user is logged in
if (!isset($_SESSION["landlord_id"])) {
    // Redirect to login page or handle unauthorized access
    header("Location: login.html");
    exit();
}
?>
