<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["landlord_id"])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_listing'])) {
    // Validate inputs
    $title = $_POST["title"];
    $rent = $_POST["rent"];
    $rooms = $_POST["rooms"];
    $beds = $_POST["beds"];
    $longitude = $_POST["longitude"];
    $latitude = $_POST["latitude"];

    if (empty($title) || empty($rent) || empty($rooms) || empty($beds) || empty($longitude) || empty($latitude)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Check if image file is uploaded
        if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
            // Define folder where images will be stored
            $uploadDirectory = "uploads/";

            // Generate a unique filename to prevent overwriting existing files
            $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $targetFilePath = $uploadDirectory . $filename;

            // Check if the file has been uploaded successfully
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Image uploaded successfully, store the path in the database
                $image_data = $targetFilePath;
            } else {
                echo "<script>alert('Error uploading image.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Please select an image.');</script>";
            exit();
        }

        // Connect to the database (Replace with your database details)
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "accommodationfinder";

        $conn = new mysqli($servername, $db_username, $db_password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute SQL query to insert listing into database
        $sql = "INSERT INTO advertisements (title, rent, rooms, beds, longitude, latitude, image_data, landlord_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sdiiddsi", $title, $rent, $rooms, $beds, $longitude, $latitude, $image_data, $_SESSION["landlord_id"]);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Listing added successfully!');</script>";
            header("Location: landlord_prop.html");
            exit();
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
