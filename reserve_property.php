<?php
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
if (!isset($_SESSION['landlord_id'])) {
    // If user is not logged in, redirect to the login page
    header("Location: login.html");
    exit();
}

// Function to update reservation status
function updateReservationStatus($advertisementId, $status) {
    global $connection;
    $update_sql = "UPDATE reservations SET status = '$status' WHERE advertisement_id = $advertisementId";
    return $connection->query($update_sql);
}

// Handle reservation actions
$message = ""; // Initialize message variable
if (isset($_GET['action']) && isset($_GET['advertisement_id'])) {
    $action = $_GET['action'];
    $advertisementId = $_GET['advertisement_id'];
    
    // Determine status based on action
    $status = '';
    if ($action === 'accept') {
        $status = 'accepted';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    }
    
    // Update reservation status
    if ($status !== '') {
        if (updateReservationStatus($advertisementId, $status)) {
            $message = "Advertisement has been $status.";
        } else {
            $message = "Failed to update status.";
        }
    }
}

// Retrieve reservation details from the database for the logged-in landlord
$landlord_id = $_SESSION['landlord_id'];
$reservation_sql = "SELECT reservations.student_id, reservations.advertisement_id, advertisements.title
                    FROM reservations 
                    INNER JOIN advertisements 
                    ON reservations.advertisement_id = advertisements.advertisement_id
                    WHERE advertisements.landlord_id = $landlord_id";
$reservation_result = $connection->query($reservation_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
     integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        // Function to display popup message
        function displayPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <h2>Reservation Notifications</h2>
    <?php if (!empty($message)): ?>
        <script>displayPopup("<?php echo $message; ?>");</script>
    <?php endif; ?>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Advertisement ID</th>
            <th>Property Title</th>
            <th>Action</th>
        </tr>
        <?php
        if ($reservation_result->num_rows > 0) {
            while ($row = $reservation_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['student_id'] . "</td>";
                echo "<td>" . $row['advertisement_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>";
                echo "<a href='?action=accept&advertisement_id=" . $row['advertisement_id'] . "'>Accept</a>";
                echo " | ";
                echo "<a href='?action=reject&advertisement_id=" . $row['advertisement_id'] . "'>Reject</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No reservations found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
