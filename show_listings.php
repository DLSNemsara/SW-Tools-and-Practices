<?php
session_start(); // Start session if not already started

$servername = "localhost";
$username = "root";
$password = "";
$database = "accommodationfinder";

// Create connection
$connection = new mysqli($servername, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard</title>
    <link rel="stylesheet" href="css/show_listings.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
     integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
     body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('uploads/background.jpg');
            background-repeat: no-repeat;
            background-size: cover;
        }
        .navbar-brand img {
        max-width: 200px; /* Adjust the width as needed */
        max-height: 50px; /* Adjust the height as needed */
        }
        footer {
            background-color: #333; /* Background color */
            color: white; /* Text color */
            padding: 5px; /* Adjust padding as needed */
            text-align: center; /* Align text to center */
            margin-top: 365px;
        }
    </style>
</head>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php"> <img src="uploads/logo.jpg" alt="Logo"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
        <a class="nav-item nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a>
        <a class="nav-item nav-link" href="login.html">Login</a>
        <a class="nav-item nav-link" href="landlord_prop.html">Landlord</a>
        <a class="nav-item nav-link" href="warden.php">Warden</a>
        <a class="nav-item nav-link" href="admin.html">Admin</a>
        <a class="nav-item nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<div class="row">
    <?php
    // Check if user is logged in
    if (isset($_SESSION["landlord_id"])) {
        $landlord_id = $_SESSION["landlord_id"];

        // Retrieve listings from the database for the logged-in user
        $sql = "SELECT * FROM advertisements WHERE landlord_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $landlord_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="listing" style="margin: 30px;">';
                // Construct the image source URL based on the image file name
                $image_src = $row['image_data']; // Assuming 'image_data' contains the file name
                echo '<img src="' . $image_src . '" alt="' . $row['title'] . '">';
                echo '<h4>' . $row['title'] . '</h4>';
                echo '<p>Rent: Rs.' . $row['rent'] . '/month</p>';
                echo '<p>No. of Rooms: ' . $row['rooms'] . '</p>';
                echo '<p>No. of Beds: ' . $row['beds'] . '</p>';
                // Hidden input field to store advertisement_id
                echo '<form action="update_listing_form.php" method="post">';
                echo '<input type="hidden" name="advertisement_id" value="' . $row['advertisement_id'] . '">';
                echo '<button type="submit" name="update">Update</button>';
                echo '</form>';
                echo '<form action="delete_listing.php" method="post">';
                echo '<input type="hidden" name="advertisement_id" value="' . $row['advertisement_id'] . '">';
                echo '<button type="submit" name="delete">Delete</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "No listings available.";
        }

        // Close statement
        $stmt->close();
    } else {
        // Redirect to login page or handle unauthorized access
        header("Location: login.html");
        exit();
    }
    ?>
</div>
<footer>
    <p>&copy; Plymouth batch 11 Group AG, NSBM Green University</p>
</footer>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>
