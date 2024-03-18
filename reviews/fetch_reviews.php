<?php
// Database connection settings
$servername = $_ENV["DB_SERVERNAME"];
$username = $_ENV["DB_USERNAME"];
$password = $_ENV["DB_PASSWORD"];
$dbname = "trips";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the restaurant data from the AJAX request
$service_id = $_POST['service_id'];
$review_rating = $_POST['review_rating'];
$review_body = $_POST['review_body'];
$review_title = $_POST['review_date'];

// Get the user ID (assuming you have a logged-in user)
$user_id = 1; // Replace with the actual user ID

// Prepare and execute the SQL query to insert the restaurant data into the database
$stmt = $conn->prepare("INSERT INTO reviews (user_id, service_id, service_type, review_rating, review_body, review_date) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issd", $user_id, $name, $address, $rating);
$stmt->execute();

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
