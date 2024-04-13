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
if (!isset($_SESSION['student_id'])) {
    // If user is not logged in, redirect to the login page
    header("Location: login.html");
    exit();
}

// Retrieve student_id from session
$student_id = $_SESSION['student_id'];

// Retrieve reservation details from the database for the logged-in student
$reservation_sql = "SELECT reservations.student_id, reservations.advertisement_id, advertisements.title, reservations.status
                    FROM reservations 
                    INNER JOIN advertisements 
                    ON reservations.advertisement_id = advertisements.advertisement_id
                    WHERE reservations.student_id = $student_id";
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
</head>
<body>
    <h2>Reservation Notifications</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Advertisement ID</th>
            <th>Property Title</th>
            <th>Status</th>
        </tr>
        <?php
        if ($reservation_result->num_rows > 0) {
            while ($row = $reservation_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['student_id'] . "</td>";
                echo "<td>" . $row['advertisement_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No reservations found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
