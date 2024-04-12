<?php
// Initialize variables to store advertisement details
$title = $location = $rent = $rooms = $beds = $longitude = $latitude= $image_data = "";

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

    // Prepare select query
    $select_query = "SELECT title, rent, rooms, beds, longitude, latitude, image_data 
                     FROM advertisements 
                     WHERE advertisement_id = ?";

    // Prepare and bind parameters
    $stmt = $connection->prepare($select_query);
    $stmt->bind_param("i", $advertisement_id);

    // Execute the query
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($title, $rent, $rooms, $beds, $longitude, $latitude, $image_data);

    // Fetch the data
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    // Close the database connection
    $connection->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Advertisement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
     integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <h2>Update Advertisement</h2>
    <form action="update_listing.php" method="POST" enctype="multipart/form-data">
        <!-- Display form fields for updating advertisement details -->
        <div class="input-group">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" value="<?php echo $title; ?>" required><br>
        </div>
        <div class="input-group">
            <label for="rent">Rent:</label><br>
            <input type="number" id="rent" name="rent" step="0.01" value="<?php echo $rent; ?>" required><br>
        </div>
        <div class="input-group">
            <label for="rooms">Number of Rooms:</label><br>
            <input type="number" id="rooms" name="rooms" value="<?php echo $rooms; ?>" required><br>
        </div>
        <div class="input-group">
            <label for="beds">Number of Beds</label><br>
            <input type="number" id="beds" name="beds" value="<?php echo $beds; ?>" required><br>
        </div>
        <div class="input-group">
            <label for="longitude">Longitude:</label><br>
            <input type="number" id="longitude" name="longitude" step="0.000001" value="<?php echo $longitude; ?>" required><br>
        </div>
        <div class="input-group">
            <label for="latitude">Latitude:</label><br>
            <input type="number" id="latitude" name="latitude" step="0.000001" value="<?php echo $latitude; ?>" required><br>
        </div>
        <label for="image">Image:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br>
        <!-- Optionally, you can display the existing image -->
        <?php if ($image_data): ?>
            <img src="<?php echo $image_data; ?>" alt="Existing Image" width="200px" height="150px"><br>
        <?php endif; ?>
        <br>
        <!-- Add a hidden input field to send the ID of the advertisement being updated -->
        <input type="hidden" name="advertisement_id" value="<?php echo $advertisement_id; ?>">
        <button type="submit" name="update_listing">Update Listing</button>
    </form>
</body>
</html>
