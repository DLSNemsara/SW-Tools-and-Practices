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

// Query to fetch property details from the advertisements table
$sql = "SELECT * FROM advertisements";
$result = $conn->query($sql);

// Create an array to store property details including longitude and latitude data
$properties = array();

if ($result->num_rows > 0) {
    // Fetching data from the result set
    while($row = $result->fetch_assoc()) {
        $property = array(
            'title' => $row['title'],
            'rent' => $row['rent'],
            'beds' => $row['beds'],
            'rooms' => $row['rooms'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'image' => $row['image_data'] // Assuming 'image_data' contains the file path
        );
        array_push($properties, $property);
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
        /* Set map height */
        #map {
            height: 400px;
            width: 60%;
            border: 3px solid #ccc; /* Add border style */
            border-radius: 8px; /* Optional: Add border radius for a rounded border */
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
        }
    </style>
</head>
<body>
    <h1>Markers on Map</h1>
    <div id="map"></div>

    <script>
        function initMap() {
            // Initialize map
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 9,
                center: {lat: 7.2, lng: 80.2} // Center of the map
            });

            // Loop through properties and add markers with InfoWindows
            <?php foreach ($properties as $property): ?>
                // Create a function closure to encapsulate the marker and infowindow creation
                (function(property) {
                    var marker = new google.maps.Marker({
                        position: {lat: <?php echo $property['latitude']; ?>, lng: <?php echo $property['longitude']; ?>},
                        map: map,
                        title: '<?php echo $property['title']; ?>'
                    });

                    var contentString = '<div class="custom-info-window">'+
                                          '<h2><?php echo $property['title']; ?></h2>'+
                                          '<img src="<?php echo $property['image']; ?>" alt="<?php echo $property['title']; ?>">' +
                                          '<p>Rent: $<?php echo $property['rent']; ?>/month</p>' +
                                          '<p>No. of Rooms: <?php echo $property['rooms']; ?></p>' +
                                          '<p>No. of Beds: <?php echo $property['beds']; ?></p>' +
                                        '</div>';

                    var infowindow = new google.maps.InfoWindow({
                        content: contentString
                    });

                    marker.addListener('click', function() {
                        infowindow.open(map, marker);
                    });
                })(<?php echo json_encode($property); ?>); // Pass property data as argument to the closure
            <?php endforeach; ?>
        }
    </script>
    <!-- Load Google Maps API with your API key -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4aALIcTM4P2lgfD15h4lEjng6H-7fgKQ&callback=initMap"></script>
</body>
</html>
