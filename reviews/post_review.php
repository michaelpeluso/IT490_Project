<?php

// Database connection settings
$servername = $_ENV["DB_SERVERNAME"];
$username = $_ENV["DB_USERNAME"];
$password = $_ENV["DB_PASSWORD"];
$dbname = "reviews";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID (assuming you have a logged-in user)
$user_id = 1; // Replace with the actual user ID

// Retrieve the restaurant data from the AJAX request
$service_id = $_POST['service_id'];
$service_type = $_POST['service_type'];
$review_rating = $_POST['review_rating'];
$review_body = $_POST['review_body'];
$review_date = $_POST['review_date'];

// sql query
$query = "INSERT INTO reviews (user_id, service_id, service_type, review_rating, review_body, review_date) 
    VALUES (".$user_id.", ".$service_id.", ".$service_type.", ".$review_rating.", ".$review_body.", ".$review_date.")";

// execute query
$stmt = $conn->prepare($query);
$stmt->bind_param("issd", $user_id, $service_id, $service_type, $review_rating, $review_body, $review_date); // prevents sql injection
$stmt->execute();

// error handling
echo $result ? "Review inserted successfully." : "There was an error inserting your review into our database.";

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
