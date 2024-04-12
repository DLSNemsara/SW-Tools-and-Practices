<?php
// Database connection
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

/// Query to fetch property details from the advertisements table
$sql = "SELECT * FROM advertisements";
$result = $conn->query($sql);

// Create an array to store property details including longitude and latitude data
$properties = array();
$unapprovedAdvertisements = array(); // Array to store advertisements with empty status column

if ($result->num_rows > 0) {
    // Fetching data from the result set
    while($row = $result->fetch_assoc()) {
        $property = array(
            'id' => $row['advertisement_id'],
            'title' => $row['title'],
            'rent' => $row['rent'],
            'beds' => $row['beds'],
            'rooms' => $row['rooms'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'image' => $row['image_data'] // Assuming 'image_data' contains the file path
        );

        // Check if the status column is empty
        if (empty($row['status'])) {
            $unapprovedAdvertisements[] = $property;
        } else {
            $properties[] = $property;
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Display Markers on Map</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
     integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        /*** Navbar ***/
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

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        nav ul li {
            display: inline;
        }
        
        nav ul li a {
            padding: 10px 20px;
            text-decoration: none;
            color: black;
        }
        
        nav ul li a:hover {
            color: #555;
        }
        
        /* Set map height */
        #map {
            height: 400px;
            width: 50%;
            border: 3px solid #ccc; /* Add border style */
            border-radius: 8px; /* Optional: Add border radius for a rounded border */
            margin: 50px auto; /* Center the map horizontally */
        }

        /* Customize InfoWindow size */
        .custom-info-window {
            width: 300px;
            height: auto;
            max-width: 100%;
        }

        /* Adjust image size within the InfoWindow */
        .custom-info-window img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 10px;
        }
        
        /* Style for the advertisements */
        #advertisements {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns */
            gap: 20px; /* Gap between advertisements */
            margin: 20px auto;
            width: 90%; /* Adjust width as needed */
        }

        .advertisement {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
        }

        .advertisement img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
            border-radius: 10px;
        }

        .advertisement h3 {
            margin: 0;
        }

        .advertisement p {
            margin: 5px 0;
        }

    </style>
</head>
<body>
<header>

</header>

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


    <div id="map"></div>

    <div id="advertisements">
        <?php foreach ($unapprovedAdvertisements as $property): ?>
            <div class="advertisement">
                <img src="<?php echo $property['image']; ?>" alt="<?php echo $property['title']; ?>">
                <h3><?php echo $property['title']; ?></h3>
                <p>Rent: $<?php echo $property['rent']; ?>/month</p>
                <p>No. of Rooms: <?php echo $property['rooms']; ?></p>
                <p>No. of Beds: <?php echo $property['beds']; ?></p>
                <button onclick="acceptAdvertisement('<?php echo $property['id']; ?>')">Accept</button>
                <button onclick="rejectAdvertisement('<?php echo $property['id']; ?>')">Reject</button>

            </div>
        <?php endforeach; ?>
    </div>

    <script>
function initMap() {
        // Initialize map
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 9,
            center: {lat: 7.2, lng: 80.2} // Center of the map
        });

        // Combine both approved and unapproved advertisements
        var allAdvertisements = <?php echo json_encode(array_merge($properties, $unapprovedAdvertisements)); ?>;

        // Loop through all advertisements and add markers with InfoWindows
        allAdvertisements.forEach(function(property) {
            var marker = new google.maps.Marker({
                position: {lat: parseFloat(property.latitude), lng: parseFloat(property.longitude)},
                map: map,
                title: property.title
            });

            var contentString = '<div class="custom-info-window">'+
                                  '<img src="' + property.image + '" alt="' + property.title + '">' +
                                  '<h2>' + property.title + '</h2>'+
                                  '<p>Rent: $' + property.rent + '/month</p>' +
                                  '<p>No. of Rooms: ' + property.rooms + '</p>' +
                                  '<p>No. of Beds: ' + property.beds + '</p>' +
                                '</div>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function() {
                infowindow.open(map, marker);
            });
        });
    }


    function acceptAdvertisement(id) {
    // Send AJAX request to accept.php with the advertisement ID
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Handle the response if needed
            alert(this.responseText);
            // Optionally, you can update the UI after accepting the advertisement
            // For example, you can remove the corresponding advertisement from the list
            var element = document.getElementById('advertisement_' + id);
            if (element) {
                element.parentNode.removeChild(element);
            }
        }
    };
    xhttp.open("GET", "accept.php?id=" + id, true);
    xhttp.send();
}

function rejectAdvertisement(id) {
    // Send AJAX request to reject.php with the advertisement ID
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Handle the response if needed
            alert(this.responseText);
            // Optionally, you can update the UI after rejecting the advertisement
            // For example, you can remove the corresponding advertisement from the list
            var element = document.getElementById('advertisement_' + id);
            if (element) {
                element.parentNode.removeChild(element);
            }
        }
    };
    xhttp.open("GET", "reject.php?id=" + id, true);
    xhttp.send();
}


    </script>
    <!-- Load Google Maps API with your API key -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4aALIcTM4P2lgfD15h4lEjng6H-7fgKQ&callback=initMap"></script>
</body>
<footer>
    <p>&copy; Plymouth batch 11 Group AG, NSBM Green University</p>
</footer>
</html>
