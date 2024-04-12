<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if advertisement_id is set and not empty
    if (isset($_POST["advertisement_id"]) && !empty($_POST["advertisement_id"])) {
        // Retrieve the advertisement_id
        $advertisement_id = $_POST["advertisement_id"];

        // Connect to the database
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $dbname = "accommodationfinder";

        // Create connection
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Perform further actions, such as deleting the listing from the database
        $sql = "DELETE FROM advertisements WHERE advertisement_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $advertisement_id);
        $stmt->execute();

        // Close the statement
        $stmt->close();

        // Close the database connection
        $conn->close();

        // Display success message
        echo "Advertisement deleted successfully.";

        // After deleting, you can redirect to a success page or back to the main page
        header("Location: show_listings.php"); // Redirect to the main page
        exit; // Stop further execution
    } else {
        // If advertisement_id is not set or empty, handle the error or redirect as needed
        echo "Advertisement ID is missing.";
    }
} else {
    // If the form is not submitted via POST method, handle the error or redirect as needed
    echo "Invalid request method.";
}
?>
