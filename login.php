<?php
session_start(); // Start session if not already started

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category'])) {
    // Retrieve form data
    $category = $_POST["category"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate inputs
    if (empty($category) || empty($username) || empty($password)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Connect to your database (replace with your own database credentials)
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

        // Prepare and execute SQL query to fetch user from database
        $sql = "SELECT * FROM $category WHERE username=? AND password=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Fetch user details
            $row = $result->fetch_assoc();
            $user_id = $row[$category . '_id']; // Adjust this according to your table structure

            // Store username and user_id in session
            $_SESSION["username"] = $username;
            $_SESSION[$category . "_id"] = $user_id;

            // Redirect based on user category
            switch ($category) {
                case 'student':
                    // Redirect to student page
                    header("Location: index.php");
                    exit();
                    break;
                case 'warden':
                    // Redirect to warden page
                    header("Location: warden.php");
                    exit();
                    break;
                case 'landlord':
                    // Redirect to landlord page
                    header("Location: landlord_prop.html");
                    exit();
                    break;
                default:
                    // Handle error or invalid category
                    break;
            }
        } else {
            echo "<script>alert('Invalid username or password.');</script>";
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>
