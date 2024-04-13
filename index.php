<?php
// Start or resume session
session_start();

// Establish a connection to the database
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

// Check if user is logged in
$student_id = null;
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
}

// Reservation logic
$reservationMessage = ""; // Initialize reservation message
if (isset($_POST['reserve'])) {
    if (!$student_id) {
        $reservationMessage = "Please login to reserve a property.";
    } else {
        $advertisement_id = $_POST['advertisement_id'];
        
        // Check if the user has already reserved this property
        $check_reservation_sql = "SELECT * FROM reservations WHERE student_id = '$student_id' AND advertisement_id = '$advertisement_id'";
        $check_reservation_result = $connection->query($check_reservation_sql);
        
        if ($check_reservation_result->num_rows > 0) {
            $reservationMessage = "You have already reserved this property. Please wait for the landlord's response.";
        } else {
            // Insert reservation into the database
            $reserve_sql = "INSERT INTO reservations (student_id, advertisement_id) VALUES ('$student_id', '$advertisement_id')";
            
            if ($connection->query($reserve_sql) === TRUE) {
                $reservationMessage = "Reservation successful!";
                // Execute JavaScript to display the message box
                echo '<script>alert("' . $reservationMessage . '");</script>';
            } else {
                $reservationMessage = "Error: " . $reserve_sql . "<br>" . $connection->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodation Website</title>
    <link rel="stylesheet" href="css/styles.css">
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
        footer {
            background-color: #333; /* Background color */
            color: white; /* Text color */
            padding: 5px; /* Adjust padding as needed */
            text-align: center; /* Align text to center */
        }
        .navbar-brand img {
        max-width: 200px; /* Adjust the width as needed */
        max-height: 50px; /* Adjust the height as needed */
        }
     </style>
</head>
<body>
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
    
    <main>
        <h3>Latest Listings</h3>
        <div class="row">
            <?php
            // Assuming you have a database connection established
            $sql = "SELECT * FROM advertisements WHERE status = 'accepted'";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="listing">';
                    $image_src = $row['image_data']; // Assuming 'image_data' contains the file name
                    echo '<img src="' . $image_src . '" alt="' . $row['title'] . '">';
                    echo '<h4>'.$row['title'].'</h4>';
                    echo '<p>Rent: Rs.'.$row['rent'].'/month</p>';
                    echo '<p>No. of Rooms: '.$row['rooms'].'</p>';
                    echo '<p>No. of Beds: '.$row['beds'].'</p>';
                    if ($student_id) {
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="student_id" value="'.$student_id.'">';
                        echo '<input type="hidden" name="advertisement_id" value="'.$row['advertisement_id'].'">';
                        echo '<input type="hidden" name="title" value="'.$row['title'].'">'; // Add title field
                        echo '<button type="submit" name="reserve">Reserve Property</button>';
                        echo '</form>';
                    } else {
                        echo '<br><p><a href="login.html">Login</a> to reserve this property.</p>';
                    }
                    echo '</div>';
                }
            } else {
                echo "No listings available.";
            }
            ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; Plymouth batch 11 Group AG, NSBM Green University</p>
    </footer>
</body>
</html>
<?php
// Close the database connection
$connection->close();
?>
