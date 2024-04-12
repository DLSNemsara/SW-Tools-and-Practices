<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve advertisement ID from the form
    $advertisement_id = $_POST['advertisement_id'];

    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "accommodationfinder";

    $connection = new mysqli($servername, $username, $password, $database);
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Prepare update query
    $update_query = "UPDATE advertisements SET 
                     title = ?,  
                     rent = ?, 
                     rooms = ?, 
                     beds = ?, 
                     longitude = ?, 
                     latitude = ?";

    // Check if a new image is uploaded
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK && $_FILES['image']['size'] > 0) {
        // Specify the directory where you want to store the uploaded image
        $uploadDirectory = "uploads/";

        // Generate a unique filename to prevent overwriting existing files
        $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $uploadDirectory . $filename;

        // Move the uploaded image to the target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Image uploaded successfully
            $image_path = $targetFilePath;
            $update_query .= ", image_data = ?";
        } else {
            echo "Error moving uploaded file.";
            exit; // Exit if there's an error moving the uploaded file
        }
    }

    // Add the advertisement ID to the update query
    $update_query .= " WHERE advertisement_id = ?";

    // Prepare and bind parameters
    $stmt = $connection->prepare($update_query);
    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }

    // Bind parameters
    if (isset($image_path)) {
        $stmt->bind_param("sdiiddsi", $title, $rent, $rooms, $beds, $longitude, $latitude, $image_path, $advertisement_id);
    } else {
        $stmt->bind_param("sdiiddi", $title, $rent, $rooms, $beds, $longitude, $latitude, $advertisement_id);
    }

    // Set parameters
    $title = $_POST['title'];
    $rent = $_POST['rent'];
    $rooms = $_POST['rooms'];
    $beds = $_POST['beds'];
    $longitude = $_POST['longitude']; // Assuming these are provided in the form
    $latitude = $_POST['latitude'];   // Assuming these are provided in the form

    // Execute the update query
    if ($stmt->execute()) {
        echo "Advertisement updated successfully.";
        header("Location: Show_listings.php");
    exit();
    } else {
        echo "Error updating advertisement: " . $connection->error;
    }

    // Close the statement
    $stmt->close();

    // Close the database connection
    $connection->close();
}
?>
