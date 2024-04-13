<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $category = $_POST["category"];

    // Validate inputs (you can add more specific validation if needed)
    if (empty($username) || empty($email) || empty($password) || empty($category)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Database connection details
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

        // Prepare SQL statement to prevent SQL injection
        $sql = $conn->prepare("INSERT INTO " . $category . " (username, email, password) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $username, $email, $password);

        // Execute SQL statement
        if ($sql->execute()) {
            $message = "New user registered successfully.";
            echo "<script>alert('$message'); window.location.href = 'admin.html';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close connection
        $conn->close();
    }
}
?>
