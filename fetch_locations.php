<?php
// Assuming you're using MySQL and already have a connection
// Replace the database credentials and query with your own

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "accommodation";

$connection = mysqli_connect($host, $username, $password, $database);

if ($connection) {
    $query = "SELECT * FROM advertisements";
    $result = mysqli_query($connection, $query);

    $markers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $markers[] = array(
            'lat' => $row['latitude'],
            'lng' => $row['lngitude']
        );
    }

    header('Content-Type: application/json');
    echo json_encode($markers);

    mysqli_close($connection);
} else {
    echo "Failed to connect to the database.";
}
?>
